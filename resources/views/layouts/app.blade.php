<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDK System</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-item {
            position: relative;
            cursor: pointer;
            white-space: nowrap;
        }

        .nav-item:hover {
            color: #14b8a6;
        }

        summary {
            list-style: none;
            cursor: pointer;
        }

        summary::-webkit-details-marker {
            display: none;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">

<nav x-data="{ mobileOpen: false }" class="navbar bg-gray-900 text-white shadow-lg">
    <div class="w-full flex items-center justify-between h-16 px-4 md:px-6">
        
        <!-- Logo -->
        <a href="/" class="text-xl md:text-2xl font-bold text-teal-400 hover:text-teal-300 transition">
            NDK System
        </a>

        <!-- Desktop Menu -->
        <ul class="hidden md:flex space-x-6 ml-auto items-center">

            <!-- Workshop -->
            <li x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="relative nav-item">
                <button>WorkShop Bills</button>
                <ul x-show="open" x-transition class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">
                    <li><a href="{{ route('workshop.create') }}" class="block px-4 py-2 hover:bg-gray-700">Workshop</a></li>
                    <li><a href="{{ route('suggestion.index') }}" class="block px-4 py-2 hover:bg-gray-700">Suggestion</a></li>
                </ul>
            </li>

            <!-- Employee -->
            <li x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="relative nav-item">
                <button>Employee</button>
                <ul x-show="open" x-transition class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">
                    <li><a href="{{ route('employee.index') }}" class="block px-4 py-2 hover:bg-gray-700">Add Employee</a></li>
                    <li><a href="{{ route('employee.accounts') }}" class="block px-4 py-2 hover:bg-gray-700">Accounts</a></li>
                </ul>
            </li>

            <!-- Accounts -->
            <li x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="relative nav-item">
                <button>Accounts</button>
                <ul x-show="open" x-transition class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">
                    <li><a href="{{ route('purpose.index') }}" class="block px-4 py-2 hover:bg-gray-700">Purpose</a></li>
                    <li><a href="{{ route('accounts.index') }}" class="block px-4 py-2 hover:bg-gray-700">Account Sheet</a></li>
                </ul>
            </li>

            <!-- Bill Book -->
            <li x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="relative nav-item">
                <button>Bill Book</button>
                <ul x-show="open" x-transition class="absolute left-0 mt-2 w-48 bg-gray-800 rounded shadow-xl py-2">
                    <li><a href="{{ route('billing.create') }}" class="block px-4 py-2 hover:bg-gray-700">Billing Form</a></li>
                    <li><a href="{{ route('billing.index') }}" class="block px-4 py-2 hover:bg-gray-700">Bill Book</a></li>
                </ul>
            </li>

            <!-- Trips -->
            <li x-data="{open:false}" @mouseenter="open=true" @mouseleave="open=false" class="relative nav-item">
                <button>Trips</button>
                <ul x-show="open" x-transition class="absolute left-0 mt-2 w-56 bg-gray-800 rounded shadow-xl py-2">
                    <li><a href="{{ route('company.add') }}" class="block px-4 py-2 hover:bg-gray-700">Add Company</a></li>
                    <li><a href="{{ route('destination.index') }}" class="block px-4 py-2 hover:bg-gray-700">Add Destination</a></li>
                    <li><a href="{{ route('truck.index') }}" class="block px-4 py-2 hover:bg-gray-700">Add Truck</a></li>
                    <li><a href="{{ route('trip.index') }}" class="block px-4 py-2 hover:bg-gray-700">Trip Sheet</a></li>
                    <li><a href="{{ route('expense.index') }}" class="block px-4 py-2 hover:bg-gray-700">Expense Sheet</a></li>
                </ul>
            </li>

            <li>
                <a href="{{ route('sparepart.index') }}" class="hover:text-teal-400">
                    Spareparts
                </a>
            </li>

            <li>
                <form method="POST" action="{{ route('dashboard.logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-500 hover:bg-red-600 rounded-xl font-semibold">
                        Logout
                    </button>
                </form>
            </li>
        </ul>

        <!-- Mobile Button -->
        <button @click="mobileOpen = !mobileOpen" class="md:hidden text-3xl">
            ☰
        </button>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen" x-transition class="md:hidden bg-gray-800 px-4 pb-4 space-y-2">

        <details class="bg-gray-700 rounded-lg p-2">
            <summary>Workshop Bills</summary>
            <div class="mt-2 ml-4 space-y-2">
                <a href="{{ route('workshop.create') }}" class="block">Workshop</a>
                <a href="{{ route('suggestion.index') }}" class="block">Suggestion</a>
            </div>
        </details>

        <details class="bg-gray-700 rounded-lg p-2">
            <summary>Employee</summary>
            <div class="mt-2 ml-4 space-y-2">
                <a href="{{ route('employee.index') }}" class="block">Add Employee</a>
                <a href="{{ route('employee.accounts') }}" class="block">Accounts</a>
            </div>
        </details>

        <details class="bg-gray-700 rounded-lg p-2">
            <summary>Accounts</summary>
            <div class="mt-2 ml-4 space-y-2">
                <a href="{{ route('purpose.index') }}" class="block">Purpose</a>
                <a href="{{ route('accounts.index') }}" class="block">Account Sheet</a>
            </div>
        </details>

        <details class="bg-gray-700 rounded-lg p-2">
            <summary>Bill Book</summary>
            <div class="mt-2 ml-4 space-y-2">
                <a href="{{ route('billing.create') }}" class="block">Billing Form</a>
                <a href="{{ route('billing.index') }}" class="block">Bill Book</a>
            </div>
        </details>

        <details class="bg-gray-700 rounded-lg p-2">
            <summary>Trips</summary>
            <div class="mt-2 ml-4 space-y-2">
                <a href="{{ route('company.add') }}" class="block">Add Company</a>
                <a href="{{ route('destination.index') }}" class="block">Add Destination</a>
                <a href="{{ route('truck.index') }}" class="block">Add Truck</a>
                <a href="{{ route('trip.index') }}" class="block">Trip Sheet</a>
                <a href="{{ route('expense.index') }}" class="block">Expense Sheet</a>
            </div>
        </details>

        <a href="{{ route('sparepart.index') }}" class="block bg-gray-700 rounded-lg p-2">
            Spareparts
        </a>

        <form method="POST" action="{{ route('dashboard.logout') }}">
            @csrf
            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 rounded-lg py-2">
                Logout
            </button>
        </form>
    </div>
</nav>

<div class="w-full p-4 md:p-6">
    @yield('content')
</div>

@stack('scripts')

</body>
</html>