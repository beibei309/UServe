<?php

namespace App\Http\Controllers;
use App\Models\Faq;
use Illuminate\Http\Request;

class HelpController extends Controller
{

    public function index(Request $request)
    {
        $faqs = Faq::where('hfq_is_active', true)
            ->orderBy('hfq_category')
            ->orderBy('hfq_display_order')
            ->get()
            ->groupBy('hfq_category');

        return view('help', compact('faqs'));
    }
}