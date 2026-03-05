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
        $products = Product::with(['category', 'images'])->latest()->get();

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
        $product = Product::create([
            'user_id'     => Auth::id(),
            'category_id' => $request->category_id,
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
        $product = Product::with(['category', 'images', 'seller'])->find($id);

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
        $product = Product::with('images')->find($id);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
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
            'data'    => new ProductResource($product->load(['category', 'images'])),
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
        $product = Product::findOrFail($productId);

        if (!$product) {
            return response()->json([
                'status'  => false,
                'message' => 'Product not found',
            ], 404);
        }

        $result = $coinService->unlockProduct(auth()->user(), $product);

        return response()->json([
            'status' => $result,
            'coins' => auth()->user()->fresh()->coins
        ]);
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

        $product->update([
            'material_priority' => $request->material_priority,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Material priority updated successfully',
            'data'    => new ProductResource($product->load(['category', 'images'])),
        ], 200);
    }
}
