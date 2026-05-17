<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NDK System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-gray-900">

<div class="min-h-screen relative overflow-hidden">
    <!-- Background soft glow -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-yellow-100 rounded-full blur-3xl opacity-60"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-slate-100 rounded-full blur-3xl opacity-70"></div>

    <div class="relative max-w-7xl mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center min-h-screen py-16">

            <!-- Left Content -->
            <div>
                <div class="inline-block px-4 py-2 bg-yellow-100 text-yellow-700 rounded-full text-sm font-semibold mb-6">
                    New Desert King LLC
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold leading-tight mb-6">
                    Reliable Transport <br>
                    <span class="text-yellow-600">Solutions in Oman</span>
                </h1>

                <p class="text-xl text-gray-600 leading-relaxed mb-10 max-w-2xl">
                    NEW DESERT KING LLC is committed to delivering quality services with experienced staff and well-maintained equipment.
                    Our team understands local regulations and client requirements, ensuring dependable solutions for every project.
                </p>

                <div class="flex flex-wrap gap-4 mb-12">
                    <button onclick="openLoginModal()"
                        class="px-8 py-4 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl transition duration-300">
                        Launch Dashboard
                    </button>
                </div>
            </div>

            <!-- Right Logo Section -->
            <div class="relative flex justify-center">
                <div class="absolute -top-4 right-8 bg-yellow-500 text-white px-5 py-2 rounded-2xl shadow-lg z-10">
                    NDK
                </div>

                <div class="bg-white border border-gray-100 rounded-[2rem] shadow-2xl p-8 lg:p-12 w-full max-w-xl hover:scale-105 transition duration-500">
                    <img src="{{ asset('storage/logo.jpg') }}"
                         alt="NDK Logo"
                         class="w-full max-w-md mx-auto object-contain">
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Login Modal -->
<div id="loginModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 relative">
        <button onclick="closeLoginModal()" class="absolute top-4 right-4 text-gray-500 hover:text-black text-xl">✕</button>

        <h2 class="text-3xl font-bold mb-2">Dashboard Login</h2>
        <p class="text-gray-500 mb-6">Enter your password to continue</p>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-600 rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('dashboard.login') }}">
            @csrf

            <input type="password" 
                   name="password"
                   class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 mb-5"
                   placeholder="Enter Password" required>

            <div class="grid grid-cols-2 gap-3">
                <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-3 rounded-2xl transition duration-300">
                    Login
                </button>

                <a href="{{ route('dashboard.password') }}"
                   class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 rounded-2xl transition duration-300">
                    Update Password
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function openLoginModal() {
        document.getElementById('loginModal').classList.remove('hidden');
        document.getElementById('loginModal').classList.add('flex');
    }

    function closeLoginModal() {
        document.getElementById('loginModal').classList.add('hidden');
        document.getElementById('loginModal').classList.remove('flex');
    }

    // auto reopen modal on validation/login error
    @if(session('error') || session('success'))
        openLoginModal();
    @endif
</script>

</body>
</html>