<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqsController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('category')
            ->orderBy('display_order')
            ->get()
            ->groupBy('category');

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'question' => 'required|string',
            'answer' => 'required|string',
            'display_order' => 'nullable|integer',
        ]);

        Faq::create([
            'category' => $request->category,
            'question' => $request->question,
            'answer' => $request->answer,
            'display_order' => $request->display_order ?? 0,
            'is_active' => true,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ added');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([
            'category' => 'required|string|max:100',
            'question' => 'required|string',
            'answer' => 'required|string',
            'display_order' => 'nullable|integer',
        ]);

        $faq->update($request->only([
            'category', 'question', 'answer', 'display_order'
        ]));

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully!');    }

    public function toggle(Faq $faq)
    {
        $faq->update([
            'is_active' => !$faq->is_active
        ]);

        return back();
    }
}
