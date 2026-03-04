@extends('admin.layout')

@section('content')

<div class="max-w-xl bg-slate-800 p-6 rounded-2xl shadow-xl border border-slate-700">

    <h2 class="text-2xl font-bold mb-4 text-white">Add New Admin</h2>

    <form action="{{ route('admin.super.admins.store') }}" method="POST">
        @csrf

        <label class="block text-slate-300 font-medium mb-2">Name</label>
        <input type="text" name="name" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white placeholder-slate-500 focus:ring-cyan-500 focus:border-cyan-500">

        <label class="block text-slate-300 font-medium mb-2">Email</label>
        <input type="email" name="email" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white placeholder-slate-500 focus:ring-cyan-500 focus:border-cyan-500">

        <label class="block text-slate-300 font-medium mb-2">Password</label>
        <input type="password" name="password" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white placeholder-slate-500 focus:ring-cyan-500 focus:border-cyan-500">

        <label class="block text-slate-300 font-medium mb-2">Role</label>
        <select name="role" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white focus:ring-cyan-500 focus:border-cyan-500">
            <option value="admin" class="bg-slate-900">Admin</option>
            <option value="superadmin" class="bg-slate-900">Super Admin</option>
        </select>

        <button class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-4 py-2 rounded font-medium transition">Create Admin</button>
    </form>

</div>

@endsection
