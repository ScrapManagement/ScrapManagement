<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\DashBoard\Category\CategoryRequest;
use App\Http\Requests\DashBoard\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Product\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with(['parent', 'children' ,'admin'])->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data'   => CategoryResource::collection($categories)
        ], 200);
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'material_priority' => $request->material_priority,
            'created_by' => auth('admin-api')->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully',
            'data' => new CategoryResource($category->load(['parent', 'children', 'admin'])),
        ], 201);
    }


    public function show(string $id)
    {
        $category =  Category::with(['parent', 'children', 'admin'])->find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'status' => 'true',
            'message' => 'Category retrieved successfully',
            'data' =>  new CategoryResource($category),
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }


        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'material_priority' => $request->material_priority,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Category updated successfully',
            'data'    => new CategoryResource($category->load(['parent', 'children', 'admin'])),
        ], 200);
    }

    public function softDelete(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Category soft deleted successfully',
        ], 200);
    }

    public function trashed()
    {
        $categories = Category::with(['parent', 'children', 'admin'])->onlyTrashed()->get();

        return response()->json([
            'status'  => true,
            'message' => 'Trashed categories retrieved successfully',
            'data'    => CategoryResource::collection($categories),
        ], 200);
    }


    public function restore(string $id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        if (!$category->trashed()) {
            return response()->json([
                'status'  => false,
                'message' => 'Category is not deleted',
            ], 400);
        }


        $category->restore();

        return response()->json([
            'status'  => true,
            'message' => 'Category restored successfully',
            'data'    => new CategoryResource($category->load(['parent', 'children', 'admin'])),
        ], 200);
    }

    public function forceDelete(string $id)
    {
        $category = Category::withTrashed()->find($id);

        if (!$category) {
            return response()->json([
                'status'  => false,
                'message' => 'Category not found',
            ], 404);
        }

        $category->forceDelete();

        return response()->json([
            'status'  => true,
            'message' => 'Category permanently deleted successfully',
        ], 200);
    }
}
