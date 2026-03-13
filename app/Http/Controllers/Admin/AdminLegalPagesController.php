<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminLegalPagesController extends Controller
{
    public function index(): View
    {
        $termsPage = LegalPage::firstOrCreate(
            ['hlp_slug' => 'terms'],
            [
                'hlp_title' => 'Terms of Service',
                'hlp_content' => '<p>Update terms content here.</p>',
                'hlp_is_active' => true,
            ]
        );

        $privacyPage = LegalPage::firstOrCreate(
            ['hlp_slug' => 'privacy'],
            [
                'hlp_title' => 'Privacy Policy',
                'hlp_content' => '<p>Update privacy content here.</p>',
                'hlp_is_active' => true,
            ]
        );

        return view('admin.legal-pages.index', compact('termsPage', 'privacyPage'));
    }

    public function update(Request $request, LegalPage $legalPage): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $legalPage->update([
            'hlp_title' => $validated['title'],
            'hlp_content' => $validated['content'],
            'hlp_is_active' => (bool) ($validated['is_active'] ?? false),
        ]);

        return redirect()->route('admin.legal-pages.index')->with('success', 'Legal page updated successfully.');
    }
}
