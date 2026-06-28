<?php

namespace App\Console\Commands;

use App\Models\Auction\Auction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StartScheduledAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:start-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically start auctions when their starts_at time arrives';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $scheduledAuctions = Auction::where('status', 'scheduled')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->get();

        $count = 0;

        foreach ($scheduledAuctions as $auction) {
            try {
                $auction->update(['status' => 'active']);
                $count++;
            } catch (\Exception $e) {
                Log::error("Failed to auto-start auction ID {$auction->id}: " . $e->getMessage());
            }
        }

        $this->info("Successfully started {$count} scheduled auctions.");
    }
}
