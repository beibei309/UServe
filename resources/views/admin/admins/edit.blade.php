@extends('admin.layout')

@section('content')

<div class="max-w-xl bg-white p-6 rounded-2xl shadow border border-gray-200">

    <h2 class="text-2xl font-bold mb-4">Edit Admin</h2>

    <form action="{{ route('admin.super.admins.update', $admin->id) }}" method="POST">
        @csrf

        <label>Name</label>
        <input type="text" name="name" value="{{ $admin->name }}" class="w-full p-2 border rounded mb-3">

        <label>Email</label>
        <input type="email" name="email" value="{{ $admin->email }}" class="w-full p-2 border rounded mb-3">

        <label>Password (leave blank to keep same)</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-3">

        <label>Role</label>
        <select name="role" class="w-full p-2 border rounded mb-3">
            <option value="admin" {{ $admin->role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="superadmin" {{ $admin->role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
        </select>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update Admin</button>

        <!-- CANCEL BUTTON -->
        <a href="{{ route('admin.super.admins.index') }}"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
            Cancel
        </a>
    </form>

</div>

@endsection
