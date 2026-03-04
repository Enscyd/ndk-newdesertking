<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDK System</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        /* Underline hover animation */
        .nav-item {
            position: relative;
            padding-bottom: 4px;
        }
        .nav-item::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 0%;
            background-color: #14b8a6;
            transition: width 0.3s ease;
        }
        .nav-item:hover::after {
            width: 100%;
        }
        /* ACTIVE MENU */
        .nav-item.active {
            color: #14b8a6;
            font-weight: bold;
        }
        .nav-item.active::after {
            width: 100%;
        }

        /* Mobile slide animation */
        #mobileMenu {
            transform-origin: top;
            transform: scaleY(0);
            opacity: 0;
            transition: transform .3s ease, opacity .3s ease;
        }
        #mobileMenu.open {
            transform: scaleY(1);
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-100">

<!-- TOP NAV -->
<nav class="bg-gray-900 text-white h-16 flex items-center px-6 shadow-lg">

    <!-- LOGO -->
    <div class="text-2xl font-bold text-teal-300 mr-10">NDK System</div>

    <!-- DESKTOP MENU -->
    <ul class="hidden md:flex space-x-10">

        <!-- WORKSHOP -->
        <li x-data="{open:false}" class="relative nav-item">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    @click="open=!open">
                WorkShop Bills
            </button>

            <ul x-show="open" x-transition.opacity.duration.200ms
                @mouseenter="open=true" @mouseleave="open=false"
                class="absolute bg-gray-800 w-48 mt-2 py-2 rounded shadow-xl">

                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Workshop</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Suggestions</a></li>
            </ul>
        </li>

        <!-- EMPLOYEE -->
        <li x-data="{open:false}" class="relative nav-item">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    @click="open=!open">
                Employee
            </button>

            <ul x-show="open" x-transition.opacity.duration.200ms
                @mouseenter="open=true" @mouseleave="open=false"
                class="absolute bg-gray-800 w-48 mt-2 py-2 rounded shadow-xl">
                
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Add Employee</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Accounts</a></li>
            </ul>
        </li>

        <!-- ACCOUNTS -->
        <li x-data="{open:false}" class="relative nav-item">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    @click="open=!open">
                Accounts
            </button>

            <ul x-show="open" x-transition.opacity.duration.200ms
                @mouseenter="open=true" @mouseleave="open=false"
                class="absolute bg-gray-800 w-48 mt-2 py-2 rounded shadow-xl">
                
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Account Sheet</a></li>
            </ul>
        </li>

        <!-- BILL BOOK -->
        <li x-data="{open:false}" class="relative nav-item">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    @click="open=!open">
                Bill Book
            </button>

            <ul x-show="open" x-transition.opacity.duration.200ms
                @mouseenter="open=true" @mouseleave="open=false"
                class="absolute bg-gray-800 w-48 mt-2 py-2 rounded shadow-xl">
                
                
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Billing Form</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Print Bill</a></li>
            </ul>
        </li>

        <!-- TRIPS -->
        <li x-data="{open:false}" class="relative nav-item">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    @click="open=!open">
                Trips
            </button>

            <ul x-show="open" x-transition.opacity.duration.200ms
                @mouseenter="open=true" @mouseleave="open=false"
                class="absolute bg-gray-800 w-48 mt-2 py-2 rounded shadow-xl">
<li><a href="{{ route('company.add') }}" class="block px-4 py-2 hover:bg-gray-700">Add Company</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Add Destination</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Add Truck</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Trip Sheet</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Expense Sheet</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Password</a></li>
            </ul>
        </li>

        <!-- SPAREPARTS -->
        <li x-data="{open:false}" class="relative nav-item">
            <button @mouseenter="open=true" @mouseleave="open=false"
                    @click="open=!open">
                Spareparts
            </button>

            <ul x-show="open" x-transition.opacity.duration.200ms
                @mouseenter="open=true" @mouseleave="open=false"
                class="absolute bg-gray-800 w-48 mt-2 py-2 rounded shadow-xl">

                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Add Supplier</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Add Sparepart</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Sale</a></li>
                <li><a href="#" class="block px-4 py-2 hover:bg-gray-700">Stock Report</a></li>
            </ul>
        </li>

    </ul>

    <!-- MOBILE MENU BUTTON -->
    <button class="md:hidden ml-auto text-3xl" onclick="toggleMobileMenu()">☰</button>
</nav>

<!-- MOBILE MENU -->
<div id="mobileMenu" class="hidden bg-gray-800 text-white p-4 md:hidden space-y-4">

    <details class="submenu">
        <summary class="py-2">WorkShop Bills</summary>
        <ul class="ml-6 space-y-1">
            <li><a href="#" class="block py-1">Workshop</a></li>
            <li><a href="#" class="block py-1">Suggestions</a></li>
        </ul>
    </details>

    <details class="submenu">
        <summary class="py-2">Employee</summary>
        <ul class="ml-6 space-y-1">
            <li><a href="#" class="block py-1">Add Employee</a></li>
            <li><a href="#" class="block py-1">Accounts</a></li>
        </ul>
    </details>

    <details class="submenu">
        <summary class="py-2">Accounts</summary>
        <ul class="ml-6 space-y-1">
            <li><a href="#" class="block py-1">Account Sheet</a></li>
        </ul>
    </details>

    <details class="submenu">
        <summary class="py-2">Bill Book</summary>
        <ul class="ml-6 space-y-1">
           
            <li><a href="#" class="block py-1">Billing Form</a></li>
            <li><a href="#" class="block py-1">Print Bill</a></li>
        </ul>
    </details>

    <details class="submenu">
        <summary class="py-2">Trips</summary>
        <ul class="ml-6 space-y-1">
             <li><a href="{{ route('company.add') }}" class="block py-1">Add Company</a></li>
            <li><a href="#" class="block py-1">Add Destination</a></li>
            <li><a href="#" class="block py-1">Trip Sheet</a></li>
            <li><a href="#" class="block py-1">Expense Sheet</a></li>
            <li><a href="#" class="block py-1">Password</a></li>
        </ul>
    </details>

    <details class="submenu">
        <summary class="py-2">Spareparts</summary>
        <ul class="ml-6 space-y-1">
            <li><a href="#" class="block py-1">Add Supplier</a></li>
            <li><a href="#" class="block py-1">Add Sparepart</a></li>
            <li><a href="#" class="block py-1">Sale</a></li>
            <li><a href="#" class="block py-1">Stock Report</a></li>
        </ul>
    </details>

</div>

<!-- MAIN CONTENT -->
<div class="p-6">
    @yield('content')
</div>

<script>
// Toggle mobile menu
function toggleMobileMenu() {
    const m = document.getElementById('mobileMenu');
    m.classList.toggle('hidden');
    setTimeout(() => m.classList.toggle('open'), 10);
}

// Auto-close other submenus
document.querySelectorAll("#mobileMenu .submenu").forEach(el => {
    el.addEventListener("click", () => {
        document.querySelectorAll("#mobileMenu .submenu").forEach(other => {
            if (other !== el) other.removeAttribute("open");
        });
    });
});
</script>

</body>
</html>