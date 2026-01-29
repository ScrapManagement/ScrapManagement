<?php

namespace App\Http\Controllers\DashBoard\Product;

use Illuminate\Http\Request;
use App\Models\Product\Image;
use App\Models\Product\Product;
use App\Models\Product\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Product\ProductRequest;
use App\Http\Requests\DashBoard\Product\UpdateProductRequest;
use App\Services\Product\ImageService;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['category', 'images'])->latest()->get();
        return view('DashBoard.Product.view', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('DashBoard.Product.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'user_id'     => /* Auth::id() */ 1,
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

        return redirect()->route('product.index')->with('success', 'Product created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with(['category', 'images','seller'])->findOrFail($id);

        return view('DashBoard.Product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('images')->findOrFail($id);
        $categories = Category::whereNull('parent_id')->get();

        return view('DashBoard.Product.update', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::with('images')->findOrFail($id);
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

        return redirect()->route('product.index')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::with('images')->findOrFail($id);

        ImageService::deleteImages($product->images);
        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted successfully');
    }
}
