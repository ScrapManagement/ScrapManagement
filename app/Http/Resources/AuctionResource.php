<?php

namespace App\Http\Resources;

use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuctionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = auth()->id();

        return [
            'id'               => $this->id,
            'starting_price'   => $this->starting_price,
            'current_price'    => $this->current_price,
            'insurance_rate'   => $this->insurance_rate,
            'insurance_amount' => $this->calculateInsurance(),
            'is_participant'   => $this->when($userId, fn() => $this->hasParticipant($userId)),
            'my_highest_bid'   => $this->when($userId, function () use ($userId) {
                return $this->bids->where('user_id', $userId)->max('amount');
            }),
            'status'           => $this->status,
            'starts_at'        => $this->starts_at->format('Y-m-d H:i:s'),
            'ends_at'          => $this->ends_at->format('Y-m-d H:i:s'),

            'winner'           => $this->when($this->status === 'ended', function () {
                $winningBid = $this->bids()->orderByDesc('amount')->first();

                if ($winningBid) {
                    return [
                        'user_id'    => $winningBid->user->id,
                        'user_name'  => $winningBid->user->name,
                        'win_amount' => $winningBid->amount,
                    ];
                }

                return null;
            }),
            'bids'             => $this->whenLoaded('bids', function () {
                return $this->bids->map(function ($bid) {
                    return [
                        'id'         => $bid->id,
                        'user_id'    => $bid->user_id,
                        'amount'     => $bid->amount,
                        'user_name'  => $bid->user->name ?? 'Unknown',
                        'created_at' => $bid->created_at->diffForHumans(),
                    ];
                });
            }),
            'product' => new ProductResource($this->whenLoaded('product')),
            'created_at'       => $this->created_at->diffForHumans(),
        ];
    }
}
