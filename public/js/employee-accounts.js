$(document).ready(function () {
    console.log('JS Loaded');

    // ================= CSRF =================
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ================= TOAST =================
    function toast(msg, icon = 'success') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: msg,
            showConfirmButton: false,
            timer: 2000
        });
    }

    // ================= FORMAT DATE =================
    function formatDate(dateStr) {
        if (!dateStr) return '';

        let cleanDate = dateStr.substring(0, 10);
        let parts = cleanDate.split('-');

        if (parts.length === 3) {
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        return cleanDate;
    }

    // ================= MONTH NAME =================
    function getMonthName(month) {
        const months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        return months[month - 1] || '';
    }

    // ================= ADD ENTRY =================
    $('#accountForm').on('submit', function (e) {
        e.preventDefault();

        let employeeId = $('#employeeId').val();
        let month = $('#month').val();
        let date = $('#date').val();
        let amount = $('#amount').val();

        if (!employeeId || !month || !date || !amount) {
            Swal.fire('Error', 'Please fill all required fields', 'error');
            return;
        }

        $.ajax({
            url: '/employee-accounts',
            type: 'POST',
            data: {
                employeeId: employeeId,
                month: month,
                date: date,
                type: $('#type').val(),
                amount: parseFloat(amount),
                remarks: $('#remarks').val()
            },
            success: function () {
                toast('Entry Added');

                // reset form
                $('#accountForm')[0].reset();

                // auto apply selected employee/month in filter
                $('#filterEmployee').val(employeeId);
                $('#filterMonth').val(
                    `${new Date(date).getFullYear()}-${String(month).padStart(2, '0')}`
                );

                $('#filterBtn').trigger('click');
            },
            error: function (xhr) {
                console.log(xhr.responseText);

                let errorMessage = 'Save failed';

                if (xhr.responseJSON?.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });

    // ================= FILTER =================
    $(document).on('click', '#filterBtn', function () {
        console.log('Filter clicked');

        let employeeId = $('#filterEmployee').val();
        let filterMonth = $('#filterMonth').val();

        // extract month number from YYYY-MM
        let month = '';
        if (filterMonth) {
            month = parseInt(filterMonth.split('-')[1]);
        }

        $.ajax({
            url: '/employee-accounts/filter',
            type: 'GET',
            data: {
                employeeId: employeeId,
                month: month
            },
            success: function (res) {
                let html = '';

                if (!res || !res.entries || res.entries.length === 0) {
                    html = `
                        <tr>
                            <td colspan="7" class="text-center p-4 text-gray-500">
                                No data found
                            </td>
                        </tr>`;
                } else {
                    res.entries.forEach(function (e) {
                        let rowColor = e.type === 'CREDIT'
                            ? 'bg-green-50 hover:bg-green-100'
                            : 'bg-red-50 hover:bg-red-100';

                        html += `
                        <tr id="row-${e.id}" class="${rowColor}">
                            <td class="p-2 border">${getMonthName(e.month)}</td>

                            <td class="p-2 border">${formatDate(e.date)}</td>

                            <td class="p-2 border font-semibold ${e.type === 'CREDIT' ? 'text-green-700' : 'text-red-700'}">
                                ${e.type}
                            </td>

                            <td class="p-2 border font-bold">
                                ${parseFloat(e.amount || 0).toFixed(2)}
                            </td>

                            <td class="p-2 border font-bold text-blue-700">
                                ${parseFloat(e.running_balance || 0).toFixed(2)}
                            </td>

                            <td class="p-2 border">
                                ${e.remarks || ''}
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
                $('#creditVal').text(parseFloat(res.credits || 0).toFixed(2));
                $('#debitVal').text(parseFloat(res.debits || 0).toFixed(2));
                $('#netVal').text(parseFloat(res.net || 0).toFixed(2));
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                Swal.fire('Error', 'Filter request failed', 'error');
            }
        });
    });

    // ================= DELETE =================
    $(document).on('click', '.deleteBtn', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: 'Delete entry?',
            icon: 'warning',
            showCancelButton: true
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/employee-accounts/' + id,
                    type: 'DELETE',
                    success: function () {
                        toast('Deleted');
                        $('#filterBtn').trigger('click');
                    },
                    error: function () {
                        Swal.fire('Error', 'Delete failed', 'error');
                    }
                });
            }
        });
    });

    // ================= SEARCH =================
    $('#search').on('keyup', function () {
        let value = $(this).val().toLowerCase();

        $('#tableBody tr').filter(function () {
            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );
        });
    });

    // ================= PDF =================
    $(document).on('click', '#pdfBtn', function () {
        let employeeId = $('#filterEmployee').val();
        let filterMonth = $('#filterMonth').val();

        if (!employeeId) {
            employeeId = $('#employeeId').val();
        }

        if (!employeeId) {
            Swal.fire('Error', 'Select Employee', 'error');
            return;
        }

        let pdfUrl = `/employee-accounts/pdf?employeeId=${employeeId}`;

        if (filterMonth) {
            let month = parseInt(filterMonth.split('-')[1]);
            pdfUrl += `&month=${month}`;
        }

        window.open(pdfUrl, '_blank');
    });
});