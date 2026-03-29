$(document).ready(function(){

    console.log('JS Loaded');

    // ================= CSRF =================
    $.ajaxSetup({
        headers:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ================= TOAST =================
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

    // ================= ADD ENTRY =================
    $('#accountForm').on('submit', function(e){
        e.preventDefault();

        let employeeId = $('#employeeId').val();
        let date = $('#date').val();
        let amount = $('#amount').val();

        if(!employeeId || !date || !amount){
            Swal.fire('Error','Please fill all required fields','error');
            return;
        }

        $.ajax({
            url:'/employee-accounts',
            type:'POST',
            data:{
                employeeId: employeeId,
                date: date,
                type: $('#type').val(),
                amount: parseFloat(amount),
                remarks: $('#remarks').val()
            },
            success:function(){
                toast('Entry Added');
                $('#filterBtn').trigger('click');
            },
            error:function(xhr){
                console.log(xhr.responseText);
                Swal.fire('Error', xhr.responseText, 'error');
            }
        });

    });


    // ================= FILTER =================
    $(document).on('click', '#filterBtn', function(){

        console.log('Filter clicked');

        let employeeId = $('#filterEmployee').val();
        let month = $('#filterMonth').val();

        $.ajax({
            url:'/employee-accounts/filter',
            type:'GET',
            data:{
                employeeId: employeeId,
                month: month
            },
            success:function(res){

                let html = '';

                if(!res || !res.entries || res.entries.length === 0){
                    html = `
                        <tr>
                            <td colspan="6" class="text-center p-4 text-gray-500">
                                No data found
                            </td>
                        </tr>`;
                } else {

                    res.entries.forEach(function(e){

                        let rowColor = e.type === 'CREDIT'
                            ? 'bg-green-50 hover:bg-green-100'
                            : 'bg-red-50 hover:bg-red-100';

                        html += `
                        <tr id="row-${e.id}" class="${rowColor}">
                            <td class="p-2 border">${formatDate(e.date)}</td>

                            <td class="p-2 border font-semibold ${e.type === 'CREDIT' ? 'text-green-700' : 'text-red-700'}">
                                ${e.type}
                            </td>

                            <td class="p-2 border font-bold">
                                ${parseFloat(e.amount || 0).toFixed(2)}
                            </td>

                            <!-- ✅ BALANCE COLUMN -->
                            <td class="p-2 border font-bold text-blue-700">
                                ${parseFloat(e.running_balance || 0).toFixed(2)}
                            </td>

                            <!-- ✅ REMARKS COLUMN -->
                            <td class="p-2 border">
                                ${e.remarks ? e.remarks : ''}
                            </td>

                            <td class="p-2 border">
                                <button class="deleteBtn bg-red-600 text-white px-2 py-1 rounded"
                                    data-id="${e.id}">
                                    Delete
                                </button>
                            </td>
                        </tr>`;
                    });

                }

                $('#tableBody').html(html);

                // ================= SUMMARY =================
                $('#creditVal').text(res.credits || 0);
                $('#debitVal').text(res.debits || 0);
                $('#netVal').text(res.net || 0);

            },
            error:function(xhr){
                console.log(xhr.responseText);
                Swal.fire('Error','Filter request failed','error');
            }
        });

    });


    // ================= DELETE =================
    $(document).on('click', '.deleteBtn', function(){

        let id = $(this).data('id');

        Swal.fire({
            title:'Delete entry?',
            icon:'warning',
            showCancelButton:true
        }).then(function(result){

            if(result.isConfirmed){

                $.ajax({
                    url:'/employee-accounts/' + id,
                    type:'DELETE',
                    success:function(){
                        toast('Deleted');

                        // 🔥 Refresh ledger (important for running balance)
                        $('#filterBtn').trigger('click');
                    },
                    error:function(){
                        Swal.fire('Error','Delete failed','error');
                    }
                });

            }

        });

    });


    // ================= SEARCH =================
    $('#search').on('keyup', function(){

        let value = $(this).val().toLowerCase();

        $('#tableBody tr').filter(function(){
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );
        });

    });


    // ================= FORMAT DATE =================
    function formatDate(dateStr){
        if(!dateStr) return '';
        let d = new Date(dateStr);
        return d.toLocaleDateString('en-GB');
    }

});

$('#pdfBtn').click(function(){

    let employeeId = $('#filterEmployee').val();
    let month = $('#filterMonth').val();

    if(!employeeId || !month){
        Swal.fire('Error','Select Employee and Month','error');
        return;
    }

    window.open(`/employee-accounts/pdf?employeeId=${employeeId}&month=${month}`, '_blank');

});