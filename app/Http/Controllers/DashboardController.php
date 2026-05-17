<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Employee;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalTrips = Trip::count();
        $totalEmployees = Employee::count();
        $totalRevenue = Trip::sum('tripAmount');
        $totalExpenses = Expense::sum('amount');
        
        $recentTrips = Trip::with('company')
            ->orderBy('tripDate', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalTrips',
            'totalEmployees',
            'totalRevenue',
            'totalExpenses',
            'recentTrips'
        ));
    }
}
