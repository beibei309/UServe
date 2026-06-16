@extends('admin.layout')

@section('content')
    <div class="admin-list-page">
        
        <div class="admin-list-header">
            <div>
                <h1 class="admin-list-title">
                    Reviews for: {{ $service->hss_title }}
                </h1>
                <p class="admin-list-subtitle">
                    Seller: {{ $service->user->hu_name ?? 'Unknown' }}
                </p>
            </div>
            <a href="{{ route('admin.services.index') }}"
               class="admin-secondary-action">
                Back to Services
            </a>
        </div>

        {{-- Data Table --}}
        <div class="admin-table-card">
            <div class="admin-table-scroll">
                <table class="admin-table">
                    <thead>
                        <tr style="background-color: var(--bg-tertiary);">
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Reviewer</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Rating</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Comment</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                        <tr class="border-b transition-all duration-300" style="border-color: var(--border-color);">
                            <td class="py-4 px-3">
                                <div class="font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $review->reviewer->hu_name ?? 'Unknown User' }}
                                </div>
                            </td>

                            <td class="py-4 px-3">
                                <div class="flex items-center gap-1">
                                    <span class="font-bold text-lg transition-colors duration-300" style="color: var(--text-primary);">
                                        {{ $review->hr_rating }}
                                    </span>
                                    <i class="fa-solid fa-star text-yellow-500"></i>
                                </div>
                            </td>

                            <td class="py-4 px-3">
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $review->hr_comment ?? '-' }}
                                </div>
                                
                                @if($review->hr_reply)
                                    <div class="mt-2 text-xs p-2 rounded border transition-colors duration-300" 
                                         style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-secondary);">
                                        <strong>Seller Reply:</strong>
                                        {{ $review->hr_reply }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-4 px-3">
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ optional($review->hr_created_at)->format('d M Y h:i A') ?? '-' }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="admin-empty-row">
                                No reviews yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($reviews->hasPages())
            <div class="admin-pagination">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection
