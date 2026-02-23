@extends('admin.layout')

@section('content')

<div class="px-4 md:px-8">

    <!-- HEADER -->
    <h1 class="text-4xl font-bold text-gray-800">Super Admin Dashboard</h1>
    <p class="text-gray-500 mt-1">System-wide insights and administrator management.</p>

    <!-- STAT CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-10">

        <!-- Students -->
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition border border-gray-100">
            <p class="text-gray-500 font-medium">Total Students</p>
            <p class="text-5xl font-bold text-blue-600 mt-2">{{ $totalStudents }}</p>
        </div>

        <!-- Community Users -->
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition border border-gray-100">
            <p class="text-gray-500 font-medium">Community Users</p>
            <p class="text-5xl font-bold text-purple-600 mt-2">{{ $totalCommunityUsers }}</p>
        </div>

        <!-- Services -->
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition border border-gray-100">
            <p class="text-gray-500 font-medium">All Services</p>
            <p class="text-5xl font-bold text-pink-600 mt-2">{{ $totalServices }}</p>
        </div>

        <!-- Requests -->
        <div class="bg-white p-6 rounded-2xl shadow-md hover:shadow-xl transition border border-gray-100">
            <p class="text-gray-500 font-medium">Pending Requests</p>
            <p class="text-5xl font-bold text-yellow-600 mt-2">{{ $pendingRequests }}</p>
        </div>
    </div>

    <!-- MANAGE ADMINS -->
    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 mt-12">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-700">System Administrators</h2>

            <a href="{{ route('admin.super.admins.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
               + Add New Admin
            </a>
        </div>

        <table class="w-full mt-4">
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
                        <td class="py-3">{{ $admin->name }}</td>
                        <td class="py-3">{{ $admin->email }}</td>
                        <td class="py-3 capitalize">{{ $admin->role }}</td>
                        <td class="py-3 flex gap-3">
                            
                            <a href="{{ route('admin.super.admins.edit', $admin->id) }}"
                               class="text-blue-600 hover:underline">Edit</a>

                            <form action="{{ route('admin.super.admins.delete', $admin->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Delete this admin?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

@endsection
