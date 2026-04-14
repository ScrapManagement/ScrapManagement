<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'email'       => $this->email,
            'phone'       => $this->phone,
            'city'        => $this->city,
            'region'      => $this->region,
            'address'     => $this->address,
            'coin'        => $this->coins,
            'account_type' => $this->account_type,
            'id_card'    => [
                'status'      => $this->id_card_status,
                'front_image' => $this->when($this->id_card_front, asset('storage/' . $this->id_card_front)),
                'back_image'  => $this->when($this->id_card_back, asset('storage/' . $this->id_card_back)),
                'verified_at' => $this->id_card_verified_at?->format('Y-m-d H:i:s'),
            ],
            'category'    => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),
            'deleted_at'  => $this->deleted_at?->format('Y-m-d H:i:s'),
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
