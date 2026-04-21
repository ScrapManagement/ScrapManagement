<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $products = $user->favoriteProducts()
            ->with(['images', 'category' , 'seller'])
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Favorite products retrieved successfully',
            'data' => ProductResource::collection($products)
        ]);
    }

    public function add($productId)
    {
        $user = auth()->user();

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $user->favoriteProducts()->syncWithoutDetaching([$productId]);

        return response()->json([
            'status' => true,
            'message' => 'Product added to favorites'
        ]);
    }

    public function remove($productId)
    {
        $user = auth()->user();

        $user->favoriteProducts()->detach($productId);

        return response()->json([
            'status' => true,
            'message' => 'Product removed from favorites'
        ]);
    }


}
