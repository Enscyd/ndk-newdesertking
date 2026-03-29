$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | CSRF SETUP
    |--------------------------------------------------------------------------
    */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    /*
    |--------------------------------------------------------------------------
    | SAVE ACCOUNT (AJAX)
    |--------------------------------------------------------------------------
    */
    $('#accountForm').submit(function (e) {

        e.preventDefault();

        if (!$('[name="amount"]').val() || !$('[name="type"]').val()) {
            Swal.fire('Error', 'Type and Amount are required', 'error');
            return;
        }

        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: accountStoreUrl,
            type: "POST",
            data: $(this).serialize(),

            success: function (res) {

                Swal.fire({
                    icon: 'success',
                    title: res.message,
                    timer: 1000,
                    showConfirmButton: false
                });

                $('#accountForm')[0].reset();

                fetchLedger();
            },

            error: function () {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        });

    });


    /*
    |--------------------------------------------------------------------------
    | FILTER (AJAX - BUTTON ONLY)
    |--------------------------------------------------------------------------
    */
    $('#filterForm').submit(function (e) {

        e.preventDefault();

        let formData = $(this).serialize();

        loadLedger(formData);
    });


    /*
    |--------------------------------------------------------------------------
    | DELETE ACCOUNT (NEW)
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.delete-btn', function () {

        let id = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "This record will be deleted",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({
                    url: accountIndexUrl + '/' + id,
                    type: 'DELETE',

                    success: function (res) {

                        Swal.fire({
                            icon: 'success',
                            title: res.message,
                            timer: 1000,
                            showConfirmButton: false
                        });

                        fetchLedger(); // refresh after delete
                    },

                    error: function () {
                        Swal.fire('Error', 'Delete failed', 'error');
                    }
                });

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | LOAD LEDGER (COMMON FUNCTION)
    |--------------------------------------------------------------------------
    */
    function loadLedger(params = '') {

        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.get(accountIndexUrl + '?' + params, function (data) {

            let html = $(data).find('#ledgerContent').html();

            $('#ledgerContent').html(html);

            Swal.close();

        }).fail(function () {
            Swal.fire('Error', 'Failed to load data', 'error');
        });
    }


    /*
    |--------------------------------------------------------------------------
    | REFRESH AFTER SAVE / DELETE
    |--------------------------------------------------------------------------
    */
    function fetchLedger() {

        let params = $('#filterForm').serialize();

        $.get(accountIndexUrl + '?' + params, function (data) {

            let html = $(data).find('#ledgerContent').html();

            $('#ledgerContent').html(html);

        });

    }

});