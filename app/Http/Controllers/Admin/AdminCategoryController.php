<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\Concerns\LogsAdminActions;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    use LogsAdminActions;

    private function iconOptions(): array
    {
        return [
            'fa fa-user', 'fa fa-users', 'fa fa-user-circle', 'fa fa-id-card',
            'fa fa-home', 'fa fa-building', 'fa fa-store', 'fa fa-laptop-code',
            'fa fa-graduation-cap', 'fa fa-book', 'fa fa-pencil', 'fa fa-university',
            'fa fa-cog', 'fa fa-cogs', 'fa fa-wrench', 'fa fa-check-circle',
            'fa fa-paint-brush', 'fa fa-folder-open', 'fa fa-file', 'fa fa-file-text',
            'fa fa-calendar', 'fa fa-bell', 'fa fa-envelope',
            'fa fa-comments', 'fa fa-commenting', 'fa fa-search', 'fa fa-filter',
            'fa fa-soap', 'fa fa-credit-card', 'fa fa-shopping-cart', 'fa fa-tag',
            'fa fa-star', 'fa fa-heart', 'fa fa-thumbs-up', 'fa fa-flag',
            'fa fa-globe', 'fa fa-map-marker', 'fa fa-car', 'fa fa-bicycle',
        ];
    }

    public function index()
    {
        $categories = Category::orderBy('hc_name')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

     public function create()
    {
        $icons = $this->iconOptions();
        return view('admin.categories.create', compact('icons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:h2u_categories,hc_slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'required|boolean',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $resolvedSlug = $this->resolveSlug((string) $data['name'], $data['slug'] ?? null);

        $payload = [
            'hc_name' => $data['name'],
            'hc_slug' => $resolvedSlug,
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
        $icons = $this->iconOptions();
        return view('admin.categories.edit', compact('category', 'icons'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:h2u_categories,hc_slug,' . $category->hc_id . ',hc_id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'is_active' => 'required|boolean',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $resolvedSlug = $this->resolveSlug((string) $data['name'], $data['slug'] ?? null, $category);

        $payload = [
            'hc_name' => $data['name'],
            'hc_slug' => $resolvedSlug,
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
        $this->logAdminAction('category_deleted', [
            'category_id' => $category->hc_id,
            'name' => $category->hc_name,
            'slug' => $category->hc_slug,
        ]);

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

    private function resolveSlug(string $name, ?string $slug = null, ?Category $ignoreCategory = null): string
    {
        $baseSlug = Str::slug(trim($slug ?: $name));
        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $candidate = $baseSlug;
        $counter = 1;

        while (true) {
            $query = Category::query()->where('hc_slug', $candidate);
            if ($ignoreCategory) {
                $query->where('hc_id', '!=', $ignoreCategory->hc_id);
            }

            if (! $query->exists()) {
                return $candidate;
            }

            $candidate = $baseSlug . '-' . $counter;
            $counter++;
        }
    }

}
