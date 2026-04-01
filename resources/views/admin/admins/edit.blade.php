@extends('admin.layout')

@section('content')





<div class="max-w-xl bg-slate-800 p-6 rounded-2xl shadow-xl border border-slate-700" style="background-color: var(--bg-secondary); border-color: var(--border-color);">
    <h2 class="text-2xl font-bold mb-4 text-white">Edit Admin</h2>


    <form id="edit-admin-form" action="{{ route('admin.super.admins.update', $admin->ha_id) }}" method="POST">
        @csrf

        <label class="block text-slate-300 font-medium mb-2">Name</label>
        <input type="text" name="name" value="{{ $admin->ha_name }}" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white placeholder-slate-500 focus:ring-cyan-500 focus:border-cyan-500">

        <label class="block text-slate-300 font-medium mb-2">Email</label>
        <input type="email" name="email" value="{{ $admin->ha_email }}" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white placeholder-slate-500 focus:ring-cyan-500 focus:border-cyan-500">

        <label class="block text-slate-300 font-medium mb-2">Password <span class="text-xs text-slate-400">(leave blank to keep same)</span></label>
        <input type="password" name="password" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white placeholder-slate-500 focus:ring-cyan-500 focus:border-cyan-500">

        <label class="block text-slate-300 font-medium mb-2">Role</label>
        <select name="role" class="w-full p-2 border border-slate-700 rounded mb-3 bg-slate-900 text-white focus:ring-cyan-500 focus:border-cyan-500">
            <option value="admin" class="bg-slate-900" {{ $admin->ha_role == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="superadmin" class="bg-slate-900" {{ $admin->ha_role == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
        </select>

        <button id="update-admin-btn" type="submit" class="w-full bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white px-4 py-2 rounded font-medium transition">Update Admin</button>
    </form>

    <!-- CANCEL BUTTON -->
    <a href="{{ route('admin.super.admins.index') }}"
       class="w-full block mt-3 px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-600 text-center transition">Cancel</a>
</div>

@endsection
