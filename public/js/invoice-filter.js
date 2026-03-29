// =========================
// GLOBAL SAFETY (NO REFRESH)
// =========================
document.addEventListener('click', function(e){
    if(e.target.closest('.editItemBtn') || e.target.closest('.saveItemBtn')){
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

// CLOSE MODAL
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

    const params = new URLSearchParams({ invoiceNo, companyId, status, date });

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

    ['invoiceNo','companyId','status','date'].forEach(name=>{
        document.querySelector(`[name="${name}"]`)?.addEventListener('change', loadInvoices);
    });

    document.querySelector('[name="invoiceNo"]')?.addEventListener('keyup', loadInvoices);

    document.getElementById('clearFilter')?.addEventListener('click', ()=>{
        document.querySelectorAll('[name="invoiceNo"],[name="companyId"],[name="status"],[name="date"]')
            .forEach(el => el.value = '');
        loadInvoices();
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
        else input = `<input class="border p-1 w-full" value="${val}">`;

        cells[i].innerHTML = input;
    }

    btn.innerText = "Save";
    btn.classList.replace('editItemBtn','saveItemBtn');
});


// =========================
// AUTO CALCULATION (FULL)
// =========================
document.addEventListener('input', function(e){

    if(e.target.classList.contains('qty') || e.target.classList.contains('rent')){

        const row = e.target.closest('tr');

        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const rent = parseFloat(row.querySelector('.rent')?.value) || 0;

        const taxable = qty * rent;
        const vat = taxable * 0.05; // change %
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
// SAVE ITEM
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.saveItemBtn');
    if(!btn) return;

    e.stopImmediatePropagation();

    const row = btn.closest('tr');
    const id = btn.dataset.id;
    const inputs = row.querySelectorAll('input');

    const data = {
        description: inputs[0]?.value || '',
        vehicleNo: inputs[1]?.value || '',
        quantity: inputs[2]?.value || 0,
        rent: inputs[3]?.value || 0,
        taxableAmount: inputs[4]?.value || 0,
        vat: inputs[5]?.value || 0,
        totalAmount: inputs[6]?.value || 0
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