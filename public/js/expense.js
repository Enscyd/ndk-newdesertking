$(document).ready(function(){

/* CSRF TOKEN */

$.ajaxSetup({
headers:{
'X-CSRF-TOKEN':$('meta[name="csrf-token"]').attr('content')
}
});


/* IMAGE PREVIEW */

$('#imageInput').on('change',function(){

let reader = new FileReader();

reader.onload = function(e){

$('#previewImage')
.attr('src',e.target.result)
.removeClass('hidden');

}

reader.readAsDataURL(this.files[0]);

});


/* SAVE EXPENSE */

$('#expenseForm').submit(function(e){

e.preventDefault();

let formData = new FormData(this);

let id = $('#expenseId').val();

let url = id ? '/expense/'+id : '/expense';
let method = id ? 'POST' : 'POST';

if(id){
formData.append('_method','PUT');
}

$.ajax({

url:url,
method:method,
data:formData,
processData:false,
contentType:false,

success:function(){

Swal.fire(
'Success',
'Expense saved successfully',
'success'
);

location.reload();

}

});

});


/* EDIT EXPENSE */

$(document).on('click','.editBtn',function(){

let id = $(this).data('id');

$.get('/expense/'+id+'/edit',function(data){

$('#expenseId').val(data.id);

$('select[name="employeeId"]').val(data.employeeId);

$('select[name="truckId"]').val(data.truckId);

$('input[name="expenseDate"]').val(data.expenseDate);

$('select[name="category"]').val(data.category);

$('input[name="details"]').val(data.details);

$('input[name="amount"]').val(data.amount);

});

});


/* DELETE EXPENSE */

$(document).on('click','.deleteBtn',function(){

let id = $(this).data('id');

Swal.fire({

title:'Delete expense?',

icon:'warning',

showCancelButton:true,

confirmButtonColor:'#d33',

confirmButtonText:'Delete'

}).then((result)=>{

if(result.isConfirmed){

$.ajax({

url:'/expense/'+id,

method:'DELETE',

success:function(){

Swal.fire(
'Deleted',
'Expense deleted',
'success'
);

location.reload();

}

});

}

});

});


/* SEARCH */

$('#search').on('keyup',function(){

let value = $(this).val().toLowerCase();

$('#expenseTableBody tr').filter(function(){

$(this).toggle(

$(this).text().toLowerCase().indexOf(value) > -1

);

});

});


/* FILTER */

$('#filterBtn').click(function(){

let date = $('#dateFilter').val();

let category = $('#categoryFilter').val();

$.get('/expense/filter',{

date:date,
category:category

},function(data){

$('#expenseTableBody').html(data);

});

});


/* IMAGE POPUP */

$(document).on('click','#expenseTableBody img',function(){

$('#popupImage').attr('src',$(this).attr('src'));

$('#imageModal').removeClass('hidden').addClass('flex');

});

$('#imageModal').click(function(){

$(this).addClass('hidden').removeClass('flex');

});

});