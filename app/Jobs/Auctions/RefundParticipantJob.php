<?php

namespace App\Jobs\Auctions;

use App\Models\Auction\AuctionParticipant;
use App\Services\Auctions\AuctionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class RefundParticipantJob implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public function __construct(public AuctionParticipant $participant) {}

    public function handle(): void
    {

        $auctionService = app(AuctionService::class);

        try {
            $auctionService->refundParticipant($this->participant);
            Log::info("Refund Job processed for Participant: {$this->participant->id}");
        } catch (\Exception $e) {
            Log::error("Refund Job failed: " . $e->getMessage());
            throw $e;
        }
    }
}
