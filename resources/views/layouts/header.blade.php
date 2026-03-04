<header class="bg-gray-900 text-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <a href="/" class="text-2xl font-bold text-teal-300">NDK System</a>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex space-x-6 items-center">

                <!-- WORKSHOP -->
                <div class="group relative">
                    <button class="hover:text-teal-300">⚙️ WorkShop Bills</button>
                    <div class="absolute hidden group-hover:block bg-gray-800 shadow mt-2 rounded w-48">
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Workshop</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Suggestions</a>
                    </div>
                </div>

                <!-- EMPLOYEE -->
                <div class="group relative">
                    <button class="hover:text-teal-300">👤 Employee</button>
                    <div class="absolute hidden group-hover:block bg-gray-800 shadow mt-2 rounded w-48">
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Add Employee</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Accounts</a>
                    </div>
                </div>

                <!-- ACCOUNTS -->
                <div class="group relative">
                    <button class="hover:text-teal-300">💰 Accounts</button>
                    <div class="absolute hidden group-hover:block bg-gray-800 shadow mt-2 rounded w-48">
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Account Sheet</a>
                    </div>
                </div>

                <!-- BILL BOOK -->
                <div class="group relative">
                    <button class="hover:text-teal-300">🧾 Bill Book</button>
                    <div class="absolute hidden group-hover:block bg-gray-800 shadow mt-2 rounded w-48">
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Add Company</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Billing Form</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Print Bill</a>
                    </div>
                </div>

                <!-- MOBILE DATA -->
                <div class="group relative">
                    <button class="hover:text-teal-300">📱 Mobile Data</button>
                    <div class="absolute hidden group-hover:block bg-gray-800 shadow mt-2 rounded w-48">
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Add Destination</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Trip Sheet</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Expense Sheet</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Password</a>
                    </div>
                </div>

                <!-- SPAREPARTS -->
                <div class="group relative">
                    <button class="hover:text-teal-300">🧩 Spareparts</button>
                    <div class="absolute hidden group-hover:block bg-gray-800 shadow mt-2 rounded w-48">
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Add Supplier</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Add Sparepart</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Sale</a>
                        <a class="block px-4 py-2 hover:bg-gray-700" href="#">Stock Report</a>
                    </div>
                </div>

            </nav>

            <!-- Mobile Hamburger -->
            <button id="mobileMenuBtn" class="md:hidden text-white">
                ☰
            </button>

        </div>
    </div>

    <!-- MOBILE MENU -->
    <div id="mobileMenu" class="hidden md:hidden bg-gray-800 p-4 space-y-4">

        <details class="group">
            <summary class="cursor-pointer text-white">⚙️ WorkShop Bills</summary>
            <div class="ml-4 mt-2 space-y-2">
                <a class="block hover:text-teal-300" href="#">Workshop</a>
                <a class="block hover:text-teal-300" href="#">Suggestions</a>
            </div>
        </details>

        <details>
            <summary class="cursor-pointer text-white">👤 Employee</summary>
            <div class="ml-4 mt-2 space-y-2">
                <a class="block hover:text-teal-300" href="#">Add Employee</a>
                <a class="block hover:text-teal-300" href="#">Accounts</a>
            </div>
        </details>

        <details>
            <summary class="cursor-pointer text-white">💰 Accounts</summary>
            <div class="ml-4 mt-2 space-y-2">
                <a class="block hover:text-teal-300" href="#">Account Sheet</a>
            </div>
        </details>

        <details>
            <summary class="cursor-pointer text-white">🧾 Bill Book</summary>
            <div class="ml-4 mt-2 space-y-2">
                <a class="block hover:text-teal-300" href="#">Add Company</a>
                <a class="block hover:text-teal-300" href="#">Billing Form</a>
                <a class="block hover:text-teal-300" href="#">Print Bill</a>
            </div>
        </details>

        <details>
            <summary class="cursor-pointer text-white">📱 Mobile Data</summary>
            <div class="ml-4 mt-2 space-y-2">
                <a class="block hover:text-teal-300" href="#">Add Destination</a>
                <a class="block hover:text-teal-300" href="#">Trip Sheet</a>
                <a class="block hover:text-teal-300" href="#">Expense Sheet</a>
                <a class="block hover:text-teal-300" href="#">Password</a>
            </div>
        </details>

        <details>
            <summary class="cursor-pointer text-white">🧩 Spareparts</summary>
            <div class="ml-4 mt-2 space-y-2">
                <a class="block hover:text-teal-300" href="#">Add Supplier</a>
                <a class="block hover:text-teal-300" href="#">Add Sparepart</a>
                <a class="block hover:text-teal-300" href="#">Sale</a>
                <a class="block hover:text-teal-300" href="#">Stock Report</a>
            </div>
        </details>

    </div>
</header>

<script>
document.getElementById("mobileMenuBtn").onclick = () =>
    document.getElementById("mobileMenu").classList.toggle("hidden");
</script>