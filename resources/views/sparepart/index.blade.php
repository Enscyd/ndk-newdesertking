@extends('layouts.app')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="p-6 grid grid-cols-1 lg:grid-cols-4 gap-6">

<!-- CATEGORY -->
<div class="bg-white p-4 rounded shadow">
<h2 class="font-bold mb-3">Category</h2>

<form id="catForm">
<input id="cat_name" placeholder="Name" class="w-full border p-2 mb-2">
<input id="cat_desc" placeholder="Description" class="w-full border p-2 mb-2">

<button class="bg-indigo-600 text-white w-full py-2">Save</button>
</form>
</div>

<!-- SUPPLIER -->
<div class="bg-white p-4 rounded shadow">
<h2 class="font-bold mb-3">Supplier</h2>

<form id="supForm">
<input id="sup_name" placeholder="Name" class="w-full border p-2 mb-2">
<input id="sup_phone" placeholder="Phone" class="w-full border p-2 mb-2">
<input id="sup_address" placeholder="Address" class="w-full border p-2 mb-2">

<button class="bg-indigo-600 text-white w-full py-2">Save</button>
</form>
</div>

<!-- SPAREPART -->
<div class="bg-white p-4 rounded shadow">
<h2 class="font-bold mb-3">SparePart</h2>

<form id="spForm">

<input id="name" placeholder="Name" class="w-full border p-2 mb-2">
<input id="part_number" placeholder="Part No" class="w-full border p-2 mb-2">

<select id="category_id" class="w-full border p-2 mb-2">
<option value="">Select Category</option>
@foreach($categories as $c)
<option value="{{ $c->id }}">{{ $c->name }}</option>
@endforeach
</select>

<button class="bg-indigo-600 text-white w-full py-2">Save</button>

</form>
</div>

<!-- STOCK -->
<div class="bg-white p-4 rounded shadow">
<h2 class="font-bold mb-3">Stock IN/OUT</h2>

<form id="stockForm">

<select id="sparepart_id" class="w-full border p-2 mb-2">
@foreach($spareparts as $sp)
<option value="{{ $sp->id }}">{{ $sp->name }}</option>
@endforeach
</select>

<select id="supplier_id" class="w-full border p-2 mb-2">
<option value="">Select Supplier</option>
@foreach($suppliers as $s)
<option value="{{ $s->id }}">{{ $s->name }}</option>
@endforeach
</select>

<input id="quantity" type="number" placeholder="Quantity" class="w-full border p-2 mb-2">

<select id="type" class="w-full border p-2 mb-2">
<option value="IN">Stock IN</option>
<option value="OUT">Stock OUT</option>
</select>

<button class="bg-green-600 text-white w-full py-2">Save</button>

</form>
</div>

<!-- TABLE -->
<div class="bg-white p-6 rounded shadow lg:col-span-4">

<div class="flex justify-between mb-4">
<h2 class="font-bold text-lg">SpareParts List</h2>
<input id="search" placeholder="Search..." class="border px-3 py-2 rounded">
</div>

<table class="w-full border" id="table">

<thead class="bg-gray-100">
<tr>
<th class="p-2 border">Name</th>
<th class="p-2 border">Part No</th>
<th class="p-2 border">Category</th>
<th class="p-2 border">Stock</th>
<th class="p-2 border">Action</th>
</tr>
</thead>

<tbody>

@foreach($spareparts as $sp)
<tr id="row-{{ $sp->id }}">
<td class="p-2 border">{{ $sp->name }}</td>
<td class="p-2 border">{{ $sp->part_number }}</td>
<td class="p-2 border">{{ $sp->category->name ?? '-' }}</td>
<td class="p-2 border">{{ $sp->quantity }}</td>

<td class="p-2 border flex gap-2">
<button class="historyBtn bg-gray-600 text-white px-2 py-1" data-id="{{ $sp->id }}">History</button>
<button class="deleteBtn bg-red-600 text-white px-2 py-1" data-id="{{ $sp->id }}">Delete</button>
</td>
</tr>
@endforeach

</tbody>
</table>

</div>

</div>

<!-- HISTORY MODAL -->
<div id="historyModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
<div class="bg-white p-6 rounded w-2/3">

<h3 class="font-bold mb-4">Stock History</h3>

<table class="w-full border">
<thead>
<tr>
<th>Type</th>
<th>Qty</th>
<th>Supplier</th>
<th>Date</th>
</tr>
</thead>
<tbody id="historyBody"></tbody>
</table>

<button onclick="closeModal()" class="mt-4 bg-gray-500 text-white px-4 py-2">Close</button>

</div>
</div>

<script>

/* CSRF */
$.ajaxSetup({
headers:{ 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

/* TOAST */
function toast(msg){
Swal.fire({toast:true,position:'top-end',icon:'success',title:msg,showConfirmButton:false,timer:2000});
}

/* ERROR HANDLER */
function showError(err){
let msg = err.responseJSON?.message || 'Validation Error';
Swal.fire('Error', msg, 'error');
}

/* CATEGORY */
$('#catForm').submit(function(e){
e.preventDefault();

let name = $('#cat_name').val();
if(!name) return Swal.fire('Error','Category name required','error');

$.post('/sparepart-category',{
name:name,
description:$('#cat_desc').val()
},()=>location.reload()).fail(showError);
});

/* SUPPLIER */
$('#supForm').submit(function(e){
e.preventDefault();

let name = $('#sup_name').val();
if(!name) return Swal.fire('Error','Supplier name required','error');

$.post('/sparepart-supplier',{
name:name,
phone:$('#sup_phone').val(),
address:$('#sup_address').val()
},()=>location.reload()).fail(showError);
});

/* SPAREPART */
$('#spForm').submit(function(e){
e.preventDefault();

let name = $('#name').val();
let part = $('#part_number').val();

if(!name || !part)
return Swal.fire('Error','Name & Part Number required','error');

$.post('/sparepart',{
name:name,
part_number:part,
category_id:$('#category_id').val()
},()=>location.reload()).fail(showError);
});

/* STOCK */
$('#stockForm').submit(function(e){
e.preventDefault();

let qty = $('#quantity').val();
if(!qty || qty <= 0)
return Swal.fire('Error','Enter valid quantity','error');

$.post('/sparepart-stock',{
sparepart_id:$('#sparepart_id').val(),
supplier_id:$('#supplier_id').val(),
quantity:qty,
type:$('#type').val()
},function(res){
toast('Stock Updated');
location.reload();
}).fail(showError);
});

/* DELETE */
$('.deleteBtn').click(function(){
let id=$(this).data('id');

Swal.fire({title:'Delete?',showCancelButton:true}).then(r=>{
if(r.isConfirmed){
$.ajax({
url:'/sparepart/'+id,
type:'DELETE',
success:()=>location.reload()
});
}
});
});

/* SEARCH */
$('#search').keyup(function(){
let v=$(this).val().toLowerCase();
$("#table tbody tr").filter(function(){
$(this).toggle($(this).text().toLowerCase().indexOf(v)>-1)
});
});

/* HISTORY */
$('.historyBtn').click(function(){
let id=$(this).data('id');

$.get('/sparepart-stock/'+id,function(data){

let html='';
data.forEach(d=>{
html+=`
<tr>
<td>${d.type}</td>
<td>${d.quantity}</td>
<td>${d.supplier ? d.supplier.name : '-'}</td>
<td>${d.created_at}</td>
</tr>`;
});

$('#historyBody').html(html);
$('#historyModal').removeClass('hidden').addClass('flex');

});
});

function closeModal(){
$('#historyModal').addClass('hidden').removeClass('flex');
}

</script>

@endsection