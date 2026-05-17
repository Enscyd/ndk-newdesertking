<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $user = DB::table('dashboardusers')
            ->where('isActive', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Invalid password');
        }

        session([
            'dashboard_user_id' => $user->id
        ]);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        session()->forget('dashboard_user_id');
        return redirect('/');
    }

    public function showChangePassword()
    {
        return view('dashboard.change-password');
    }

   public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|same:new_password_confirmation',
        'new_password_confirmation' => 'required',
    ]);

    $user = DB::table('dashboardusers')
        ->where('id', session('dashboard_user_id'))
        ->first();

    if (!$user) {
        return back()->with('error', 'User not found');
    }

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->with('error', 'Current password is incorrect');
    }

    DB::table('dashboardusers')
        ->where('id', $user->id)
        ->update([
            'password' => Hash::make($request->new_password),
            'updatedAt' => now(),
        ]);

    return back()->with('success', 'Password updated successfully');
}
}