@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Dashboard Overview</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Stats Cards -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-teal-50 rounded-lg">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Total Trips</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalTrips ?? 0 }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-indigo-50 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Active Employees</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalEmployees ?? 0 }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-50 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Total Revenue</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">OMR {{ number_format($totalRevenue ?? 0, 2) }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-rose-50 rounded-lg">
                    <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">Total Expenses</h3>
            <p class="text-2xl font-bold text-gray-900 mt-1">OMR {{ number_format($totalExpenses ?? 0, 2) }}</p>
        </div>
    </div>

    <div class="mt-10 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Recent Trips</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-400 border-b">
                            <th class="pb-3 font-medium">Date</th>
                            <th class="pb-3 font-medium">Company</th>
                            <th class="pb-3 font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($recentTrips ?? [] as $trip)
                        <tr>
                            <td class="py-3">{{ $trip->tripDate->format('d M Y') }}</td>
                            <td class="py-3">{{ $trip->company->name }}</td>
                            <td class="py-3">OMR {{ number_format($trip->tripAmount, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-3 text-center text-gray-400">No recent trips found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('trip.index') }}" class="p-4 bg-teal-50 text-teal-700 rounded-lg hover:bg-teal-100 transition text-center font-medium">Add New Trip</a>
                <a href="{{ route('billing.create') }}" class="p-4 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition text-center font-medium">Create Invoice</a>
                <a href="{{ route('expense.index') }}" class="p-4 bg-amber-50 text-amber-700 rounded-lg hover:bg-amber-100 transition text-center font-medium">Log Expense</a>
                <a href="{{ route('employee.index') }}" class="p-4 bg-rose-50 text-rose-700 rounded-lg hover:bg-rose-100 transition text-center font-medium">Manage Employees</a>
            </div>
        </div>
    </div>
</div>
@endsection
