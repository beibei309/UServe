@extends('admin.layout')

@section('content')

<div class="px-4 sm:px-6 py-4">
    <div class="max-w-xl p-6 rounded-2xl shadow-xl border transition-colors duration-300"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        <h2 class="text-2xl font-bold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Edit Admin</h2>

        <form id="edit-admin-form" action="{{ route('admin.super.admins.update', $admin->ha_id) }}" method="POST">
            @csrf

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Name</label>
            <input
                type="text"
                name="name"
                value="{{ $admin->ha_name }}"
                class="w-full p-2 border rounded mb-3 transition-colors duration-300 placeholder-gray-400 focus:ring-cyan-500 focus:border-cyan-500"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
            >

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Email</label>
            <input
                type="email"
                name="email"
                value="{{ $admin->ha_email }}"
                class="w-full p-2 border rounded mb-3 transition-colors duration-300 placeholder-gray-400 focus:ring-cyan-500 focus:border-cyan-500"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
            >

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">
                Password <span class="text-xs transition-colors duration-300" style="color: var(--text-muted);">(leave blank to keep same)</span>
            </label>
            <input
                type="password"
                name="password"
                class="w-full p-2 border rounded mb-3 transition-colors duration-300 placeholder-gray-400 focus:ring-cyan-500 focus:border-cyan-500"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
            >

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Role</label>
            <select
                name="role"
                class="w-full p-2 border rounded mb-3 transition-colors duration-300 focus:ring-cyan-500 focus:border-cyan-500"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
            >
                <option value="admin" {{ $admin->ha_role == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="superadmin" {{ $admin->ha_role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
            </select>

            <button id="update-admin-btn" type="submit"
                    class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-4 py-2 rounded font-medium transition">
                Update Admin
            </button>
        </form>

        <a href="{{ route('admin.super.admins.index') }}"
           class="w-full block mt-3 px-4 py-2 rounded text-center border transition-colors duration-300 surface-hover"
           style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);">
            Cancel
        </a>
    </div>
</div>

@endsection
