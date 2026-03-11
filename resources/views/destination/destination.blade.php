@extends('layouts.app')

@section('content')

<!-- jQuery + SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

<!-- ADD DESTINATION -->
<div class="bg-white p-6 rounded-lg shadow">

<h2 class="text-xl font-bold mb-6 text-black">
Add Destination
</h2>

<form id="destinationForm">

@csrf

<div>
<label class="block text-sm font-medium text-black">
Destination Name
</label>

<input
type="text"
id="destinationName"
name="name"
class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-black"
placeholder="Enter destination name"
required>
</div>

<button
type="submit"
class="mt-4 w-full bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700">
Save Destination
</button>

</form>

</div>



<!-- DESTINATION LIST -->
<div class="bg-white p-6 rounded-lg shadow lg:col-span-2">

<div class="flex justify-between mb-4">

<h2 class="text-xl font-bold text-black">
Destination List
</h2>

<input
type="text"
id="search"
placeholder="Search destination..."
class="border px-3 py-2 rounded w-64">

</div>

<div class="overflow-x-auto">

<table class="w-full border border-gray-200" id="destinationTable">

<thead class="bg-gray-100">

<tr>
<th class="p-3 border text-left w-2/3">
Destination
</th>

<th class="p-3 border text-left w-40">
Actions
</th>
</tr>

</thead>

<tbody>

@foreach($destinations as $destination)

<tr id="row-{{ $destination->id }}">

<td class="p-3 border destinationName">
{{ $destination->name }}
</td>

<td class="p-3 border flex gap-2">

<button
class="editBtn bg-blue-600 text-white px-3 py-1 rounded"
data-id="{{ $destination->id }}"
data-name="{{ $destination->name }}">
Edit
</button>

<button
class="deleteBtn bg-red-600 text-white px-3 py-1 rounded"
data-id="{{ $destination->id }}">
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
Edit Destination
</h3>

<input type="hidden" id="editId">

<div class="mb-4">
<label class="block text-sm">
Destination Name
</label>

<input
type="text"
id="editName"
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



/* ADD DESTINATION */

$('#destinationForm').submit(function(e){

e.preventDefault();

let name = $('#destinationName').val();

$.ajax({

url:"{{ route('destination.save') }}",
type:"POST",

data:{
name:name
},

success:function(){

Swal.fire({
icon:'success',
title:'Destination added',
timer:1500,
showConfirmButton:false
});

location.reload();

},

error:function(xhr){
console.log(xhr.responseText);
Swal.fire('Error','Destination not saved','error');
}

});

});



/* OPEN EDIT MODAL */

$(document).on('click','.editBtn',function(){

$('#editId').val($(this).data('id'));
$('#editName').val($(this).data('name'));

$('#editModal').removeClass('hidden').addClass('flex');

});



/* CLOSE MODAL */

window.closeModal=function(){

$('#editModal').removeClass('flex').addClass('hidden');

}



/* UPDATE DESTINATION */

$('#saveEdit').click(function(){

let id = $('#editId').val();

$.ajax({

url:"/destination/"+id,
type:"PUT",

data:{
name:$('#editName').val()
},

success:function(){

toast('Destination updated');
location.reload();

}

});

});



/* DELETE DESTINATION */

$(document).on('click','.deleteBtn',function(){

let id=$(this).data('id');

Swal.fire({

title:'Delete destination?',
text:'This cannot be undone',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
confirmButtonText:'Delete'

}).then((result)=>{

if(result.isConfirmed){

$.ajax({

url:'/destination/'+id,
type:'DELETE',

success:function(){

$('#row-'+id).remove();
toast('Destination deleted');

}

});

}

});

});



/* SEARCH */

$('#search').on('keyup',function(){

let value=$(this).val().toLowerCase();

$("#destinationTable tbody tr").filter(function(){

$(this).toggle($(this).text().toLowerCase().indexOf(value)>-1);

});

});

});

</script>

@endsection