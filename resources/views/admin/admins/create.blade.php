@extends('admin.layout')

@section('content')


    <script src="/js/admin-admins-index.js?v=3"></script>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        const success = @json(session('success'));
        const error = @json(session('error'));
        if (success) {
            document.body.setAttribute('data-success-message', success);
        }
        if (error) {
            document.body.setAttribute('data-error-message', error);
        }
    });
</script>

<div class="px-4 sm:px-6 py-4">
    <div class="max-w-xl p-6 rounded-2xl shadow-xl border transition-colors duration-300"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">

        <h2 class="text-2xl font-bold mb-4 transition-colors duration-300" style="color: var(--text-primary);">Add New Admin</h2>

        <form action="{{ route('admin.super.admins.store') }}" method="POST">
            @csrf

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Name</label>
            <input
                type="text"
                name="name"
                class="w-full p-2 border rounded mb-3 transition-colors duration-300 placeholder-gray-400 focus:ring-cyan-500 focus:border-cyan-500"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
            >

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Email</label>
            <input
                type="email"
                name="email"
                class="w-full p-2 border rounded mb-3 transition-colors duration-300 placeholder-gray-400 focus:ring-cyan-500 focus:border-cyan-500"
                style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
            >

            <label class="block font-medium mb-2 transition-colors duration-300" style="color: var(--text-secondary);">Password</label>
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
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
            </select>

            <button class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-4 py-2 rounded font-medium transition">Create Admin</button>
        </form>

    </div>
</div>

@endsection
