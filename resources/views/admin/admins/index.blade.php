@extends('admin.layout')

@section('content')
    <div class="px-4 sm:px-6 py-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <h1 class="text-3xl font-bold transition-colors duration-300" style="color: var(--text-primary);">Manage Admin Accounts</h1>
            <a href="{{ route('admin.super.admins.create') }}"
               class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-lg hover:from-cyan-400 hover:to-blue-500 transition-all duration-300 {{ $admins->count() >= 3 ? 'opacity-50 pointer-events-none' : '' }}">
                + Add Admin
            </a>
        </div>

        @if($admins->count() >= 3)
            <div class="mb-4 p-3 rounded-lg border transition-colors duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                <p class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                    ⚠️ You already have 3 admin accounts. Delete one to add another.
                </p>
            </div>
        @endif

        {{-- Data Table --}}
        <div class="p-4 rounded-lg shadow-xl border transition-all duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr style="background-color: var(--bg-tertiary);">
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Name</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Email</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Role</th>
                            <th class="py-3 px-3 text-left text-xs font-medium" style="color: var(--text-secondary);">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($admins as $admin)
                        <tr class="border-b transition-all duration-300" style="border-color: var(--border-color); {{ $admin->ha_role === 'superadmin' ? 'background-color: var(--bg-tertiary);' : '' }}">
                            <td class="py-4 px-3">
                                <div class="font-medium transition-colors duration-300" style="color: var(--text-primary);">
                                    {{ $admin->ha_name }}
                                    @if($admin->ha_role === 'superadmin')
                                        <span class="ml-2 px-2 py-1 text-xs bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded">Super Admin</span>
                                    @endif
                                </div>
                            </td>

                            <td class="py-4 px-3">
                                <div class="text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ $admin->ha_email }}
                                </div>
                            </td>

                            <td class="py-4 px-3">
                                <div class="text-sm capitalize transition-colors duration-300" style="color: var(--text-secondary);">
                                    {{ $admin->ha_role }}
                                </div>
                            </td>

                            <td class="py-4 px-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.super.admins.edit', $admin->ha_id) }}"
                                       class="text-blue-500 hover:text-blue-700 transition-colors duration-300">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    @if($admin->ha_role !== 'superadmin')
                                        <form action="{{ route('admin.super.admins.delete', $admin->ha_id) }}"
                                              method="POST"
                                              data-confirm-message="Delete admin account?"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition-colors duration-300" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Admin Management Tips --}}
        <div class="mt-6 p-4 rounded-lg border transition-colors duration-300"
             style="background-color: var(--bg-secondary); border-color: var(--border-color);">
            <h3 class="font-semibold mb-2 transition-colors duration-300" style="color: var(--text-primary);">Admin Management Tips:</h3>
            <ul class="list-disc ml-5 space-y-1 text-sm transition-colors duration-300" style="color: var(--text-secondary);">
                <li>You can only create up to <strong>3 admins</strong> (including Super Admin).</li>
                <li>Only non-superadmin accounts can be deleted.</li>
                <li>Use roles to differentiate responsibility: <strong>superadmin</strong>, <strong>admin</strong>.</li>
                <li>Keep admin list clean by removing inactive accounts.</li>
            </ul>
        </div>
    </div>
@endsection
