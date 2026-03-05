<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use App\Http\Requests\DashBoard\Category\CategoryRequest;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 200);
    }

    public function store(CategoryRequest $request)
    {
        $category = \App\Models\Product\Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => auth('admin-api')->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'data' => $category
        ], 201);
    }     
    

    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $category
        ], 200);
    }

    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'data' => $category
        ], 200);
    }

    public function softDelete(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $category->delete(); 

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully'
        ], 200);
    }

    public function trashed()
    {
        $categories = Category::onlyTrashed()->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 200);
    }

    
    public function restore(string $id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        if (!$category->trashed()) {
            return response()->json(['status' => 'error', 'message' => 'Category is not deleted'], 400);
        }

        $category->restore();

        return response()->json([
            'status' => 'success',
            'message' => 'Category restored successfully',
            'data' => $category
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json(['status' => 'error', 'message' => 'Not Found'], 404);
        }

        $category->forceDelete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category permanently deleted'
        ], 200);
    }
}
