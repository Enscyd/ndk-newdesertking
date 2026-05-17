<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DashboardAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // check browser session
        if (!session()->has('dashboard_user_id')) {
            return redirect('/');
        }

        // verify user still active in DB
        $user = DB::table('dashboardusers')
            ->where('id', session('dashboard_user_id'))
            ->where('isActive', true)
            ->first();

        if (!$user) {
            session()->forget('dashboard_user_id');
            return redirect('/');
        }

        return $next($request);
    }
}