<?php

use App\Models\Auction\Auction;
use App\Services\Auctions\AuctionService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Auction::where('status', 'scheduled')
        ->where('starts_at', '<=', now())
        ->update(['status' => 'active']);
})->everyMinute();

// ٢. إنهاء المزادات المنتهية تلقائياً
Schedule::call(function () {
    $service = app(AuctionService::class);

    Auction::where('status', 'active')
        ->where('ends_at', '<=', now())
        ->each(function ($auction) use ($service) {
            $service->endAuction($auction);
        });
})->everyMinute();
