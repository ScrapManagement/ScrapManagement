<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Product\ProductRequest;
use App\Http\Requests\DashBoard\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product\Image;
use App\Models\Product\Product;
use App\Services\Payment\CoinService;
use App\Services\Product\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'images', 'seller', 'auction'])
            ->withCount('productUnlocks')
            ->latest()
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Products retrieved successfully',
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $user = auth()->user();

        $saleType = 'coins';

        if ($request->sale_type === 'auction') {
            if ($user->account_type === 'auction') {
                $saleType = 'auction';
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => 'Your account is not eligible to create auction products. Please verify your ID card to access this feature.',
                ], 403);
            }
        }
        $product = Product::create([
            'user_id'     => $user->id,
            'category_id' => $request->category_id,
            'sale_type'   => $saleType,
            'name'        => $request->name,
            'description' => $request->description,
            'quantity'    => $request->quantity,
            'unit'        => $request->unit,
            'price'       => $request->price,
            'status'      => 'pending',
        ]);

        if ($request->hasFile('images')) {
            $paths = ImageService::saveImages($request->file('images'));

            foreach ($paths as $path) {
                Image::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Product created successfully',
            'data'    => new ProductResource($product->load(['category', 'images'])),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'images', 'seller', 'auction'])
            ->withCount('productUnlocks')
            ->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Product retrieved successfully',
            'data'    => new ProductResource($product),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::with(['category', 'images', 'seller', 'auction'])->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        if ($product->status === 'approved') {
            return response()->json([
                'status'  => false,
                'message' => 'Approved products cannot be updated. Please contact support if you need to make changes.',
            ], 403);
        }

        $product->update($request->only([
            'name',
            'description',
            'quantity',
            'unit',
            'price',
            'category_id',
        ]));

        if ($request->hasFile('images')) {
            ImageService::deleteImages($product->images);
            Image::where('product_id', $product->id)->delete();

            $paths = ImageService::saveImages($request->file('images'));
            foreach ($paths as $path) {
                Image::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Product updated successfully',
            'data'    => new ProductResource($product->load(['category', 'images', 'seller'])),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */

    public function softDelete(string $id)
    {
        $product = Product::with('images')->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        if ($product->status === 'approved') {
            return response()->json([
                'status'  => false,
                'message' => 'Approved products cannot be updated. Please contact support if you need to make changes.',
            ], 403);
        }

        $product->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Product soft deleted successfully',
        ], 200);
    }


    public function forceDelete(string $id)
    {
        $product = Product::with('images')->withTrashed()->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        ImageService::deleteImages($product->images);
        Image::where('product_id', $product->id)->forceDelete();
        $product->forceDelete();

        return response()->json([
            'status'  => true,
            'message' => 'Product permanently deleted successfully',
        ], 200);
    }


    public function trashed()
    {
        $products = Product::with(['category', 'images'])->onlyTrashed()->latest()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Trashed products retrieved successfully',
            'data'    => ProductResource::collection($products),
        ], 200);
    }


    public function restore(string $id)
    {
        $product = Product::withTrashed()->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        if (!$product->trashed()) {
            return response()->json([
                'status'  => false,
                'message' => 'Product is not deleted',
            ], 400);
        }

        $product->restore();

        return response()->json([
            'status'  => true,
            'message' => 'Product restored successfully',
            'data'    => new ProductResource($product->load(['category', 'images'])),
        ], 200);
    }

    /**
     * Change the status of the specified product (Admin only).
     */
    public function changeStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->update([
            'status'      => $request->status,
            'reviewed_by' => auth('admin-api')->id(),
        ]);

        $message = $request->status === 'approved'
            ? 'Product has been approved successfully!'
            : 'Product has been rejected.';

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => new ProductResource($product->load(['category', 'images'])),
        ], 200);
    }

    public function getUnlockCost($productId, CoinService $coinService)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        $cost = $coinService->calculateUnlockCost($product);

        return response()->json([
            'unlock_cost' => $cost,
            'user_coins' => auth()->user()->coins,
        ]);
    }

    public function unlock($productId, CoinService $coinService)
    {
        $product = Product::with('seller')->findOrFail($productId);

        $user = auth('api')->user();

        if ($product->user_id === $user->id) {
            return response()->json([
                'status'  => false,
                'message' => 'You cannot unlock your own product. You already have access to its details.',
            ], 403);
        }

        if ($product->sale_type === 'auction' && $user->account_type !== 'auction') {
            return response()->json([
                'status'  => false,
                'message' => 'Your account is not eligible to unlock auction products. Please verify your ID card to access this feature.',
            ], 403);
        }

        $result = $coinService->unlockProduct($user, $product);

        if ($result) {
            return response()->json([
                'status'  => true,
                'message' => 'Product unlocked successfully.',
                'coins'   => $user->fresh()->coins,
                'data'    => new ProductResource($product),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Failed to unlock product. Please check your coin balance.',
            ], 400);
        }
    }

    public function updateMaterialPriority(Request $request, string $id)
    {
        $request->validate([
            'material_priority' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        if ($product->status === 'active') {
            return response()->json([
                'status'  => false,
                'message' => 'Cannot update material priority for an active product.',
            ], 403);
        }

        $product->update([
            'material_priority' => $request->material_priority,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Material priority updated successfully',
            'data'    => new ProductResource($product->load(['category', 'images', 'seller'])),
        ], 200);
    }

    public function approvedProducts()
    {
        $products = Product::with(['category', 'images', 'seller', 'auction'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        if (!$products) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Approved products retrieved successfully',
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    public function getAuctionProducts()
    {
        $products = Product::where('sale_type', 'auction')
            ->where('status', 'approved')
            ->whereHas('auction', function ($query) {
                $query->whereIn('status', ['scheduled', 'active'])
                    ->where('ends_at', '>', now());
            })
            ->with(['category', 'seller', 'images', 'auction'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'  => true,
            'message' => 'Auction products retrieved successfully.',
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    public function getCoinProducts()
    {
        $products = Product::where('sale_type', 'coins')
            ->where('status', 'approved')
            ->with(['category', 'seller', 'images', 'auction'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'  => true,
            'message' => 'Coin-based products retrieved successfully.',
            'data'    => ProductResource::collection($products),
        ], 200);
    }

    public function changeSaleType(Request $request, string $id)
    {
        $request->validate([
            'sale_type' => 'required|in:coins,auction',
        ]);

        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        $product->update([
            'sale_type' => $request->sale_type,
        ]);

        $message = $request->sale_type === 'auction'
            ? 'Product set to auction successfully!'
            : 'Product set to coins successfully!';

        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => new ProductResource($product->load(['category', 'images', 'seller'])),
        ], 200);
    }

    public function myProducts()
    {
        $products = auth()->user()->products()
            ->with(['category', 'seller', 'images', 'auction'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'true',
            'data'   => ProductResource::collection($products),
        ], 200);
    }

    public function myCoinProducts()
    {
        $products = auth('api')->user()->products()
            ->where('sale_type', 'coins')
            ->with(['category', 'seller', 'images'])
            ->latest()
            ->paginate(15);

        return response()->json([
            'status'  => 'true',
            'message' => 'My coin products retrieved successfully.',
            'data'    => ProductResource::collection($products),
        ], 200);
    }



    public function myUnlockedProducts()
    {
        $products = auth('api')->user()->unlockedProducts()
            ->with(['seller', 'category', 'images', 'auction'])
            ->latest('product_unlocks.created_at')
            ->paginate(15);

        return response()->json([
            'status'  => 'true',
            'message' => 'Unlocked products retrieved successfully.',
            'data'    => ProductResource::collection($products),
        ], 200);
    }
}
