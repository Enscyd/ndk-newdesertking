@extends('layouts.app')

@section('content')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- ADD SUGGESTION -->
    <div class="bg-white p-6 rounded-lg shadow lg:col-span-1">
        <h2 class="text-xl font-bold mb-6 text-black">New Suggestion</h2>

        <form id="suggestionForm" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-black">Suggestion Name</label>
                <input type="text" id="name"
                    class="mt-1 block w-full px-3 py-2 border rounded-md text-black"
                    placeholder="Enter suggestion name" required>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">
                Save Suggestion
            </button>
        </form>
    </div>


    <!-- SUGGESTION LIST -->
    <div class="bg-white p-6 rounded-lg shadow lg:col-span-2">

        <div class="flex justify-between mb-4">
            <h2 class="text-xl font-bold text-black">Suggestion List</h2>

            <input type="text" id="search"
                placeholder="Search suggestion..."
                class="border px-3 py-2 rounded w-64">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border" id="suggestionTable">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-3 border text-left">Suggestion Name</th>
                        <th class="p-3 border text-left w-40">Actions</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($suggestions as $suggestion)

                    <tr id="row-{{ $suggestion->id }}">

                        <td class="p-3 border suggestionName">
                            {{ $suggestion->name }}
                        </td>

                        <td class="p-3 border flex gap-2">

                            <button
                                class="editBtn bg-blue-600 text-white px-3 py-1 rounded"
                                data-id="{{ $suggestion->id }}"
                                data-name="{{ $suggestion->name }}">
                                Edit
                            </button>

                            <button
                                class="deleteBtn bg-red-600 text-white px-3 py-1 rounded"
                                data-id="{{ $suggestion->id }}">
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

        <h3 class="text-lg font-bold mb-4">Edit Suggestion</h3>

        <input type="hidden" id="editId">

        <div class="mb-4">
            <label class="block text-sm">Suggestion Name</label>
            <input type="text" id="editName"
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


/* ADD */
$('#suggestionForm').submit(function(e){

e.preventDefault();

$.ajax({
url:"{{ route('suggestion.save') }}",
type:"POST",
data:{
name:$('#name').val()
},
success:function(){
toast('Suggestion added');
location.reload();
}
});

});


/* EDIT OPEN */
$('.editBtn').click(function(){

$('#editId').val($(this).data('id'));
$('#editName').val($(this).data('name'));

$('#editModal').removeClass('hidden').addClass('flex');

});


function closeModal(){
$('#editModal').removeClass('flex').addClass('hidden');
}


/* UPDATE */
$('#saveEdit').click(function(){

let id = $('#editId').val();

$.ajax({
url:"{{ url('/suggestion') }}/"+id,
type:"PUT",
data:{
name:$('#editName').val()
},
success:function(){
Swal.fire({
icon:'success',
title:'Suggestion updated',
timer:1500,
showConfirmButton:false
});
location.reload();
}
});

});


/* DELETE */
$('.deleteBtn').click(function(){

let id = $(this).data('id');

Swal.fire({
title:'Delete suggestion?',
icon:'warning',
showCancelButton:true,
confirmButtonColor:'#d33'
}).then((result)=>{

if(result.isConfirmed){

$.ajax({
url:'/suggestion/'+id,
type:'DELETE',
success:function(){
$('#row-'+id).remove();
toast('Suggestion deleted');
}
});

}

});

});


/* SEARCH */
$('#search').on('keyup',function(){

let value = $(this).val().toLowerCase();

$("#suggestionTable tbody tr").filter(function(){
$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
});

});

</script>

@endsection