<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

     public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:h2u_categories,hc_slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'required|boolean',
        ]);

        $payload = [
            'hc_name' => $data['name'],
            'hc_slug' => $data['slug'],
            'hc_description' => $data['description'] ?? null,
            'hc_icon' => $data['icon'] ?? null,
            'hc_color' => $data['color'] ?? null,
            'hc_is_active' => $data['is_active'],
        ];

        if ($request->hasFile('image_path')) {
            $payload['hc_image_path'] = $request->file('image_path')->store('categories', 'public');
        }

        Category::create($payload);
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:h2u_categories,hc_slug,' . $category->hc_id . ',hc_id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'required|boolean',
        ]);

        $payload = [
            'hc_name' => $data['name'],
            'hc_slug' => $data['slug'],
            'hc_description' => $data['description'] ?? null,
            'hc_icon' => $data['icon'] ?? null,
            'hc_color' => $data['color'] ?? null,
            'hc_is_active' => $data['is_active'],
        ];

        if ($request->hasFile('image_path')) {
            $payload['hc_image_path'] = $request->file('image_path')->store('categories', 'public');
        }

        $category->update($payload);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

}
