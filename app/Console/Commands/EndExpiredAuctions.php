<?php

namespace App\Console\Commands;

use App\Models\Auction\Auction;
use App\Services\Auctions\AuctionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EndExpiredAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:end-expired';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically end auctions that have passed their ends_at time';
    /**
     * Execute the console command.
     */
    public function handle(AuctionService $auctionService)
    {
        $expiredAuctions = Auction::where('status', 'active')
            ->where('ends_at', '<=', now())
            ->get();

        $count = 0;

        foreach ($expiredAuctions as $auction) {
            try {
                $auctionService->endAuction($auction);
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-end auction ID {$auction->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully ended {$count} expired auctions.");
    }
}
