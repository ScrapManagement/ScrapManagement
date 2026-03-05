<?php

namespace App\Http\Resources;

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
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'unit'        => $this->unit,
            'price'       => $this->price,
            'status'      => $this->status,
            'material_priority' => $this->material_priority,
            'category'    => $this->whenLoaded('category', fn() => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ]),
            'seller'      => $this->whenLoaded('seller', fn() => [
                'id'   => $this->seller->id,
                'name' => $this->seller->name,
            ]),
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
