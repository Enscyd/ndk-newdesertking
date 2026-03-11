$(document).ready(function(){

    // CSRF setup
    $.ajaxSetup({
        headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });


    /* ===============================
       SET TODAY DATE ON PAGE LOAD
    =============================== */

    let today = new Date().toISOString().split('T')[0];
    $('#dateFilter').val(today);


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


    // Load today's trips on first page load
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

                // Reset today's date again after form reset
                $('#dateFilter').val(today);
                $('#monthFilter').val('');

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
            $('input[name="tripDate"]').val(data.tripDate);
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


});