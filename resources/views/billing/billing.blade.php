@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="space-y-6">

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="bg-green-100 text-green-800 px-4 py-3 rounded shadow">
    {{ session('success') }}
</div>
@endif

{{-- ERROR MESSAGE --}}
@if($errors->any())
<div class="bg-red-100 text-red-800 px-4 py-3 rounded shadow">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ✅ TRIPS --}}
@include('billing.partials.trip-section')

{{-- ✅ INVOICE --}}
@include('billing.partials.invoice-section')

</div>

{{-- PASS DATA TO JS --}}
<script>
    window.invoiceNo = "{{ $nextInvoice }}";
</script>

{{-- MAIN JS --}}
<script type="module" src="{{ asset('js/billing/main.js') }}"></script>

@endsection