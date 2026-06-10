$(document).ready(function(){

    // CSRF setup
    $.ajaxSetup({
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });


    /* ===============================
       SET CURRENT MONTH ON PAGE LOAD
    =============================== */

    function setDefaultMonthFilter() {
        const defaultMonth = $('#monthFilter').data('default-month') || '';
        $('#monthFilter').val(defaultMonth);
        $('#dateFilter').val('');
    }

    setDefaultMonthFilter();


    /* ===============================
       LOAD TRIPS (AJAX PAGINATION)
    =============================== */

    function loadTrips(page = 1){

        $.ajax({

            url:'/fetch-trips?page=' + page,

            data:{
                search: $('#search').val(),
                date: $('#dateFilter').val(),
                month: $('#monthFilter').val(),
                company: $('#companyFilter').val()
            },

            success:function(res){
                $('#tripTableBody').html(res);
            },

            error:function(){
                Swal.fire('Error','Failed loading trips','error');
            }

        });

    }


    // Load current month's trips on first page load
    loadTrips();



    /* ===============================
       PAGINATION CLICK
    =============================== */

    $(document).on('click','.pagination a',function(e){

        e.preventDefault();

        let page = $(this).attr('href').split('page=')[1];

        loadTrips(page);

    });



    /* ===============================
       FILTER INDEPENDENCE
    =============================== */

    $('#monthFilter').change(function(){
        if($(this).val() !== ''){
            $('#dateFilter').val('');
        }
        loadTrips();
    });

    $('#dateFilter').change(function(){
        if($(this).val() !== ''){
            $('#monthFilter').val('');
        } else {
            setDefaultMonthFilter();
        }
        loadTrips();
    });



    /* ===============================
       IMAGE PREVIEW
    =============================== */

    $('#imageInput').change(function(){

        if(this.files && this.files[0]){

            let reader = new FileReader();

            reader.onload = function(e){

                $('#previewImage')
                    .attr('src',e.target.result)
                    .removeClass('hidden');

            };

            reader.readAsDataURL(this.files[0]);

        }

    });



    /* ===============================
       IMAGE POPUP
    =============================== */

    $(document).on('click','.tripImage',function(){

        let src=$(this).data('src');

        $('#popupImage').attr('src',src);

        $('#imageModal')
            .removeClass('hidden')
            .addClass('flex');

    });


    $('#imageModal').click(function(){

        $(this)
            .addClass('hidden')
            .removeClass('flex');

    });



    /* ===============================
       TRIP TYPE TOGGLE DRIVER AMOUNT
    =============================== */

    $('#tripType').change(function(){

        if($(this).val()=="Go Trip"){

            $('#driverAmountBox').show();

        } else {

            $('#driverAmountBox').hide();

            $('input[name="driverAmount"]').val('');

        }

    });



    /* ===============================
       OMANI FIELDS TOGGLE
    =============================== */

    $('#isOmani').change(function(){

        if($(this).val()=="Yes"){

            $('.omaniFields').removeClass('hidden');

        } else {

            $('.omaniFields').addClass('hidden');

            $('input[name="omaniName"]').val('');

            $('input[name="omaniAmount"]').val('');

        }

    });



    /* ===============================
       ADD / UPDATE TRIP
    =============================== */

    $('#tripForm').submit(function(e){

        e.preventDefault();

        let id = $('#tripId').val();

        let formData = new FormData(this);

        let url = '/trip';

        let method = 'POST';

        if(id){

            url = '/trip/'+id;

            formData.append('_method','PUT');

        }

        $.ajax({

            url: url,

            type: method,

            data: formData,

            processData: false,

            contentType: false,

            success: function(res){

                Swal.fire({
                    icon:'success',
                    title: id ? 'Trip Updated' : 'Trip Added',
                    timer:1200,
                    showConfirmButton:false
                });

                // Reset form
                $('#tripForm')[0].reset();

                $('#tripId').val('');

                $('#driverAmountBox').show();

                $('.omaniFields').addClass('hidden');

                $('#previewImage').addClass('hidden');

                // Reset to current month filter after form reset
                setDefaultMonthFilter();

                // Reload table
                loadTrips();

            },

            error: function(xhr){

                let msg = 'Something went wrong';

                if(xhr.responseJSON && xhr.responseJSON.errors){

                    msg = Object.values(xhr.responseJSON.errors)
                        .flat()
                        .join('<br>');

                }

                Swal.fire('Error', msg, 'error');

            }

        });

    });



    /* ===============================
       EDIT TRIP
    =============================== */

    $(document).on('click','.editBtn',function(){

        let id = $(this).data('id');

        $.get('/trip/'+id+'/edit', function(data){

            $('#tripId').val(data.id);

            $('select[name="companyId"]').val(data.companyId);
            $('select[name="destinationId"]').val(data.destinationId);
            $('select[name="employeeId"]').val(data.employeeId);
            $('select[name="truckId"]').val(data.truckId);

            $('select[name="tripType"]').val(data.tripType);

            $('input[name="driverAmount"]').val(data.driverAmount);
            $('input[name="tripDate"]').val(
                data.tripDate ? String(data.tripDate).substring(0, 10) : ''
            );
            $('input[name="tripAmount"]').val(data.tripAmount);

            $('select[name="isOmani"]').val(data.isOmani);
            $('input[name="omaniName"]').val(data.omaniName);
            $('input[name="omaniAmount"]').val(data.omaniAmount);


            if(data.tripType=="Go Trip"){
                $('#driverAmountBox').show();
            }
            else{
                $('#driverAmountBox').hide();
            }

            if(data.isOmani=="Yes"){
                $('.omaniFields').removeClass('hidden');
            }
            else{
                $('.omaniFields').addClass('hidden');
            }

            $('html, body').animate({
                scrollTop: $("#tripForm").offset().top
            },500);

        });

    });



    /* ===============================
       DELETE TRIP
    =============================== */

    $(document).on('click','.deleteBtn',function(){

        let id = $(this).data('id');

        Swal.fire({

            title:'Delete Trip?',

            text:'This action cannot be undone',

            icon:'warning',

            showCancelButton:true,

            confirmButtonColor:'#d33'

        }).then((result)=>{

            if(result.isConfirmed){

                $.ajax({

                    url:'/trip/'+id,

                    type:'DELETE',

                    success:function(res){

                        loadTrips();

                        Swal.fire('Deleted!','Trip removed','success');

                    },

                    error:function(){

                        Swal.fire('Error','Delete failed','error');

                    }

                });

            }

        });

    });



    /* ===============================
       SEARCH TABLE (AJAX)
    =============================== */

    $('#search').on('keyup', function(){
        loadTrips();
    });



    /* ===============================
       FILTER TRIPS
    =============================== */

    $('#filterBtn').click(function(){
        loadTrips();
    });



    /* ===============================
       EXPORT PDF (WITH CURRENT FILTERS)
    =============================== */

    $('#exportPDF').click(function(){

        let search  = $('#search').val();
        let date    = $('#dateFilter').val();
        let month   = $('#monthFilter').val();
        let company = $('#companyFilter').val();

        let url = '/trip/pdf?search='+encodeURIComponent(search)
                +'&date='+encodeURIComponent(date)
                +'&month='+encodeURIComponent(month)
                +'&company='+encodeURIComponent(company);

        window.open(url,'_blank');

    });



    /* ===============================
       INLINE TRUCK OVERRIDE EDIT
    =============================== */

    let activeOverrideCell = null;
    let overrideSavePending = false;

    function renderTruckCellDisplay($cell, displayText, isOverridden) {
        const safeText = $('<div>').text(displayText).html();

        $cell.removeClass('is-editing')
            .toggleClass('bg-amber-50', isOverridden)
            .attr('data-original', displayText)
            .html(
                '<div class="truck-override-wrap flex items-center justify-between gap-1 min-w-0">' +
                    '<span class="truck-display truncate">' + safeText + '</span>' +
                    '<button type="button" class="editTruckBtn shrink-0 bg-slate-600 text-white px-1.5 py-0.5 rounded text-[10px] leading-tight hover:bg-slate-700" title="Edit truck name">Edit</button>' +
                '</div>'
            );

        if (activeOverrideCell && activeOverrideCell.is($cell)) {
            activeOverrideCell = null;
        }
    }

    function cancelOverrideEdit($cell) {
        if (!$cell || !$cell.length || !$cell.hasClass('is-editing')) return;

        const originalText = String($cell.data('original') || '');
        const isOverridden = Boolean($cell.data('is-overridden'));

        renderTruckCellDisplay($cell, originalText, isOverridden);
    }

    function applyOverrideCellState($cell, displayText, isOverridden) {
        renderTruckCellDisplay($cell, displayText, isOverridden);
    }

    function startOverrideEdit($cell) {
        if (!$cell.length || $cell.hasClass('is-editing')) {
            return;
        }

        if (activeOverrideCell && !activeOverrideCell.is($cell)) {
            cancelOverrideEdit(activeOverrideCell);
        }

        activeOverrideCell = $cell;
        const originalText = String($cell.data('original') || $cell.find('.truck-display').text().trim());
        const isOverridden = $cell.hasClass('bg-amber-50');

        $cell.addClass('is-editing')
            .data('original', originalText)
            .data('is-overridden', isOverridden)
            .removeClass('bg-amber-50')
            .html(
                '<div class="truck-override-edit flex flex-col gap-1">' +
                    '<input type="text" class="override-input" value="">' +
                    '<div class="flex gap-1 justify-end">' +
                        '<button type="button" class="saveTruckBtn bg-green-600 text-white px-2 py-0.5 rounded text-[10px] hover:bg-green-700">Save</button>' +
                        '<button type="button" class="cancelTruckBtn bg-gray-500 text-white px-2 py-0.5 rounded text-[10px] hover:bg-gray-600">Cancel</button>' +
                    '</div>' +
                '</div>'
            );

        const $input = $cell.find('.override-input');
        $input.val(originalText).trigger('focus')[0].select();

        $input.on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                saveTruckOverride($cell, $input.val());
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                cancelOverrideEdit($cell);
            }
        });
    }

    function saveTruckOverride($cell, rawValue) {
        const tripId = $cell.data('trip-id');
        const originalText = String($cell.data('original') || '');
        const newValue = String(rawValue || '').trim();

        if (newValue === originalText) {
            cancelOverrideEdit($cell);
            return;
        }

        const confirmText = newValue === ''
            ? 'Remove override and use linked truck name?'
            : `Override truck name to "${newValue}"?`;

        overrideSavePending = true;

        Swal.fire({
            title: 'Update truck name?',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            overrideSavePending = false;

            if (!result.isConfirmed) {
                cancelOverrideEdit($cell);
                return;
            }

            $.ajax({
                url: '/trip/' + tripId + '/override',
                type: 'PATCH',
                data: {
                    field: 'truck',
                    value: newValue
                },
                success: function(res) {
                    applyOverrideCellState($cell, res.displayTruckNumber, res.isOverridden);

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        timer: 900,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Failed to update truck name', 'error');
                    cancelOverrideEdit($cell);
                }
            });
        });
    }

    $(document).on('click', '#tripTableBody .editTruckBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        startOverrideEdit($(this).closest('.truck-override-cell'));
    });

    $(document).on('click', '#tripTableBody .saveTruckBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $cell = $(this).closest('.truck-override-cell');
        saveTruckOverride($cell, $cell.find('.override-input').val());
    });

    $(document).on('click', '#tripTableBody .cancelTruckBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        cancelOverrideEdit($(this).closest('.truck-override-cell'));
    });


});