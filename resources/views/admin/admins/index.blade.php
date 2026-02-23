@extends('admin.layout')

@section('content')

<div class="bg-white p-6 rounded-2xl shadow border border-gray-200">

    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-gray-700">Manage Admin Accounts</h2>

        {{-- ⭐ Bonus Tip 4: Disable Add Admin when 3 exist --}}
        <a href="{{ route('admin.super.admins.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700
                  {{ $admins->count() >= 3 ? 'opacity-50 pointer-events-none' : '' }}">
            + Add Admin
        </a>
    </div>

    {{-- ⭐ Bonus Tip 2: Notification when limit reached --}}
    @if($admins->count() >= 3)
        <p class="text-sm text-red-500 mb-3">
            ⚠️ You already have 3 admin accounts. Delete one to add another.
        </p>
    @endif

    {{-- ⭐ Bonus Tip 3: Highlight Super Admin visually --}}
    <table class="w-full mt-4">
        <thead>
            <tr class="border-b">
                <th class="py-3 text-left">Name</th>
                <th class="py-3 text-left">Email</th>
                <th class="py-3 text-left">Role</th>
                <th class="py-3 text-left">Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($admins as $admin)
            <tr class="border-b 
                {{ $admin->role === 'superadmin' ? 'bg-blue-50' : '' }}">

                <td class="py-3 font-medium">
                    {{ $admin->name }}
                    @if($admin->role === 'superadmin')
                        <span class="ml-2 px-2 py-1 text-xs bg-blue-600 text-white rounded">Super Admin</span>
                    @endif
                </td>

                <td class="py-3">{{ $admin->email }}</td>

                <td class="py-3 capitalize">{{ $admin->role }}</td>

                <td class="py-3 flex gap-3">

                    <a href="{{ route('admin.super.admins.edit', $admin->id) }}"
                       class="text-blue-600 hover:underline">Edit</a>

                    {{-- ⭐ Bonus Tip 1: Prevent deleting the main superadmin --}}
                    @if($admin->role !== 'superadmin')
                    <form action="{{ route('admin.super.admins.delete', $admin->id) }}"
                          method="POST"
                          onsubmit="return confirm('Delete admin account?');">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600 hover:underline">
                            Delete
                        </button>
                    </form>
                    @endif

                </td>
            </tr>
            @endforeach
        </tbody>

    </table>

    {{-- ⭐ Extra Advice Section (recommended for Super Admin UI clarity) --}}
    <div class="mt-6 p-4 bg-gray-50 border rounded-xl text-sm text-gray-600">
        <strong>Admin Management Tips:</strong>
        <ul class="list-disc ml-5 mt-2 space-y-1">
            <li>You can only create up to <strong>3 admins</strong> (including Super Admin).</li>
            <li>Only non-superadmin accounts can be deleted.</li>
            <li>Use roles to differentiate responsibility: <strong>superadmin</strong>, <strong>admin</strong>.</li>
            <li>Keep admin list clean by removing inactive accounts.</li>
        </ul>
    </div>

</div>

@endsection
