<?php

namespace App\Http\Controllers;

use App\Models\StudentService;
use App\Models\Category;
use Illuminate\Http\Request;

class GuestServicesController extends Controller
{
    public function index(Request $request)
    {
        // filters
        $category_id = $request->category_id;
        $sort = $request->sort ?? 'newest';

$query = StudentService::with(['student', 'category'])
            ->where('status', 'available');

        // filter by category
        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        // sorting logic
        if ($sort == 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort == 'price_low') {
            $query->orderBy('suggested_price', 'asc');
        } elseif ($sort == 'price_high') {
            $query->orderBy('suggested_price', 'desc');
        }

        return view('guest.services', [
            'services' => $query->get(),
            'categories' => Category::all(),
            'category_id' => $category_id,
            'sort' => $sort,
        ]);
    }

     public function show($id)
    {
        $service = StudentService::with(['student', 'category'])->findOrFail($id);

        return view('guest.detail-service', compact('service'));
    }
}
