<?php

namespace App\Http\Controllers\DashBoard\Category;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return view for listing categories
        $categories = Category::all(); // This would typically fetch categories from the database
        return view('DashBoard.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view for creating a new category
        return view('DashBoard.categories.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Logic to store the category would go here
        \App\Models\Product\Category::create([
            'name' => $request->name,
            'description' => $request->input('description'),
            'created_by' => auth('admin')->id(), // assuming admin is authenticated
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Logic to get the category by id would go here
        $category = Category::findOrFail($id);
        return view('DashBoard.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Logic to get the category by id would go here
        $category = Category::findOrFail($id);
        return view('DashBoard.categories.update', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        // Logic to update the category would go here
        $category = Category::findOrFail($id);
        
        $category->update([
            'name' => $request->name,
            'description' => $request->input('description'),
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Logic to delete the category would go here
        $category = Category::findOrFail($id);
        $category->delete();    

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
