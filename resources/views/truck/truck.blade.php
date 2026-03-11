@extends('layouts.app')

@section('content')

<!-- jQuery + SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

<!-- ADD TRUCK -->

<div class="bg-white p-6 rounded-lg shadow">

<h2 class="text-xl font-bold mb-6 text-black">
Add Truck
</h2>

<form id="truckForm">

@csrf

<div>

<label class="block text-sm font-medium text-black">
Truck Number
</label>

<input
type="text"
id="truckNumber"
name="truckNumber"
placeholder="Enter Truck Number"
class="mt-1 w-full border px-3 py-2 rounded text-black"
required>

</div>

<button
type="submit"
class="mt-4 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">

Save Truck

</button>

</form>

</div>



<!-- TRUCK LIST -->

<div class="bg-white p-6 rounded-lg shadow lg:col-span-2">

<div class="flex justify-between mb-4">

<h2 class="text-xl font-bold text-black">
Truck List
</h2>

<input
type="text"
id="search"
placeholder="Search truck..."
class="border px-3 py-2 rounded w-64">

</div>


<div class="overflow-x-auto">

<table class="w-full border border-gray-200" id="truckTable">

<thead class="bg-gray-100">

<tr>

<th class="p-3 border text-left w-2/3">
Truck Number
</th>

<th class="p-3 border text-left w-40">
Actions
</th>

</tr>

</thead>


<tbody>

@foreach($trucks as $truck)

<tr id="row-{{ $truck->id }}">

<td class="p-3 border truckNumber">
{{ $truck->truckNumber }}
</td>

<td class="p-3 border flex gap-2">

<button
class="editBtn bg-blue-600 text-white px-3 py-1 rounded"
data-id="{{ $truck->id }}"
data-number="{{ $truck->truckNumber }}">

Edit

</button>

<button
class="deleteBtn bg-red-600 text-white px-3 py-1 rounded"
data-id="{{ $truck->id }}">

Delete

</button>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>



<!-- EDIT MODAL -->

<div id="editModal"
class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">

<div class="bg-white p-6 rounded shadow w-96">

<h3 class="text-lg font-bold mb-4">
Edit Truck
</h3>

<input type="hidden" id="editId">

<div class="mb-4">

<label class="block text-sm">
Truck Number
</label>

<input
type="text"
id="editNumber"
class="w-full border px-3 py-2 rounded">

</div>


<div class="flex justify-end gap-2">

<button
onclick="closeModal()"
class="px-4 py-2 bg-gray-400 text-white rounded">

Cancel

</button>

<button
id="saveEdit"
class="px-4 py-2 bg-indigo-600 text-white rounded">

Save

</button>

</div>

</div>

</div>



<script>

$(document).ready(function(){

/* CSRF TOKEN */

$.ajaxSetup({

headers:{
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}

});


/* SUCCESS TOAST */

function toast(message){

Swal.fire({
toast:true,
position:'top-end',
icon:'success',
title:message,
showConfirmButton:false,
timer:2000
});

}



/* ADD TRUCK */

$('#truckForm').submit(function(e){

e.preventDefault();

let truckNumber = $('#truckNumber').val();

$.ajax({

url:"{{ route('truck.save') }}",
type:"POST",

data:{
truckNumber:truckNumber
},

success:function(){

Swal.fire({
icon:'success',
title:'Truck added',
timer:1500,
showConfirmButton:false
});

location.reload();

},

error:function(xhr){

console.log(xhr.responseText);

Swal.fire('Error','Truck not saved','error');

}

});

});



/* OPEN EDIT MODAL */

$(document).on('click','.editBtn',function(){

$('#editId').val($(this).data('id'));
$('#editNumber').val($(this).data('number'));

$('#editModal').removeClass('hidden').addClass('flex');

});



/* CLOSE MODAL */

window.closeModal=function(){

$('#editModal').removeClass('flex').addClass('hidden');

}



/* UPDATE TRUCK */

$('#saveEdit').click(function(){

let id = $('#editId').val();

$.ajax({

url:"/truck/"+id,
type:"PUT",

data:{
truckNumber:$('#editNumber').val()
},

success:function(){

toast('Truck updated');

location.reload();

}

});

});



/* DELETE TRUCK */

$(document).on('click','.deleteBtn',function(){

let id=$(this).data('id');

Swal.fire({

title:'Delete truck?',
text:'This cannot be undone',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
confirmButtonText:'Delete'

}).then((result)=>{

if(result.isConfirmed){

$.ajax({

url:'/truck/'+id,
type:'DELETE',

success:function(){

$('#row-'+id).remove();

toast('Truck deleted');

}

});

}

});

});



/* SEARCH */

$('#search').on('keyup',function(){

let value=$(this).val().toLowerCase();

$("#truckTable tbody tr").filter(function(){

$(this).toggle($(this).text().toLowerCase().indexOf(value)>-1);

});

});

});

</script>

@endsection