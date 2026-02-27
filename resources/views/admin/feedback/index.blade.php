@extends('admin.layout')

@section('content')

<div class="px-6 py-4">
    <h1 class="text-3xl font-bold mb-6">Manage User Feedback & Warnings</h1>

    <form class="mb-6" method="GET" action="{{ route('admin.feedback.index') }}">
        <div class="flex space-x-4">
            <input type="text" name="search" placeholder="Search by name or email..."
                   class="p-2 border rounded w-1/3" value="{{ request('search') }}">
            <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">Search</button>
        </div>
    </form>

    <div class="bg-white shadow-lg rounded-lg p-6">
        <table class="w-full text-left">
            <thead class="bg-gray-100">
                <tr class="border-b">
                    <th class="py-3 px-4 text-sm font-medium text-gray-600">User Details</th>
                    <th class="py-3 px-4 text-sm font-medium text-gray-600">Reviews Avg (Total)</th>
                    <th class="py-3 px-4 text-sm font-medium text-gray-600">Warnings</th>
                    <th class="py-3 px-4 text-sm font-medium text-gray-600">Status</th>
                    <th class="py-3 px-4 text-sm font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($usersWithReviews as $user)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-3 px-4 text-sm">
                            <p class="font-bold">{{ $user->hu_name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->hu_email }} ({{ $user->hu_role }})</p>
                        </td>
                        
                        {{-- Average Rating: Tentukan warna berdasarkan rating --}}
                        <td class="py-3 px-4 text-sm">
                            <span class="font-semibold {{ ($user->reviews_received_avg_rating ?? 0) < 3.0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($user->reviews_received_avg_rating ?? 0, 2) }} / 5.0
                            </span>
                            <span class="text-xs text-gray-500">({{ $user->reviews_received_count ?? 0 }} reviews)</span>
                        </td>
                        
                        {{-- Warning Count --}}
                        <td class="py-3 px-4 text-sm font-semibold {{ $user->hu_warning_count >= 2 ? 'text-red-700' : 'text-yellow-600' }}">
                            {{ $user->hu_warning_count }} / 2
                        </td>
                        
                        {{-- Block Status --}}
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                {{ $user->hu_is_blocked ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}"> 
                                {{ $user->hu_is_blocked ? 'BLOCKED' : 'Active' }} 
                            </span> 
                        </td>
                        
                        {{-- Actions: Notify/Block --}}
                        <td class="py-3 px-4 text-sm">
                            @if (!$user->hu_is_blocked)
                                @if ($user->hu_warning_count < 2)
                                    {{-- Form untuk send warning --}}
                                    <form action="{{ route('admin.feedback.warning', $user->hu_id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('Send warning to {{ $user->hu_name }} (Warning #{{ $user->hu_warning_count + 1 }})?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600 transition-colors">
                                            Notify Warning ({{ $user->hu_warning_count + 1 }}/2)
                                        </button>
                                    </form>
                                @else
                                    {{-- Form untuk block user --}}
                                    <form action="{{ route('admin.feedback.block', $user->hu_id) }}" method="POST" class="inline-block"
                                        onsubmit="return confirm('FINAL WARNING: Block user {{ $user->hu_name }} permanently?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors">
                                            BLOCK USER
                                        </button>
                                    </form>
                                @endif
                            @else
                                <span class="text-red-500 font-semibold">Permanently Blocked</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $usersWithReviews->links() }}
        </div>
    </div>
</div>

@endsection