@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- ROUTES FOR JS -->
<script>
    const accountStoreUrl = "{{ route('accounts.store') }}";
    const accountIndexUrl = "{{ route('accounts.index') }}";
</script>

<!-- LIBRARIES -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- CUSTOM JS -->
<script src="{{ asset('js/accounts.js') }}"></script>


<div class="grid grid-cols-3 gap-6 p-6">

    <!-- LEFT FORM -->
    <div class="bg-gray-100 p-4 rounded shadow">

        <h2 class="text-blue-600 font-bold mb-4">💾 Accounts</h2>

        <form id="accountForm">

            <label>Purpose</label>
            <select name="purpose_id" class="w-full border p-2 mb-2">
                @foreach($purposes as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
            </select>

            <label>Description</label>
            <textarea name="description" class="w-full border p-2 mb-2"></textarea>

            <label>Type</label>
            <select name="type" class="w-full border p-2 mb-2">
                <option value="">--Select--</option>
                <option value="INCOME">INCOME</option>
                <option value="EXPENSE">EXPENSE</option>
            </select>

            <label>Amount</label>
            <input type="number" name="amount" class="w-full border p-2 mb-2">

            <label>Date</label>
            <input type="date" name="date" class="w-full border p-2 mb-2">

            <button type="submit" class="w-full bg-blue-600 text-white p-2">
                Save
            </button>

        </form>

    </div>


    <!-- RIGHT PANEL -->
    <div class="bg-white p-4 rounded shadow col-span-2">

        <!-- FILTER -->
        <form method="GET" id="filterForm" class="flex gap-2 mb-4">

            <select name="month" class="border p-2">
                <option value="">Month</option>
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" {{ ($month ?? '') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0,0,0,$m,1)) }}
                    </option>
                @endfor
            </select>

            <select name="year" class="border p-2">
                @for($y = now()->year; $y >= now()->year - 5; $y--)
                    <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <select name="purpose_id" class="border p-2">
                <option value="">Purpose</option>
                @foreach($purposes as $p)
                    <option value="{{ $p->id }}" {{ request('purpose_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>

            <select name="type" class="border p-2">
                <option value="">Type</option>
                <option value="INCOME" {{ request('type')=='INCOME'?'selected':'' }}>INCOME</option>
                <option value="EXPENSE" {{ request('type')=='EXPENSE'?'selected':'' }}>EXPENSE</option>
            </select>

            <button type="submit" class="bg-blue-600 text-white px-4">
                Apply
            </button>

            <a href="{{ route('accounts.index') }}" class="bg-gray-400 text-white px-4 flex items-center">
                Reset
            </a>

            <a href="{{ route('accounts.pdf', request()->all()) }}" 
   target="_blank"
   class="bg-green-600 text-white px-4 flex items-center">
    Export PDF
</a>

        </form>


        <!-- AJAX WRAPPER -->
        <div id="ledgerContent">

            <!-- SUMMARY -->
            <div class="mb-3 font-semibold">

                <span class="text-blue-700">
                    OPENING : {{ number_format($opening, 2) }}
                </span>

                <span class="text-green-600 ml-4">
                    INCOME : {{ number_format($income, 2) }}
                </span>

                <span class="text-red-600 ml-4">
                    EXPENSE : {{ number_format($expense, 2) }}
                </span>

                <span class="ml-4">
                    NET BALANCE : {{ number_format($net, 2) }}
                </span>

            </div>


            <!-- TABLE -->
            <table class="w-full border">

                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Type</th>
                        <th class="p-2 border">Description</th>
                        <th class="p-2 border">Purpose</th>
                        <th class="p-2 border text-right">Amount</th>
                        <th class="p-2 border text-right">Balance</th>
                        <th class="p-2 border text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @include('accounts.partials.account')
                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection