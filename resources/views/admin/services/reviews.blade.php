@extends('admin.layout')

@section('content')
    <div class="px-4 sm:px-6 py-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">
                    Reviews for: {{ $service->hss_title }}
                </h1>
                <p class="text-sm transition-colors duration-300 mt-1" style="color: var(--text-secondary);">
                    Seller: {{ $service->user->hu_name ?? 'Unknown' }}
                </p>
            </div>
            <a href="{{ route('admin.services.index') }}"
               class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg hover:from-cyan-400 hover:to-blue-500 transition-all duration-300">
                Back to Services
            </a>
        </div>

        {{-- Data Table --}}
        <div class="p-4 rounded-lg shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
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
                            <td colspan="4" class="py-8 text-center transition-colors duration-300" style="color: var(--text-secondary);">
                                No reviews yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($reviews->hasPages())
            <div class="mt-4 px-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
@endsection
