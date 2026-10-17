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
    | EDIT ACCOUNT (OPEN MODAL)
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.edit-btn', function () {
        let btn = $(this);
        let id = btn.data('id');
        let purposeId = btn.data('purpose-id');
        let description = btn.data('description');
        let type = btn.data('type');
        let amount = btn.data('amount');
        let date = btn.data('date');

        $('#edit_id').val(id);
        $('#edit_purpose_id').val(purposeId);
        $('#edit_description').val(description);
        $('#edit_type').val(type);
        $('#edit_amount').val(amount);
        $('#edit_date').val(date);

        $('#editAccountModal').removeClass('hidden').addClass('flex');
    });

    /*
    |--------------------------------------------------------------------------
    | CLOSE EDIT MODAL
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.close-edit-modal', function () {
        $('#editAccountModal').removeClass('flex').addClass('hidden');
    });

    // Close on backdrop click
    $('#editAccountModal').on('click', function (e) {
        if (e.target === this) {
            $(this).removeClass('flex').addClass('hidden');
        }
    });

    /*
    |--------------------------------------------------------------------------
    | UPDATE ACCOUNT (AJAX)
    |--------------------------------------------------------------------------
    */
    $('#editAccountForm').submit(function (e) {
        e.preventDefault();

        let id = $('#edit_id').val();
        let amount = $('#edit_amount').val();
        let type = $('#edit_type').val();

        if (!amount || !type) {
            Swal.fire('Error', 'Type and Amount are required', 'error');
            return;
        }

        Swal.fire({
            title: 'Updating...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: accountIndexUrl + '/' + id,
            type: "PUT",
            data: $(this).serialize(),

            success: function (res) {
                $('#editAccountModal').removeClass('flex').addClass('hidden');

                Swal.fire({
                    icon: 'success',
                    title: res.message || 'Updated successfully',
                    timer: 1000,
                    showConfirmButton: false
                });

                fetchLedger();
            },

            error: function (xhr) {
                let msg = 'Update failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
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