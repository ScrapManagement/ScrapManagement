<?php

namespace App\Http\Controllers\Api\Auction;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Auction\AuctionRequest;
use App\Http\Requests\DashBoard\Auction\UpdateAuctionRequest;
use App\Http\Resources\AuctionResource;
use App\Http\Resources\ProductResource;
use App\Models\Auction\Auction;
use App\Models\Payment\ProductUnlock;
use App\Models\Product\Product;
use App\Services\Auctions\AuctionService;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function __construct(private AuctionService $auctionService) {}

    public function index()
    {
        $auctions = Auction::with([
            'product.images',
            'product.seller',
            'product.category'
        ])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'  => 'true',
            'message' => 'All Auctions retrieved successfully for Admin.',
            'data'    => AuctionResource::collection($auctions),
        ], 200);
    }

    public function show(Auction $auction)
    {
        $auction->load([
            'product.images',
            'product.seller',
            'product.category',
            'bids' => fn($q) => $q->orderByDesc('amount')->limit(10),
            'bids.user:id,name',
        ]);

        return response()->json([
            'status'  => 'true',
            'message' => 'Auction details retrieved successfully.',
            'data'    => new AuctionResource($auction),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────
    // 2. تفاعلات المزايدين (Buyer Actions)
    // ─────────────────────────────────────────────────────────────────

    public function join(Request $request, Auction $auction)
    {
        $data = $request->validate([
            'inspection_type' => 'sometimes|in:online,offline',
        ]);

        try {

            $paymentData = $this->auctionService->initiateInsurancePayment($auction, auth()->user(), $data);

            return response()->json([
                'status'  => 'true',
                'message' => 'Payment initiated successfully.',
                'payment_url' => $paymentData['url'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 422);
        }
    }

    public function bid(Request $request, Auction $auction)
    {
        $request->validate(['amount' => 'required|numeric|min:0.01']);

        try {
            $bid = $this->auctionService->placeBid($auction, auth()->id(), $request->amount);

            return response()->json([
                'message'       => 'Bid placed successfully.',
                'bid'           => [
                    'id'        => $bid->id,
                    'amount'    => $bid->amount,
                    'placed_at' => $bid->created_at->toIso8601String(),
                ],
                'current_price' => $auction->fresh()->current_price,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 422);
        }
    }

    public function bids(Auction $auction)
    {
        $bids = $auction->bids()
            ->with('user:id,name')
            ->orderByDesc('amount')
            ->paginate(20);

        return response()->json($bids);
    }

    public function myAuctionProducts()
    {
        $products = auth('api')->user()->products()
            ->where('sale_type', 'auction')
            ->with(['category', 'seller', 'images', 'auction'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'  => 'true',
            'message' => 'My auction products retrieved successfully.',
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    public function myWonAuctions()
    {
        $user = auth('api')->user();

        $auctions = Auction::where('status', 'ended')
            ->whereHas('bids', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->whereRaw('amount = (select max(amount) from auction_bids where auction_id = auctions.id)');
            })
            ->with(['product.images', 'product.seller', 'product.category'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'  => 'true',
            'message' => 'My won auctions retrieved successfully.',
            'data'    => AuctionResource::collection($auctions),
        ], 200);
    }

    // ─────────────────────────────────────────────────────────────────
    // 3. لوحة التحكم (Admin Actions)
    // ─────────────────────────────────────────────────────────────────

    public function store(AuctionRequest $request, Product $product)
    {
        if ($product->auction()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'This product already has an auction.',
            ], 422);
        }

        $auction = $this->auctionService->createAuction($product, $request->validated());

        return response()->json([
            'status'  => 'true',
            'message' => 'Auction created successfully.',
            'data'    => new AuctionResource($auction->load('product'))
        ], 201);
    }

    public function update(UpdateAuctionRequest $request, Auction $auction)
    {
        try {
            $updatedAuction = $this->auctionService->updateAuction($auction, $request->validated());

            return response()->json([
                'status'  => 'true',
                'message' => 'Auction updated successfully.',
                'data'    => new AuctionResource($updatedAuction->load('product')),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'false',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(Auction $auction)
    {
        try {
            $this->auctionService->deleteAuction($auction);

            return response()->json([
                'status'  => 'true',
                'message' => 'Auction deleted successfully and product reset.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'false',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function activate(Auction $auction)
    {
        if ($auction->status !== 'scheduled') {
            return response()->json(['status' => 'false', 'message' => 'Auction must be scheduled.'], 422);
        }

        $auction->update(['status' => 'active']);

        return response()->json([
            'status'  => 'true',
            'message' => 'Auction activated.',
            'data'    => new AuctionResource($auction)
        ], 200);
    }

    public function end(Auction $auction)
    {
        try {
            $this->auctionService->endAuction($auction);
            return response()->json([
                'status'  => 'true',
                'message' => 'Auction ended and results processed.',
                'data'    => new AuctionResource($auction->fresh())
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 422);
        }
    }

    public function markWinnerNotSerious(Auction $auction)
    {
        try {
            $this->auctionService->markWinnerAsNotSerious($auction);
            return response()->json([
                'status'  => 'true',
                'message' => 'Winner marked as not serious, insurance forfeited.',
                'data'    => new AuctionResource($auction->fresh())
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 422);
        }
    }

    public function adminShow(Auction $auction)
    {
        $auction->load([
            'product.images',
            'product.seller',
            'product.category',
            'bids' => fn($q) => $q->orderByDesc('amount'),
            'bids.user:id,name,phone',
            'participants.user:id,name,phone',
        ]);

        return response()->json([
            'status'  => 'true',
            'message' => 'Auction details and full bid history retrieved successfully.',
            'data'    => new AuctionResource($auction),
        ], 200);
    }
}
