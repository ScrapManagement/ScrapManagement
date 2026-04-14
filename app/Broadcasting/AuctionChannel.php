<?php

namespace App\Broadcasting;

use App\Models\Auction\Auction;
use App\Models\User\User;



class AuctionChannel
{
    /**
     * الـ method دي بتتحقق إن اليوزر ده يقدر يسمع على الـ channel
     * بيتاخد من routes/channels.php
     *
     * الشرط: اليوزر لازم يكون دفع التأمين (موجود في auction_participants)
     */
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
