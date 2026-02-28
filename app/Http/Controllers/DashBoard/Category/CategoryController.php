<?php

namespace App\Http\Controllers\DashBoard\Category;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use Illuminate\Http\Request;
use App\Http\Requests\DashBoard\Category\CategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all(); 
        return view('DashBoard.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::whereNull('parent_id')->get();
        return view('DashBoard.categories.add', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest  $request)
    {   
        \App\Models\Product\Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'created_by' => auth('admin')->id(),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return view('DashBoard.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        $allCategories = Category::whereNull('parent_id')->where('id', '!=', $id)->get();
        return view('DashBoard.categories.update', compact('category', 'allCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        $category = Category::findOrFail($id);
        
        $category->update([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();    

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
