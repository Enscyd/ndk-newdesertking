@extends('layouts.app')

@section('content')

<h2 class="text-lg font-semibold mb-4 text-center">
Add Missing Trip to Invoice {{ $billing->invoiceNo }}
</h2>

<form method="POST" action="{{ route('billing.trip.store',$billing->id) }}">

@csrf

<div class="bg-white p-5 rounded shadow max-w-lg mx-auto space-y-4">

<!-- Destination -->

<div>

<label class="block text-sm font-semibold">Destination</label>

<select
name="destinationId"
required
class="w-full border rounded px-3 py-2">

<option value="">Select Destination</option>

@foreach($destinations as $destinations)

<option value="{{ $destination->id }}">

{{ $destinations->name }}

</option>

@endforeach

</select>

</div>


<!-- Vehicle -->

<div>

<label class="block text-sm font-semibold">Vehicle</label>

<select
name="truckId"
required
class="w-full border rounded px-3 py-2">

<option value="">Select Vehicle</option>

@foreach($trucks as $truck)

<option value="{{ $truck->id }}">

{{ $truck->truckNumber }}

</option>

@endforeach

</select>

</div>


<!-- Quantity -->

<div>

<label class="block text-sm font-semibold">Quantity</label>

<input
type="number"
name="qty"
value="1"
min="1"
class="w-full border rounded px-3 py-2">

</div>


<!-- Rent -->

<div>

<label class="block text-sm font-semibold">Rent</label>

<input
type="number"
step="0.01"
name="rent"
required
class="w-full border rounded px-3 py-2">

</div>


<button
type="submit"
class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">

Save Trip

</button>

</div>

</form>

@endsection