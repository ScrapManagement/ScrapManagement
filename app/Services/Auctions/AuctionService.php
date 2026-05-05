<?php

namespace App\Services\Auctions;


use App\Events\Auctions\NewBidPlaced;
use App\Jobs\Auctions\RefundParticipantJob;
use App\Models\Auction\Auction;
use App\Models\Auction\AuctionBid;
use App\Models\Auction\AuctionParticipant;
use App\Models\Payment\Payment;
use App\Models\Payment\ProductUnlock;
use App\Models\Product\Product;
use App\Services\Notification\WhatsAppService;
use App\Services\Payment\InsurancePaymentService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionService
{
    public function __construct(
        private InsurancePaymentService $insurancePayment,
        private WhatsAppService $whatsappService
    ) {}

    public function createAuction(Product $product, array $data): Auction
    {
        $seller = $product->seller;

        if (!$seller) {
            throw new \Exception('This product does not have an owner (User).');
        }

        if ($product->status !== 'approved') {
            throw new Exception('The product must be approved before creating an auction.');
        }

        if ($seller->id_card_status !== 'approved') {
            throw new Exception('The seller must have an approved ID card to create an auction.');
        }

        return DB::transaction(function () use ($product, $data) {
            $product->update(['sale_type' => 'auction']);

            return Auction::create([
                'product_id'     => $product->id,
                'status'         => 'scheduled',
                'starting_price' => $data['starting_price'] ?? $product->price,
                'current_price'  => $data['starting_price'] ?? $product->price,
                'insurance_rate' => $data['insurance_rate'],
                'starts_at'      => $data['starts_at'],
                'ends_at'        => $data['ends_at'],
            ]);
        });
    }

    public function updateAuction(Auction $auction, array $data): Auction
    {
        if ($auction->status !== 'scheduled') {
            throw new \Exception('Only scheduled auctions can be updated.');
        }

        return DB::transaction(function () use ($auction, $data) {
            if (isset($data['starting_price'])) {
                $data['current_price'] = $data['starting_price'];
            }

            $auction->update($data);

            return $auction;
        });
    }

    public function deleteAuction(Auction $auction): bool
    {
        if ($auction->status == 'active') {
            throw new \Exception('Cannot delete an auction that has already started or ended.');
        }

        return DB::transaction(function () use ($auction) {
            $auction->product->update(['sale_type' => null]);

            return $auction->delete();
        });
    }



    public function initiateInsurancePayment(Auction $auction, $user, array $data): array
    {
        if ($user->account_type !== 'auction') {
            throw new Exception('You must verify your account as an auction account (upload ID card) to participate.');
        }

        if ($auction->product->user_id === $user->id) {
            throw new Exception('You cannot participate in your own auction.');
        }

        if (!$auction->isActive()) {
            throw new Exception('The auction is not active right now.');
        }

        if ($auction->hasParticipant($user->id)) {
            throw new Exception('You have already participated in this auction.');
        }

        $hasUnlocked = ProductUnlock::where([
            'user_id'    => $user->id,
            'product_id' => $auction->product_id,
        ])->exists();

        if (!$hasUnlocked) {
            throw new Exception('You must unlock the product with coins first.');
        }

        return $this->insurancePayment->sendPayment($user, $auction, $data);
    }




    public function placeBid(Auction $auction, int $userId, float $amount): AuctionBid
    {
        if ($auction->product->user_id === $userId) {
            throw new Exception('As the owner, you are not allowed to bid on this product.');
        }

        if (!$auction->isActive()) {
            throw new Exception('The auction is not active right now.');
        }

        if (!$auction->hasParticipant($userId)) {
            throw new Exception('You must pay the insurance first to participate.');
        }

        if ($amount <= $auction->current_price) {
            throw new Exception('Your bid must be higher than the current price: ' . $auction->current_price);
        }

        return DB::transaction(function () use ($auction, $userId, $amount) {
            $bid = AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id'    => $userId,
                'amount'     => $amount,
            ]);

            $auction->update(['current_price' => $amount]);

            broadcast(new NewBidPlaced($bid->load('user')))->toOthers();

            return $bid;
        });
    }


    public function endAuction(Auction $auction): void
    {
        if ($auction->status !== 'active') {
            throw new Exception('The auction is not active.');
        }

        DB::transaction(function () use ($auction) {
            $highestBid = $auction->highestBid();

            if (!$highestBid) {
                $auction->update(['status' => 'cancelled']);
                $this->dispatchRefundsForAll($auction);
                return;
            }

            $winnerId = $highestBid->user_id;
            $auction->update([
                'status' => 'ended',
                'winner_id' => $winnerId
            ]);

            foreach ($auction->participants as $participant) {
                if ($participant->user_id === $winnerId) {
                    $participant->deductFromFinalPrice();
                } else {
                    RefundParticipantJob::dispatch($participant);
                }
            }
        });
    }


    public function markWinnerAsNotSerious(Auction $auction): void
    {
        if ($auction->status !== 'ended' || !$auction->winner_id) {
            throw new Exception('The auction has not ended or does not have a winner.');
        }

        DB::transaction(function () use ($auction) {
            $auction->participants()
                ->where('user_id', $auction->winner_id)
                ->firstOrFail()
                ->forfeitInsurance();
        });
    }

    // ──────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────

    public function refundParticipant(AuctionParticipant $participant): void
    {
        $payment = Payment::where('auction_participant_id', $participant->id)
            ->where('status', 'paid')
            ->first();

        if ($payment) {
            try {
                $response = $this->insurancePayment->refund($payment->transaction_id, $payment->amount);

                if ($response) {
                    $payment->update(['status' => 'refunded']);
                    Log::info("Refund success for Order: {$payment->order_id}");
                    $this->notifyUserViaWhatsApp($participant, $payment->amount, (string) $payment->order_id);
                } else {
                    throw new Exception("Paymob refund failed for Order: {$payment->order_id}");
                }
            } catch (Exception $e) {
                Log::error("Refund Error: " . $e->getMessage());
                throw $e;
            }
        }

        $participant->refundInsurance();
    }

    private function dispatchRefundsForAll(Auction $auction): void
    {
        foreach ($auction->participants as $participant) {
            RefundParticipantJob::dispatch($participant);
        }
    }

    private function notifyUserViaWhatsApp(AuctionParticipant $participant, float $amount, string $orderId): void
    {
        try {
            $user = $participant->user;
            $productName = $participant->auction->product->name ?? 'the auction';

            $message = "Hello {$user->name},\n\n";
            $message .= "Your insurance deposit of ({$amount} EGP) for [{$productName}] has been successfully refunded.\n";
            $message .= "Reference Transaction ID: ";

            $this->whatsappService->sendMessage($user->phone, $message, $orderId);

        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp refund notification to User {$participant->user_id}: " . $e->getMessage());
        }
    }
}
