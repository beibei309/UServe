@extends('admin.layout')

@section('content')

<div class="max-w-xl bg-white p-6 rounded-2xl shadow border border-gray-200">

    <h2 class="text-2xl font-bold mb-4">Add New Admin</h2>

    <form action="{{ route('admin.super.admins.store') }}" method="POST">
        @csrf

        <label>Name</label>
        <input type="text" name="name" class="w-full p-2 border rounded mb-3">

        <label>Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded mb-3">

        <label>Password</label>
        <input type="password" name="password" class="w-full p-2 border rounded mb-3">

        <label>Role</label>
        <select name="role" class="w-full p-2 border rounded mb-3">
            <option value="admin">Admin</option>
            <option value="superadmin">Super Admin</option>
        </select>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Create Admin</button>
    </form>

</div>

@endsection
