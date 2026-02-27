<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials = [
            'ha_email' => $input['email'],
            'password' => $input['password'],
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            // Redirect ALL admins (including superadmin) to main dashboard
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid credentials.');
    }

    public function logout(Request $request)
{
    Auth::guard('admin')->logout();   // Logout admin guard

    $request->session()->invalidate();     // Destroy session
    $request->session()->regenerateToken(); // Prevent CSRF issues

    return redirect('/admin/login');       // Redirect to login page
}

}
