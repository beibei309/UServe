@props(['userId', 'isFavorited' => false])

<button
    data-favorite-button
    id="favorite-btn-{{ $userId }}"
    aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
    {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center px-4 py-3 rounded-lg border text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 ' . ($isFavorited ? 'border-red-300 text-red-700 bg-red-50 hover:bg-red-100 focus:ring-red-500' : 'border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-indigo-500')]) }}
    data-favorited="{{ $isFavorited ? 'true' : 'false' }}"
    data-user-id="{{ $userId }}"
>
    <svg id="favorite-icon-{{ $userId }}" class="h-5 w-5 {{ $isFavorited ? 'fill-current text-red-600' : 'text-gray-700' }}" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
    <span id="favorite-text-{{ $userId }}" class="ml-2">
        {{ $isFavorited ? 'Remove from Favorites' : 'Add to Favorites' }}
    </span>
</button>

@once
@push('scripts')
<script src="{{ asset('js/favorite-button.js') }}"></script>
@endpush
@endonce
