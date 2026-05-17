<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Dashboard Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 py-10 px-4">

    <div class="max-w-xl mx-auto bg-white shadow-2xl rounded-3xl p-8 border border-gray-100">

        <!-- Page Title -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Update Dashboard Password</h2>
            <p class="text-gray-500 mt-2">
                Change your dashboard login password securely.
            </p>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-2xl border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-2xl border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-200">
                <ul class="space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Password Form -->
        <form method="POST" action="{{ route('dashboard.password.update') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Current Password
                </label>
                <input type="password"
                       name="current_password"
                       placeholder="Enter current password"
                       class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    New Password
                </label>
                <input type="password"
                       name="new_password"
                       placeholder="Enter new password"
                       class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Confirm New Password
                </label>
                <input type="password"
                       name="new_password_confirmation"
                       placeholder="Confirm new password"
                       class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                       required>
            </div>

            <p class="text-sm text-gray-500">
                Password must be at least <span class="font-semibold">6 characters</span>.
            </p>

            <!-- Buttons -->
            <div class="grid grid-cols-2 gap-4 pt-2">
                <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-2xl font-bold transition duration-300 shadow-lg hover:shadow-xl">
                    Update Password
                </button>

                <a href="/"
                   class="w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-3 rounded-2xl font-bold transition duration-300">
                    Back Dashboard
                </a>
            </div>
        </form>
    </div>

</body>
</html>