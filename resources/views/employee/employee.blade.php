@extends('layouts.app')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- jQuery + SweetAlert -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

<!-- ADD EMPLOYEE -->

<div class="bg-white p-6 rounded-lg shadow">

<h2 class="text-xl font-bold mb-6 text-black">
Add Employee
</h2>

<form id="employeeForm">

<div class="mb-3">

<label class="block text-sm font-medium text-black">
Employee Name
</label>

<input
type="text"
id="employeeName"
name="employeeName"
placeholder="Enter Employee Name"
class="mt-1 w-full border px-3 py-2 rounded text-black"
required>

</div>


<div>

<label class="block text-sm font-medium text-black">
Phone Number
</label>

<input
type="text"
id="employeePhoneNo"
name="employeePhoneNo"
placeholder="Enter Phone Number"
class="mt-1 w-full border px-3 py-2 rounded text-black">

</div>

<button
type="submit"
class="mt-4 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">

Save Employee

</button>

</form>

</div>



<!-- EMPLOYEE LIST -->

<div class="bg-white p-6 rounded-lg shadow lg:col-span-2">

<div class="flex justify-between mb-4">

<h2 class="text-xl font-bold text-black">
Employee List
</h2>

<input
type="text"
id="search"
placeholder="Search employee..."
class="border px-3 py-2 rounded w-64">

</div>


<div class="overflow-x-auto">

<table class="w-full border border-gray-200" id="employeeTable">

<thead class="bg-gray-100">

<tr>

<th class="p-3 border text-left">
Employee Name
</th>

<th class="p-3 border text-left">
Phone
</th>

<th class="p-3 border text-left w-40">
Actions
</th>

</tr>

</thead>


<tbody>

@foreach($employees as $employee)

<tr id="row-{{ $employee->id }}">

<td class="p-3 border">
{{ $employee->employeeName }}
</td>

<td class="p-3 border">
{{ $employee->employeePhoneNo }}
</td>

<td class="p-3 border flex gap-2">

<button
class="editBtn bg-blue-600 text-white px-3 py-1 rounded"
data-id="{{ $employee->id }}"
data-name="{{ $employee->employeeName }}"
data-phone="{{ $employee->employeePhoneNo }}">

Edit

</button>

<button
class="deleteBtn bg-red-600 text-white px-3 py-1 rounded"
data-id="{{ $employee->id }}">

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
Edit Employee
</h3>

<input type="hidden" id="editId">

<div class="mb-4">

<label class="block text-sm">
Employee Name
</label>

<input
type="text"
id="editName"
class="w-full border px-3 py-2 rounded">

</div>


<div class="mb-4">

<label class="block text-sm">
Phone
</label>

<input
type="text"
id="editPhone"
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


/* TOAST */

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



/* ADD EMPLOYEE */

$('#employeeForm').submit(function(e){

e.preventDefault();

$.ajax({

url:"{{ route('employee.save') }}",
type:"POST",

data:{
employeeName:$('#employeeName').val(),
employeePhoneNo:$('#employeePhoneNo').val()
},

success:function(){

toast('Employee added');

$('#employeeForm')[0].reset();

location.reload();

},

error:function(xhr){

console.log(xhr.responseText);

Swal.fire('Error','Employee not saved','error');

}

});

});



/* OPEN EDIT MODAL */

$(document).on('click','.editBtn',function(){

$('#editId').val($(this).data('id'));
$('#editName').val($(this).data('name'));
$('#editPhone').val($(this).data('phone'));

$('#editModal').removeClass('hidden').addClass('flex');

});



/* CLOSE MODAL */

window.closeModal=function(){

$('#editModal').removeClass('flex').addClass('hidden');

}



/* UPDATE */

$('#saveEdit').click(function(){

let id = $('#editId').val();

$.ajax({

url:"/employee/"+id,
type:"PUT",

data:{
employeeName:$('#editName').val(),
employeePhoneNo:$('#editPhone').val()
},

success:function(){

toast('Employee updated');

location.reload();

}

});

});



/* DELETE */

$(document).on('click','.deleteBtn',function(){

let id=$(this).data('id');

Swal.fire({

title:'Delete employee?',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
confirmButtonText:'Delete'

}).then((result)=>{

if(result.isConfirmed){

$.ajax({

url:'/employee/'+id,
type:'DELETE',

success:function(){

$('#row-'+id).remove();

toast('Employee deleted');

}

});

}

});

});



/* SEARCH */

$('#search').on('keyup',function(){

let value=$(this).val().toLowerCase();

$("#employeeTable tbody tr").filter(function(){

$(this).toggle($(this).text().toLowerCase().indexOf(value)>-1);

});

});

});

</script>

@endsection