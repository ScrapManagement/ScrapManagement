<?php

namespace App\Events\Auctions;



use App\Models\Auction\AuctionBid;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewBidPlaced implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public AuctionBid $bid) {}


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('auction.' . $this->bid->auction_id),
        ];
    }


    public function broadcastWith(): array
    {
        return [
            'bid_id'      => $this->bid->id,
            'auction_id'  => $this->bid->auction_id,
            'amount'      => $this->bid->amount,
            'user'        => [
                'id'   => $this->bid->user->id,
                'name' => $this->bid->user->name,
            ],
            'placed_at'   => $this->bid->created_at->toIso8601String(),
        ];
    }

   
    public function broadcastAs(): string
    {
        return 'bid.placed';
    }
}
