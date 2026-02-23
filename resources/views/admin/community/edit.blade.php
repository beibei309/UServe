@extends('admin.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    <!-- Back -->
    <a href="{{ route('admin.community.view', $user->id) }}" 
       class="text-blue-600 hover:underline text-sm mb-6 inline-block">
        ← Back to Profile
    </a>

    <h1 class="text-3xl font-bold mb-6 text-gray-800">Edit Community User</h1>

    <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-100">

        <form action="{{ route('admin.community.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- SECTION: PROFILE PHOTO -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Profile Photo</h2>

                <div class="flex items-center gap-6">
                <div class="h-24 w-24 rounded-full overflow-hidden border shadow">
                    @php
                        $path = $user->profile_photo_path;
                        if (Str::startsWith($path, ['http://', 'https://'])) {
                            $imageUrl = $path;
                        } elseif ($path && file_exists(public_path('storage/' . $path))) {
                            $imageUrl = asset('storage/' . $path);
                        } elseif ($path) {
                            $imageUrl = asset($path);
                        } else {
                            $imageUrl = asset('uploads/profile/default.png');
                        }
                    @endphp
                    <img src="{{ $imageUrl }}" class="w-full h-full object-cover" alt="Profile" />
                </div>
                    <input type="file" name="profile_photo" 
                           class="border p-2 rounded-lg w-full text-sm">
                </div>
            </div>

            <!-- SECTION: BASIC INFO -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Basic Information</h2>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ $user->name }}"
                           class="border p-3 rounded-lg w-full focus:ring-blue-400 focus:border-blue-400" required>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $user->email }}"
                           class="border p-3 rounded-lg w-full focus:ring-blue-400 focus:border-blue-400" required>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ $user->phone }}"
                           class="border p-3 rounded-lg w-full focus:ring-blue-400 focus:border-blue-400">
                </div>
            </div>            

            <!-- SECTION: VERIFICATION -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Verification Status</h2>

                <select name="verification_status"
        class="border p-3 rounded-lg w-full focus:ring-blue-400 focus:border-blue-400">

    {{-- Pending (disabled if already approved) --}}
    <option value="pending"
        {{ $user->verification_status == 'pending' ? 'selected' : '' }}
        {{ $user->verification_status == 'approved' ? 'disabled' : '' }}>
        Pending
    </option>

    {{-- Approved --}}
    <option value="approved"
        {{ $user->verification_status == 'approved' ? 'selected' : '' }}>
        Approved
    </option>

    {{-- Rejected --}}
    <option value="rejected"
        {{ $user->verification_status == 'rejected' ? 'selected' : '' }}>
        Rejected
    </option>

</select>

@if($user->verification_status === 'approved')
    <p class="text-xs text-gray-500 mt-2">
        Verified users cannot be reverted back to Pending.
    </p>
@endif

            </div>

            <!-- SECTION: BLACKLIST -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-3 text-gray-800">Blacklist Status</h2>

                @if($user->is_blacklisted)
                    <div class="p-4 bg-red-100 border border-red-300 rounded-lg mb-4">
                        <p class="text-red-700 font-semibold">This user is currently blacklisted.</p>
                        <p class="text-red-700 text-sm mt-1"><strong>Reason:</strong> {{ $user->blacklist_reason }}</p>
                    </div>

                    <label class="flex items-center gap-2 font-medium text-gray-700">
                        <input type="checkbox" name="remove_blacklist" value="1">
                        Remove blacklist
                    </label>

                @else
                    <label class="block font-medium text-gray-700 mb-1">
                        Add blacklist reason (optional)
                    </label>

                    <textarea name="blacklist_reason" rows="3"
                              class="border p-3 rounded-lg w-full focus:ring-red-300 focus:border-red-400"
                              placeholder="Enter reason to blacklist this user..."></textarea>
                @endif
            </div>

            <!-- SUBMIT BUTTON -->
            <button class="px-6 py-3 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition">
                Save Changes
            </button>

        </form>
    </div>
</div>

@endsection