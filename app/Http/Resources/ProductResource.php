<?php

namespace App\Http\Resources;

use App\Services\Payment\CoinService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray(Request $request): array
    {
        $coinService = app(CoinService::class);
        $userId = auth('api')->id();
        $isOwner = ($this->user_id === $userId);
        $isUnlocked = $isOwner || $this->isUnlockedBy($userId);

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'unit'        => $this->unit,
            'price'       => $this->price,
            'unlock_cost'       => $coinService->calculateUnlockCost($this->resource),
            'is_unlocked'       => $isUnlocked,
            'unlocks_count' => $this->whenCounted('productUnlocks'),
            'status'      => $this->status,
            'sale_type'   => $this->sale_type,
            'material_priority' => $this->material_priority,
            'category'    => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),

            'auction'           => $this->when(
                $this->sale_type === 'auction',
                fn() => new AuctionResource($this->whenLoaded('auction'))
            ),

            'seller'            => $this->whenLoaded('seller', function () use ($isUnlocked) {

                $data = [
                    'id'     => $this->seller->id,
                    'name'   => $this->seller->name,
                    'city'   => $this->seller->city,
                    'region' => $this->seller->region,
                ];

                if ($isUnlocked) {
                    $data['phone']   = $this->seller->phone;
                    $data['email']   = $this->seller->email;
                    $data['address'] = $this->seller->address;
                }

                return $data;
            }),

            'images'      => $this->whenLoaded(
                'images',
                fn() =>
                $this->images->map(fn($image) => [
                    'id'    => $image->id,
                    'url'   => asset('storage/' . $image->image_path),
                ])
            ),
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
            'deleted_at'  => $this->deleted_at?->format('Y-m-d H:i:s'),
        ];
    }
}
