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

    // SAFETY: ignore event object
    if (typeof url !== "string") {
        url = null;
    }

    const invoiceInput = document.querySelector('[name="invoiceNo"]');
    const companyInput = document.querySelector('[name="companyId"]');
    const statusInput  = document.querySelector('[name="status"]');

    const invoiceNo = invoiceInput ? invoiceInput.value.trim() : '';
    const companyId = companyInput ? companyInput.value : '';
    const status    = statusInput ? statusInput.value : '';

    const params = new URLSearchParams({
        invoiceNo,
        companyId,
        status
    });

    // =========================
    // BUILD FINAL URL (SAFE)
    // =========================
    let finalUrl = window.filterUrl;

    if (url) {
        finalUrl = url; // pagination url
    }

    finalUrl += (finalUrl.includes('?') ? '&' : '?') + params.toString();

    console.log("FETCH:", finalUrl);

    const tableBody = document.getElementById('invoiceTableBody');

    // =========================
    // LOADING STATE
    // =========================
    if (tableBody) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="p-4 text-center">Loading...</td>
            </tr>
        `;
    }

    // =========================
    // FETCH DATA
    // =========================
    fetch(finalUrl)
        .then(res => {
            if (!res.ok) {
                throw new Error(`HTTP ${res.status}`);
            }
            return res.text();
        })
        .then(html => {
            if (tableBody) {
                tableBody.innerHTML = html;
            }
        })
        .catch(err => {
            console.error("AJAX ERROR:", err);

            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="p-4 text-center text-red-600">
                            Failed to load data
                        </td>
                    </tr>
                `;
            }
        });
}


// =========================
// MARK AS PAID (AJAX)
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.markPaidBtn');

    if (btn) {

        const id = btn.getAttribute('data-id');

        if (!id) return;

        if (!confirm("Mark this invoice as PAID?")) return;

        fetch(`/billing/mark-paid/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => {
            if (!res.ok) {
                throw new Error("Failed to update status");
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                loadInvoices(); // 🔥 reload updated list
            }
        })
        .catch(err => {
            console.error("MARK PAID ERROR:", err);
            alert("Failed to update payment status");
        });

    }

});


// =========================
// INITIAL LOAD + EVENTS
// =========================
document.addEventListener("DOMContentLoaded", function () {

    // INITIAL LOAD
    loadInvoices();

    const invoiceInput = document.querySelector('[name="invoiceNo"]');
    const companyInput = document.querySelector('[name="companyId"]');
    const statusInput  = document.querySelector('[name="status"]');
    const clearBtn     = document.getElementById('clearFilter');

    if (invoiceInput) {
        invoiceInput.addEventListener('keyup', () => loadInvoices());
    }

    if (companyInput) {
        companyInput.addEventListener('change', () => loadInvoices());
    }

    if (statusInput) {
        statusInput.addEventListener('change', () => loadInvoices());
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (invoiceInput) invoiceInput.value = '';
            if (companyInput) companyInput.value = '';
            if (statusInput)  statusInput.value = '';
            loadInvoices();
        });
    }

});


// =========================
// AJAX PAGINATION
// =========================
document.addEventListener('click', function(e){

    const link = e.target.closest('.pagination a');

    if (link) {
        e.preventDefault();

        const url = link.getAttribute('href');

        if (url) {
            loadInvoices(url);
        }
    }

});

// =========================
// DELETE INVOICE
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.deleteInvoiceBtn');

    if(btn){

        const id = btn.getAttribute('data-id');

        if(!confirm("Delete full invoice? This cannot be undone!")) return;

        fetch(`/billing/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success){
                loadInvoices();
            }
        });

    }

});

// =========================
// DELETE ITEM
// =========================
document.addEventListener('click', function(e){

    const btn = e.target.closest('.deleteItemBtn');

    if(btn){

        const id = btn.getAttribute('data-id');

        if(!confirm("Delete this item?")) return;

        fetch(`/billing/item/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(() => {
            loadInvoices(); // reload updated data
        });

    }

});