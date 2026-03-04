@extends('admin.layout')

@section('content')

<div class="px-4 md:px-0">

    <!-- HEADER -->
    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white">Super Admin Dashboard</h1>
    <p class="text-slate-300 mt-1">System-wide insights and administrator management.</p>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mt-8 sm:mt-10">

        <!-- Students -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-cyan-500/20 transition border border-slate-700">
            <p class="text-slate-200 font-medium">Total Students</p>
            <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-cyan-400 mt-4">{{ $totalStudents }}</p>
        </div>

        <!-- Community Users -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-purple-500/20 transition border border-slate-700">
            <p class="text-slate-200 font-medium">Community Users</p>
            <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-purple-400 mt-4">{{ $totalCommunityUsers }}</p>
        </div>

        <!-- Services -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-pink-500/20 transition border border-slate-700">
            <p class="text-slate-200 font-medium">All Services</p>
            <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-pink-400 mt-4">{{ $totalServices }}</p>
        </div>

        <!-- Requests -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-6 rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-yellow-500/20 transition border border-slate-700">
            <p class="text-slate-200 font-medium">Pending Requests</p>
            <p class="text-3xl sm:text-4xl lg:text-5xl font-bold text-yellow-400 mt-4">{{ $pendingRequests }}</p>
        </div>
    </div>

    <!-- MANAGE ADMINS -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 p-4 sm:p-6 rounded-2xl shadow-xl border border-slate-700 mt-10 sm:mt-12">

        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
            <h2 class="text-xl sm:text-2xl font-bold text-white">System Administrators</h2>

            <a href="{{ route('admin.super.admins.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
               + Add New Admin
            </a>
        </div>

        <div class="overflow-x-auto">
        <table class="w-full mt-4 min-w-[640px]">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-3">Name</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($admins as $admin)
                    <tr class="border-b">
                        <td class="py-3">{{ $admin->ha_name }}</td>
                        <td class="py-3">{{ $admin->ha_email }}</td>
                        <td class="py-3 capitalize">{{ $admin->ha_role }}</td>
                        <td class="py-3">
                            <div class="flex flex-wrap gap-3">
                            
                                     <a href="{{ route('admin.super.admins.edit', $admin->ha_id) }}"
                               class="text-blue-600 hover:underline">Edit</a>

                                <form action="{{ route('admin.super.admins.delete', $admin->ha_id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this admin?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>

    </div>

</div>

@endsection
