<?php

use App\Broadcasting\AuctionChannel;
use App\Models\Auction\Auction;
use Illuminate\Support\Facades\Broadcast;

/* Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
 */
//Broadcast::channel('auction.{auctionId}', AuctionChannel::class);
Broadcast::channel('auctions.{auctionId}', function ($user, $auctionId) {
    return Auction::find($auctionId)->hasParticipant($user->id);
});
