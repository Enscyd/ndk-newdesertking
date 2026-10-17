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
        if (typeof window.openEditAccount === 'function') {
            window.openEditAccount(this);
        } else {
            let btn = $(this);
            $('#edit_id').val(btn.data('id') || btn.attr('data-id'));
            $('#edit_purpose_id').val(btn.data('purpose-id') || btn.attr('data-purpose-id'));
            $('#edit_description').val(btn.data('description') !== undefined ? btn.data('description') : (btn.attr('data-description') || ''));
            $('#edit_type').val(btn.data('type') || btn.attr('data-type'));
            $('#edit_amount').val(btn.data('amount') || btn.attr('data-amount'));
            $('#edit_date').val(btn.data('date') || btn.attr('data-date'));
            $('#editAccountModal').css('display', 'flex');
        }
    });

    /*
    |--------------------------------------------------------------------------
    | CLOSE EDIT MODAL
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.close-edit-modal', function () {
        if (typeof window.closeAccountModal === 'function') {
            window.closeAccountModal();
        } else {
            $('#editAccountModal').css('display', 'none');
        }
    });

    // Close on backdrop click
    $('#editAccountModal').on('click', function (e) {
        if (e.target === this) {
            if (typeof window.closeAccountModal === 'function') {
                window.closeAccountModal();
            } else {
                $(this).css('display', 'none');
            }
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
                if (typeof window.closeAccountModal === 'function') {
                    window.closeAccountModal();
                } else {
                    $('#editAccountModal').css('display', 'none');
                }

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