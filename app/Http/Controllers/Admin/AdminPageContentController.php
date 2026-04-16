<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPageContentController extends Controller
{
    public function index(): View
    {
        $pages = PageContent::query()
            ->select('hpc_page')
            ->selectRaw('COUNT(*) as block_count')
            ->groupBy('hpc_page')
            ->orderBy('hpc_page')
            ->get();

        return view('admin.page-content.index', compact('pages'));
    }

    public function edit(string $page): View
    {
        $blocks = PageContent::query()
            ->where('hpc_page', $page)
            ->orderBy('hpc_type')
            ->orderBy('hpc_id')
            ->get();

        abort_if($blocks->isEmpty(), 404);

        $groupedBlocks = [
            'text' => $blocks->whereIn('hpc_type', ['text', 'textarea'])->values(),
            'media' => $blocks->whereIn('hpc_type', ['image', 'video'])->values(),
        ];

        return view('admin.page-content.edit', compact('page', 'groupedBlocks'));
    }

    public function update(Request $request, string $page): RedirectResponse
    {
        $blocks = PageContent::query()
            ->where('hpc_page', $page)
            ->whereIn('hpc_type', ['text', 'textarea'])
            ->get();

        abort_if($blocks->isEmpty(), 404);

        $inputBlocks = $request->input('blocks', []);

        foreach ($blocks as $block) {
            $newValue = $inputBlocks[$block->hpc_slug] ?? $block->hpc_value;
            $block->hpc_value = is_string($newValue) ? trim($newValue) : $block->hpc_value;

            // Keep content blocks active in standard admin workflow to avoid accidental hidden content.
            $block->hpc_is_active = true;

            $block->save();
        }

        PageContent::clearRuntimeCache();

        return redirect()->route('admin.page-content.edit', $page)->with('success', 'Page content updated successfully.');
    }

    public function uploadMedia(Request $request, string $slug): RedirectResponse
    {
        $block = PageContent::query()->where('hpc_slug', $slug)->firstOrFail();

        abort_unless(in_array($block->hpc_type, ['image', 'video'], true), 422);

        $rules = $block->hpc_type === 'video'
            ? ['file' => 'required|file|mimes:mp4,webm|max:51200']
            : ['file' => 'required|file|mimes:jpg,jpeg,png,webp|max:5120'];

        $validated = $request->validate($rules);
        $file = $validated['file'];

        $dir = $block->hpc_type === 'video' ? 'videos' : 'images';
        $filename = $slug . '-' . time() . '.' . $file->getClientOriginalExtension();
        $relativePath = $dir . '/' . $filename;

        $oldPath = $block->hpc_value;
        $file->move(public_path($dir), $filename);

        if (
            is_string($oldPath)
            && $oldPath !== ''
            && $oldPath !== $block->hpc_default
            && is_file(public_path($oldPath))
        ) {
            @unlink(public_path($oldPath));
        }

        $block->update([
            'hpc_value' => $relativePath,
            'hpc_is_active' => true,
        ]);

        PageContent::clearRuntimeCache();

        return back()->with('success', 'Media file replaced successfully.');
    }

    public function resetBlock(string $slug): RedirectResponse
    {
        $block = PageContent::query()->where('hpc_slug', $slug)->firstOrFail();

        if (
            in_array($block->hpc_type, ['image', 'video'], true)
            && is_string($block->hpc_value)
            && $block->hpc_value !== ''
            && $block->hpc_value !== $block->hpc_default
            && is_file(public_path($block->hpc_value))
        ) {
            @unlink(public_path($block->hpc_value));
        }

        $block->update([
            'hpc_value' => $block->hpc_default,
            'hpc_is_active' => true,
        ]);

        PageContent::clearRuntimeCache();

        return back()->with('success', 'Block reset to default value.');
    }
}
