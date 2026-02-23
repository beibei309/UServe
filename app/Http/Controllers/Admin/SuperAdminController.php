<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function adminsIndex()
{
    $admins = \App\Models\Admin::orderBy('role')->get();

    return view('admin.admins.index', compact('admins'));
}

public function create()
{
    return view('admin.admins.create');
}

public function store(Request $request)
{
    // LIMIT 3 ADMINS
        if (Admin::count() >= 3) {
            return back()->with('error', 'You can only have up to 3 admin accounts.');
        }

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:admins,email',
        'password' => 'required|min:6',
        'role' => 'required'
    ]);

    \App\Models\Admin::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);

    return redirect()->route('admin.super.admins.index')
                     ->with('success', 'Admin created successfully.');
}

public function edit($id)
{
    $admin = \App\Models\Admin::findOrFail($id);

    return view('admin.admins.edit', compact('admin'));
}

public function update(Request $request, $id)
{
    $admin = \App\Models\Admin::findOrFail($id);

    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:admins,email,' . $id,
        'role' => 'required'
    ]);

    $admin->update([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
    ]);

    if ($request->password) {
        $admin->update(['password' => bcrypt($request->password)]);
    }

    return redirect()->route('admin.super.admins.index')
                     ->with('success', 'Admin updated successfully.');
}

public function destroy($id)
{
    if (Admin::count() <= 1) {
    return back()->with('error', 'At least 1 admin account is required.');
}

if (Admin::count() <= 1) {
    return back()->with('error', 'At least 1 admin account is required.');
}

    $admin = \App\Models\Admin::findOrFail($id);
    $admin->delete();

    return redirect()->route('admin.super.admins.index')
                     ->with('success', 'Admin deleted successfully.');
}

}
