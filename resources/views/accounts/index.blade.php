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
            <input type="number" step="0.001" min="0" placeholder="0.000" name="amount" class="w-full border p-2 mb-2" required>

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
                    OPENING : {{ number_format($opening, 3) }}
                </span>

                <span class="text-green-600 ml-4">
                    INCOME : {{ number_format($income, 3) }}
                </span>

                <span class="text-red-600 ml-4">
                    EXPENSE : {{ number_format($expense, 3) }}
                </span>

                <span class="ml-4">
                    NET BALANCE : {{ number_format($net, 3) }}
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

<!-- EDIT ACCOUNT MODAL -->
<div id="editAccountModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg overflow-hidden">
        <!-- MODAL HEADER -->
        <div class="flex justify-between items-center px-6 py-4 bg-gray-100 border-b">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                ✏️ Edit Account Transaction
            </h3>
            <button type="button" class="close-edit-modal text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none">&times;</button>
        </div>

        <!-- MODAL BODY -->
        <form id="editAccountForm" class="p-6 space-y-4">
            <input type="hidden" id="edit_id" name="id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                <select id="edit_purpose_id" name="purpose_id" required class="w-full border rounded p-2">
                    @foreach($purposes as $p)
                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="edit_description" name="description" rows="2" class="w-full border rounded p-2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select id="edit_type" name="type" required class="w-full border rounded p-2">
                        <option value="INCOME">INCOME</option>
                        <option value="EXPENSE">EXPENSE</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" step="0.001" min="0" placeholder="0.000" id="edit_amount" name="amount" required class="w-full border rounded p-2">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="edit_date" name="date" required class="w-full border rounded p-2">
            </div>

            <!-- MODAL FOOTER -->
            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" class="close-edit-modal px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded font-medium transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium shadow transition">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@endsection