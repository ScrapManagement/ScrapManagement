<?php

namespace App\Broadcasting;

use App\Models\Auction\Auction;
use App\Models\User\User;



class AuctionChannel
{

    public function join(User $user, int $auctionId): bool
    {
        $auction = Auction::find($auctionId);

        if (!$auction) {
            return false;
        }

        return $auction->participants()
            ->where('user_id', $user->id)
            ->where('insurance_status', 'held')
            ->exists();
    }
}
