<?php

namespace App\Http\Controllers;
use App\Models\Faq;
use Illuminate\Http\Request;

class HelpController extends Controller
{

    public function index(Request $request)
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('category')
            ->orderBy('display_order')
            ->get()
            ->groupBy('category');

        return view('help', compact('faqs'));
    }
}