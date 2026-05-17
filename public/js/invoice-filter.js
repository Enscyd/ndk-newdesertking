// =========================
// GLOBAL SAFETY (NO REFRESH)
// =========================
document.addEventListener('click', function(e){
    if(
        e.target.closest('.editItemBtn') ||
        e.target.closest('.saveItemBtn') ||
        e.target.closest('.addTripBtn') ||
        e.target.closest('.saveNewTripBtn')
    ){
        e.preventDefault();
    }
}, true);


// =========================
// IMAGE PREVIEW
// =========================
function openImage(src) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('modalImg');

    if (modal && img) {
        img.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}


// =========================
// CLOSE IMAGE MODAL
// =========================
document.addEventListener('click', function(e){
    const modal = document.getElementById('imageModal');

    if (modal && e.target.id === 'imageModal') {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
});


// =========================
// LOAD INVOICES (AJAX)
// =========================
function loadInvoices(url = null) {

    if (typeof url !== "string") url = null;

    const invoiceNo = document.querySelector('[name="invoiceNo"]')?.value.trim() || '';
    const companyId = document.querySelector('[name="companyId"]')?.value || '';
    const status    = document.querySelector('[name="status"]')?.value || '';
    const date      = document.querySelector('[name="date"]')?.value || '';
    const month     = document.querySelector('[name="month"]')?.value || '';

   const params = new URLSearchParams({invoiceNo,companyId,status,date,month});

    let finalUrl = url ? url : window.filterUrl + '?' + params.toString();

    const tableBody = document.getElementById('invoiceTable');

    if (tableBody) {
        tableBody.innerHTML = `<tr><td colspan="5" class="p-4 text-center">Loading...</td></tr>`;
    }

    fetch(finalUrl)
        .then(res => res.text())
        .then(html => {
            if (tableBody) tableBody.innerHTML = html;
        })
        .catch(() => {
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="5" class="p-4 text-center text-red-600">Failed</td></tr>`;
            }
        });
}


// =========================
// MARK AS PAID
// =========================
document.addEventListener('click', function(e){
    const btn = e.target.closest('.markPaidBtn');
    if (!btn) return;

    const id = btn.dataset.id;
    if (!confirm("Mark as PAID?")) return;

    fetch(`/billing/mark-paid/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => loadInvoices());
});


// =========================
// FILTER EVENTS
// =========================
document.addEventListener("DOMContentLoaded", function () {

    loadInvoices();

    ['invoiceNo','companyId','status','date','month'].forEach(name=>{
        document.querySelector(`[name="${name}"]`)?.addEventListener('change', loadInvoices);
    });

    document.querySelector('[name="invoiceNo"]')?.addEventListener('keyup', loadInvoices);

    const dateInput = document.querySelector('[name="date"]');
const monthInput = document.querySelector('[name="month"]');

// if month selected → clear date
monthInput?.addEventListener('change', function () {
    if (this.value) {
        dateInput.value = '';
    }
    loadInvoices();
});

// if date selected → clear month
dateInput?.addEventListener('change', function () {
    if (this.value) {
        monthInput.value = '';
    }
    loadInvoices();
});

document.getElementById('clearFilter')?.addEventListener('click', () => {
    // reset all filters
    document.querySelectorAll(
        '[name="invoiceNo"],[name="companyId"],[name="status"],[name="date"],[name="month"]'
    ).forEach(el => el.value = '');

    // empty table instead of loading all records
    const tableBody = document.getElementById('invoiceTable');

    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="p-10 text-center text-gray-400">
                    No invoices selected
                </td>
            </tr>
        `;
    }
});


});


// =========================
// PAGINATION
// =========================
document.addEventListener('click', function(e){
    const link = e.target.closest('.pagination a');
    if (link) {
        e.preventDefault();
        loadInvoices(link.href);
    }
});


// =========================
// DELETE INVOICE
// =========================
document.addEventListener('click', function(e){
    const btn = e.target.closest('.deleteInvoiceBtn');
    if (!btn) return;

    if (!confirm("Delete invoice?")) return;

    fetch(`/billing/delete/${btn.dataset.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(()=>loadInvoices());
});


// =========================
// DELETE ITEM
// =========================
document.addEventListener('click', function(e){
    const btn = e.target.closest('.deleteItemBtn');
    if (!btn) return;

    if (!confirm("Delete item?")) return;

    fetch(`/billing/delete-item/${btn.dataset.id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    }).then(()=>loadInvoices());
});


// =========================
// EDIT ITEM
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.editItemBtn');
    if(!btn) return;

    e.stopImmediatePropagation();

    const row = btn.closest('tr');
    const cells = row.querySelectorAll('td');

    for(let i = 1; i <= 7; i++){

        const val = cells[i].innerText.trim();

        let input = '';

        if(i === 3) input = `<input class="qty border p-1 w-full" value="${val}">`;
        else if(i === 4) input = `<input class="rent border p-1 w-full" value="${val}">`;
        else if(i === 5) input = `<input class="taxable border p-1 w-full bg-gray-100" value="${val}" readonly>`;
        else if(i === 6) input = `<input class="vat border p-1 w-full bg-gray-100" value="${val}" readonly>`;
        else if(i === 7) input = `<input class="total border p-1 w-full bg-gray-100" value="${val}" readonly>`;
        else if(i === 1) input = `<input class="description border p-1 w-full" value="${val}">`;
        else if(i === 2) input = `<input class="vehicleNo border p-1 w-full" value="${val}">`;

        cells[i].innerHTML = input;
    }

    btn.innerText = "Save";
    btn.classList.replace('editItemBtn','saveItemBtn');
});


// =========================
// AUTO CALCULATION (EDIT + ADD)
// =========================
document.addEventListener('input', function(e){

    if(e.target.classList.contains('qty') || e.target.classList.contains('rent')){

        const row = e.target.closest('tr');

        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const rent = parseFloat(row.querySelector('.rent')?.value) || 0;

        const taxable = qty * rent;
        const vat = taxable * 0.05;
        const total = taxable + vat;

        row.querySelector('.taxable').value = taxable.toFixed(2);
        row.querySelector('.vat').value = vat.toFixed(2);
        row.querySelector('.total').value = total.toFixed(2);

        updateGrandTotal();
    }

});


// =========================
// GRAND TOTAL UPDATE
// =========================
function updateGrandTotal(){

    let sum = 0;

    document.querySelectorAll('.total').forEach(el=>{
        sum += parseFloat(el.value) || 0;
    });

    const el = document.querySelector('#grandTotal');
    if(el) el.innerText = sum.toFixed(2);
}


// =========================
// SAVE EDITED ITEM
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.saveItemBtn');
    if(!btn) return;

    e.stopImmediatePropagation();

    const row = btn.closest('tr');
    const id = btn.dataset.id;

    const data = {
        description: row.querySelector('.description')?.value || '',
        vehicleNo: row.querySelector('.vehicleNo')?.value || '',
        quantity: row.querySelector('.qty')?.value || 0,
        rent: row.querySelector('.rent')?.value || 0,
        taxableAmount: row.querySelector('.taxable')?.value || 0,
        vat: row.querySelector('.vat')?.value || 0,
        totalAmount: row.querySelector('.total')?.value || 0
    };

    fetch(window.updateItemUrl.replace(':id', id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    }).then(()=>loadInvoices());

});


// =========================
// ADD TRIP UNDER SAME INVOICE
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.addTripBtn');
    if(!btn) return;

    const invoiceId = btn.dataset.id;
    const itemTable = btn.closest('tr').nextElementSibling.querySelector('tbody');

    const alreadyOpen = itemTable.querySelector('.saveNewTripBtn');
    if (alreadyOpen) return;

    const newRow = document.createElement('tr');
    newRow.className = "bg-yellow-50";

    // ✅ dropdown options from blade window variables
    const destinationOptions = (window.destinations || [])
        .map(d => `<option value="${d.name}">${d.name}</option>`)
        .join('');

    const truckOptions = (window.trucks || [])
        .map(t => `<option value="${t.truckNumber}">${t.truckNumber}</option>`)
        .join('');

    newRow.innerHTML = `
        <td class="p-3 border text-center">New</td>

        <td class="p-3 border">
            <select class="trip-description border p-1 w-full">
                <option value="">Select Destination</option>
                ${destinationOptions}
            </select>
        </td>

        <td class="p-3 border">
            <select class="trip-vehicle border p-1 w-full">
                <option value="">Select Truck</option>
                ${truckOptions}
            </select>
        </td>

        <td class="p-3 border">
            <input type="number" class="qty border p-1 w-full" placeholder="Qty">
        </td>

        <td class="p-3 border">
            <input type="number" class="rent border p-1 w-full" placeholder="Rent">
        </td>

        <td class="p-3 border">
            <input class="taxable border p-1 w-full bg-gray-100" readonly>
        </td>

        <td class="p-3 border">
            <input class="vat border p-1 w-full bg-gray-100" readonly>
        </td>

        <td class="p-3 border">
            <input class="total border p-1 w-full bg-gray-100" readonly>
        </td>

        <td class="p-3 border text-center">
            <button 
                type="button"
                class="saveNewTripBtn bg-green-600 text-white px-3 py-1 rounded-md text-xs hover:bg-green-700"
                data-invoice-id="${invoiceId}">
                Save
            </button>
        </td>
    `;

    const grandTotalRow = itemTable.querySelector('tr.bg-gray-50.font-semibold');
    itemTable.insertBefore(newRow, grandTotalRow);
});

// =========================
// SAVE NEW TRIP
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.saveNewTripBtn');
    if(!btn) return;

    const row = btn.closest('tr');

    const formData = new FormData();
    formData.append('invoice_id', btn.dataset.invoiceId);
    formData.append('description', row.querySelector('.trip-description')?.value || '');
    formData.append('vehicleNo', row.querySelector('.trip-vehicle')?.value || '');
    formData.append('quantity', row.querySelector('.qty')?.value || 0);
    formData.append('rent', row.querySelector('.rent')?.value || 0);
    formData.append('taxableAmount', row.querySelector('.taxable')?.value || 0);
    formData.append('vat', row.querySelector('.vat')?.value || 0);
    formData.append('totalAmount', row.querySelector('.total')?.value || 0);

    fetch(window.storeTripUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if(res.success){
            loadInvoices();
        } else {
            alert(res.error || 'Trip save failed');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong');
    });

});


