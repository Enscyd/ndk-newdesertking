<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title>Driver Panel</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="bg-green-600 text-white px-4 py-4 flex items-center shadow-md sticky top-0 z-50">
    <h1 class="text-base sm:text-lg font-semibold">Trip Entry (Drivers)</h1>
</div>

<form id="tripForm" enctype="multipart/form-data" class="p-3 pb-28 space-y-3 max-w-md mx-auto">

<div class="bg-white rounded-2xl shadow-sm p-3 space-y-3">
    <div>
        <label class="text-xs text-gray-500">Company</label>
        <select name="companyId" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
            <option value="">Select Company</option>
            @foreach($companies as $company)
            <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs text-gray-500">Destination</label>
        <select name="destinationId" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
            <option value="">Select Destination</option>
            @foreach($destinations as $destination)
            <option value="{{ $destination->id }}">{{ $destination->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs text-gray-500">Driver</label>
        <select name="employeeId" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
            <option value="">Select Driver</option>
            @foreach($employees as $employee)
            <option value="{{ $employee->id }}">{{ $employee->employeeName }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="text-xs text-gray-500">Truck</label>
        <select name="truckId" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
            <option value="">Select Truck</option>
            @foreach($trucks as $truck)
            <option value="{{ $truck->id }}">{{ $truck->truckNumber }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm p-3 space-y-3">
    <div>
        <label class="text-xs text-gray-500">Trip Type</label>
        <select id="tripType" name="tripType" class="w-full border rounded-xl px-3 py-4 text-sm mt-1">
            <option value="Go Trip">Go Trip</option>
            <option value="Return Trip">Return Trip</option>
        </select>
    </div>

    <div id="driverAmountBox">
        <label class="text-xs text-gray-500">Driver Amount</label>
        <input type="number" name="driverAmount" min="0" class="w-full border rounded-xl px-3 py-4 text-sm mt-1">
    </div>

    <div>
        <label class="text-xs text-gray-500">Trip Date</label>
        <input type="date" name="tripDate" max="{{ date('Y-m-d') }}" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
    </div>

    <div>
        <label class="text-xs text-gray-500">Trip Amount</label>
        <input type="number" name="tripAmount" min="1" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm p-3 space-y-3">
    <div>
        <label class="text-xs text-gray-500">Is Omani</label>
        <select id="isOmani" name="isOmani" class="w-full border rounded-xl px-3 py-4 text-sm mt-1" required>
            <option value="No">No</option>
            <option value="Yes">Yes</option>
        </select>
    </div>

    <div class="omaniFields hidden">
        <label class="text-xs text-gray-500">Omani Name</label>
        <input type="text" name="omaniName" class="w-full border rounded-xl px-3 py-4 text-sm mt-1">
    </div>

    <div class="omaniFields hidden">
        <label class="text-xs text-gray-500">Omani Amount</label>
        <input type="number" name="omaniAmount" min="0" class="w-full border rounded-xl px-3 py-4 text-sm mt-1">
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm p-3">
    <label class="text-xs text-gray-500">Trip Image</label>
    <input type="file" id="imageInput" name="image" accept="image/*" class="w-full mt-2 text-sm">
    <img id="previewImage" class="mt-3 hidden w-full max-h-48 object-cover rounded-xl border">
</div>

<div class="fixed bottom-0 left-0 right-0 bg-white border-t p-3 max-w-md mx-auto">
    <button type="submit" id="submitBtn" class="w-full bg-green-600 text-white py-4 rounded-2xl text-base font-semibold shadow active:scale-95 transition">
        Save Trip
    </button>
</div>
</form>

<script>
$.ajaxSetup({
    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

function toggleTripType() {
    let isReturn = $('#tripType').val() === 'Return Trip';
    $('#driverAmountBox').toggleClass('hidden', isReturn);
    if (isReturn) {
        $('input[name="driverAmount"]').val('');
    }
}

function toggleOmani() {
    let isYes = $('#isOmani').val() === 'Yes';
    $('.omaniFields').toggleClass('hidden', !isYes);
    $('input[name="omaniName"], input[name="omaniAmount"]').prop('required', isYes);
    if (!isYes) {
        $('input[name="omaniName"], input[name="omaniAmount"]').val('');
    }
}

$('#tripType').on('change', toggleTripType);
$('#isOmani').on('change', toggleOmani);

$('#imageInput').on('change', function(){
    let file = this.files[0];
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        Swal.fire('Error', 'Only image files allowed', 'error');
        this.value = '';
        return;
    }

    let reader = new FileReader();
    reader.onload = e => $('#previewImage').attr('src', e.target.result).removeClass('hidden');
    reader.readAsDataURL(file);
});

$('#tripForm').on('submit', function(e){
    e.preventDefault();

    let form = this;
    let formData = new FormData(form);
    let btn = $('#submitBtn');

    btn.prop('disabled', true).text('Saving...');

    $.ajax({
        url: "{{ route('driver.trip.store') }}",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(){
            Swal.fire('Saved!', 'Trip added successfully', 'success');
            form.reset();
            $('#previewImage').addClass('hidden').attr('src', '');
            toggleTripType();
            toggleOmani();
        },
        error: function(){
            Swal.fire('Error', 'Something went wrong', 'error');
        },
        complete: function(){
            btn.prop('disabled', false).text('Save Trip');
        }
    });
});

toggleTripType();
toggleOmani();
</script>
</body>
</html>