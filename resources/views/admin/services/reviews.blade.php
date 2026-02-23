@extends('admin.layout')

@section('content')
<div class="px-6 py-4">


    <div class="flex justify-between items-start mb-4">

    <!-- LEFT -->
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Reviews for: {{ $service->title }}
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Seller: {{ $service->user->name ?? 'Unknown' }}
        </p>
    </div>

    <!-- RIGHT -->
    <a href="{{ route('admin.services.index') }}"
       class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
        Back
    </a>
</div>

    

    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Reviewer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Comment</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase">Date</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($reviews as $review)
                <tr>
                    <td class="px-6 py-3">
                        {{ $review->reviewer->name ?? 'Unknown User' }}
                    </td>

                    <td class="px-6 py-3 font-bold">
                        ⭐ {{ $review->rating }}
                    </td>

                    <td class="px-6 py-3">
                        {{ $review->comment ?? '-' }}
                        
                        @if($review->reply)
                            <div class="mt-2 text-sm bg-gray-100 p-2 rounded">
                                <strong>Seller Reply:</strong>
                                {{ $review->reply }}
                            </div>
                        @endif
                    </td>

                    <td class="px-6 py-3 text-sm text-gray-500">
                        {{ $review->created_at->format('d M Y h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-6 text-center text-gray-500">
                        No reviews yet.
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
</div>
@endsection
