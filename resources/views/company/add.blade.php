@extends('layouts.app')

@section('content')

<!-- Libraries (only include once if already in layout) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ADD COMPANY -->
    <div class="bg-white p-6 rounded-lg shadow lg:col-span-1">
        <h2 class="text-xl font-bold mb-6 text-black">New Company</h2>

        <form id="companyForm" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-black">Company Name</label>
                <input type="text" name="name" id="name"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-black"
                    placeholder="Enter company name" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-black">Address</label>
                <input type="text" name="address" id="address"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-black"
                    placeholder="Enter company address">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition">
                Save Company
            </button>
        </form>
    </div>


    <!-- COMPANY LIST -->
    <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-black">Company List</h2>

            <input type="text" id="search"
                placeholder="Search company..."
                class="border px-3 py-2 rounded w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200" id="companyTable">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border text-left w-1/3">Company Name</th>
                        <th class="p-3 border text-left">Address</th>
                        <th class="p-3 border text-left w-40">Actions</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($companies as $company)

                    <tr id="row-{{ $company->id }}">

                        <td class="p-3 border companyName">
                            {{ $company->name }}
                        </td>

                        <td class="p-3 border companyAddress">
                            {{ $company->address }}
                        </td>

                        <td class="p-3 border flex gap-2">

                            <button
                                class="editBtn bg-blue-600 text-white px-3 py-1 rounded"
                                data-id="{{ $company->id }}"
                                data-name="{{ $company->name }}"
                                data-address="{{ $company->address }}">
                                Edit
                            </button>

                            <button
                                class="deleteBtn bg-red-600 text-white px-3 py-1 rounded"
                                data-id="{{ $company->id }}">
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
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">

    <div class="bg-white p-6 rounded shadow w-96">

        <h3 class="text-lg font-bold mb-4">Edit Company</h3>

        <input type="hidden" id="editId">

        <div class="mb-3">
            <label class="block text-sm">Company Name</label>
            <input type="text" id="editName"
                class="w-full border px-3 py-2 rounded">
        </div>

        <div class="mb-4">
            <label class="block text-sm">Address</label>
            <input type="text" id="editAddress"
                class="w-full border px-3 py-2 rounded">
        </div>

        <div class="flex justify-end gap-2">

            <button onclick="closeModal()"
                class="px-4 py-2 bg-gray-400 text-white rounded">
                Cancel
            </button>

            <button id="saveEdit"
                class="px-4 py-2 bg-indigo-600 text-white rounded">
                Save
            </button>

        </div>

    </div>

</div>



<script>

$.ajaxSetup({
headers:{
'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});


/* TOAST */
function toast(msg){

Swal.fire({
toast:true,
position:'top-end',
icon:'success',
title:msg,
showConfirmButton:false,
timer:2000
});

}



/* ADD COMPANY AJAX */
$('#companyForm').submit(function(e){

e.preventDefault();

$.ajax({

url:"{{ route('company.save') }}",
type:"POST",

data:{
name:$('#name').val(),
address:$('#address').val()
},

success:function(){

toast('Company added');

location.reload();

}

});

});



/* OPEN EDIT MODAL */
$('.editBtn').click(function(){

$('#editId').val($(this).data('id'));
$('#editName').val($(this).data('name'));
$('#editAddress').val($(this).data('address'));

$('#editModal').removeClass('hidden').addClass('flex');

});


function closeModal(){
$('#editModal').removeClass('flex').addClass('hidden');
}



/* SAVE EDIT */
$('#saveEdit').click(function(){

let id = $('#editId').val();

$.ajax({

url:"{{ url('/company') }}/"+id,

type:"PUT",

data:{
name:$('#editName').val(),
address:$('#editAddress').val(),
_token:$('meta[name="csrf-token"]').attr('content')
},

success:function(){

Swal.fire({
icon:'success',
title:'Company updated',
timer:1500,
showConfirmButton:false
});

location.reload();

},

error:function(){
Swal.fire('Error','Update failed','error');
}

});

});



/* DELETE COMPANY */
$('.deleteBtn').click(function(){

let id = $(this).data('id');

Swal.fire({

title:'Delete company?',
text:'This cannot be undone',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33',
confirmButtonText:'Delete'

}).then((result)=>{

if(result.isConfirmed){

$.ajax({

url:'/company/'+id,
type:'DELETE',

success:function(){

$('#row-'+id).remove();

toast('Company deleted');

}

});

}

});

});



/* SEARCH */
$('#search').on('keyup',function(){

let value = $(this).val().toLowerCase();

$("#companyTable tbody tr").filter(function(){

$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);

});

});


</script>

@endsection