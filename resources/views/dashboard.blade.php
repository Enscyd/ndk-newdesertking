@extends('layouts.app')

@section('content')

@php
    // Operations KPIs
    $truckCount = \App\Models\Truck::count();
    $employeeCount = \App\Models\Employee::count();
    $tripCount = \App\Models\Trip::whereDate('tripDate', today())->count();

    // Billing KPIs
    $totalBills = \App\Models\Billing::count();
    $paidBills = \App\Models\Billing::where('paymentStatus', 'Paid')->count();
    $unpaidBills = \App\Models\Billing::where('paymentStatus', 'Unpaid')->count();
@endphp

<div class="max-w-7xl mx-auto px-6 py-10">

    <!-- Dashboard Header -->
    <div class="mb-10">
        <h1 class="text-4xl font-bold text-gray-900">
            NDK Dashboard
        </h1>
        <p class="text-gray-600 mt-2">
            Welcome to your logistics and billing management dashboard overview.
        </p>
    </div>

    <!-- Operations Section -->
    <div class="mb-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            Fleet Operations
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Active Trucks -->
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-yellow-500 p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">
                    Active Trucks
                </h3>
                <p class="text-5xl font-extrabold text-yellow-600">
                    {{ $truckCount }}
                </p>
            </div>

            <!-- Active Employees -->
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-blue-500 p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">
                    Active Employees
                </h3>
                <p class="text-5xl font-extrabold text-blue-600">
                    {{ $employeeCount }}
                </p>
            </div>

            <!-- Today Trips -->
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-green-500 p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">
                    Today Trips
                </h3>
                <p class="text-5xl font-extrabold text-green-600">
                    {{ $tripCount }}
                </p>
            </div>

        </div>
    </div>

    <!-- Billing Section -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">
            Billing Overview
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Total Bills -->
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-indigo-500 p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">
                    Total Bills
                </h3>
                <p class="text-5xl font-extrabold text-indigo-600">
                    {{ $totalBills }}
                </p>
            </div>

            <!-- Paid Bills -->
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-emerald-500 p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">
                    Paid Bills
                </h3>
                <p class="text-5xl font-extrabold text-emerald-600">
                    {{ $paidBills }}
                </p>
            </div>

            <!-- Unpaid Bills -->
            <div class="bg-white rounded-2xl shadow-lg border-l-4 border-red-500 p-6">
                <h3 class="text-lg font-semibold text-gray-600 mb-3">
                    Unpaid Bills
                </h3>
                <p class="text-5xl font-extrabold text-red-600">
                    {{ $unpaidBills }}
                </p>
            </div>

        </div>
    </div>

</div>

@endsection