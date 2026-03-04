<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class AdminFaqsController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('hfq_category')
            ->orderBy('hfq_display_order')
            ->get()
            ->groupBy('hfq_category');

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
            'hfq_category' => $request->category,
            'hfq_question' => $request->question,
            'hfq_answer' => $request->answer,
            'hfq_display_order' => $request->display_order ?? 0,
            'hfq_is_active' => true,
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

        $faq->update([
            'hfq_category' => $request->category,
            'hfq_question' => $request->question,
            'hfq_answer' => $request->answer,
            'hfq_display_order' => $request->display_order ?? 0,
        ]);

        return redirect()->route('admin.faqs.index')->with('success', 'FAQ updated');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faqs.index')->with('success', 'FAQ deleted successfully!');    }

    public function toggle(Faq $faq)
    {
        $faq->update([
            'hfq_is_active' => !$faq->hfq_is_active
        ]);

        // If AJAX request, return JSON response
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'FAQ status updated successfully',
                'is_active' => $faq->hfq_is_active
            ]);
        }

        return back()->with('success', 'FAQ status updated successfully');
    }
}
