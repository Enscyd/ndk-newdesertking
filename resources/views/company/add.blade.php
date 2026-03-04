@extends('layouts.app')

@section('content')

<div class="p-6">  <!-- Top-left aligned container -->

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6 text-black">New Company</h2>

        @if(session('message'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-md border border-green-200">
                {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-md border border-red-200 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('company.save') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-black">Company Name</label>

                <input type="text" name="name"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md
                           shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500
                           text-black"
                    placeholder="Enter company name"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md
                       hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2
                       focus:ring-indigo-500 transition duration-150">
                Save Company
            </button>
        </form>
    </div>

</div>

@endsection