let items = [];

/* CSRF */
function getCSRF() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

/* ADD ITEM */
function addItem() {
    let desc = document.getElementById('description').value.trim();
    let qty = parseFloat(document.getElementById('qty').value);
    let rate = parseFloat(document.getElementById('rate').value);
    let price = parseFloat(document.getElementById('price').value);

    if (isNaN(qty) || qty <= 0) qty = 1;

    if (isNaN(price) && !isNaN(rate)) {
        price = qty * rate;
    } else if (isNaN(rate) && !isNaN(price)) {
        rate = price / qty;
    }

    if (!desc || isNaN(price) || price < 0) {
        alert('Please enter a valid description and rate/amount');
        return;
    }

    price = Math.round(price * 1000) / 1000;
    rate = Math.round(rate * 1000) / 1000;

    items.push({ description: desc, qty: qty, rate: rate, price: price });

    document.getElementById('description').value = '';
    document.getElementById('qty').value = '1';
    document.getElementById('rate').value = '';
    document.getElementById('price').value = '';

    renderTable();
}

/* DELETE ITEM (FORM) */
function deleteItem(index) {
    if (!confirm('Remove this item?')) return;

    items.splice(index, 1);
    renderTable();
}

/* RENDER ITEMS */
function renderTable() {
    let html = '';
    let total = 0;

    items.forEach(function(item, i) {
        total += item.price;

        html += '<tr>' +
            '<td>' + item.description + '</td>' +
            '<td>' + item.qty + '</td>' +
            '<td>' + Number(item.rate).toFixed(3) + '</td>' +
            '<td>' + Number(item.price).toFixed(3) + '</td>' +
            '<td>' +
                '<button type="button" onclick="deleteItem(' + i + ')" ' +
                'style="background:#dc2626;color:white;padding:3px 6px;border:none;border-radius:4px;cursor:pointer;">✕</button>' +
            '</td>' +
        '</tr>';
    });

    document.getElementById('itemsTable').innerHTML = html;
    document.getElementById('total_amount').value = Math.round(total * 1000) / 1000;
    document.getElementById('itemsInput').value = JSON.stringify(items);
}

/* TOGGLE */
function toggleItems(id) {
    let row = document.getElementById('items-' + id);
    if (!row) return;

    row.style.display = (row.style.display === 'none') ? 'table-row' : 'none';
}

/* DELETE BILL */
function deleteBill(id) {
    if (!confirm('Delete this bill?')) return;

    fetch('/workshop/delete/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': getCSRF() }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Deleted');
        location.reload();
    });
}

/* MARK PAID */
function markPaid(id) {
    if (!confirm('Mark this bill as paid?')) return;

    fetch('/workshop/mark-paid/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCSRF() }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Error updating status');
            return;
        }
        renderStatusCell(id, 'PAID');
    })
    .catch(err => alert('Network error: ' + err.message));
}

/* MARK UNPAID */
function markUnpaid(id) {
    if (!confirm('Mark this bill as unpaid?')) return;

    fetch('/workshop/mark-unpaid/' + id, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': getCSRF() }
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            alert(data.message || 'Error updating status');
            return;
        }
        renderStatusCell(id, 'UNPAID');
    })
    .catch(err => alert('Network error: ' + err.message));
}

/* RENDER STATUS CELL IN-PLACE */
function renderStatusCell(id, status) {
    const cell = document.getElementById('status-cell-' + id);
    if (!cell) {
        location.reload();
        return;
    }

    if (status === 'PAID') {
        cell.innerHTML = '<button type="button" onclick="event.stopPropagation(); markUnpaid(' + id + ')" ' +
            'title="Click to mark Unpaid" ' +
            'style="background:#16a34a;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;font-weight:600;font-size:12px;">' +
            'Paid' +
            '</button>';
    } else {
        cell.innerHTML = '<button type="button" onclick="event.stopPropagation(); markPaid(' + id + ')" ' +
            'title="Click to mark Paid" ' +
            'style="background:#dc2626;color:white;padding:5px 10px;border:none;border-radius:5px;cursor:pointer;font-weight:600;font-size:12px;">' +
            'Mark Paid' +
            '</button>';
    }

    cell.style.transition = 'background 0.3s ease';
    cell.style.background = '#d1fae5';
    setTimeout(() => { cell.style.background = ''; }, 1000);
}

/* DELETE ITEM DB */
function deleteItemServer(id) {
    if (!confirm('Delete this item?')) return;

    fetch('/workshop/item/delete/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': getCSRF() }
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message || 'Deleted');
        location.reload();
    });
}

/* OPEN MODAL */
function editItem(id, description, qty, rate, price) {
    document.getElementById('edit_item_id').value = id;
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_qty').value = qty || 1;
    document.getElementById('edit_rate').value = rate || price || 0;
    document.getElementById('edit_price').value = price || 0;

    document.getElementById('editItemModal').style.display = 'flex';
}

/* CLOSE MODAL */
function closeItemModal() {
    document.getElementById('editItemModal').style.display = 'none';
}

/* UPDATE ITEM (LIVE) */
function updateItem() {

    let id = document.getElementById('edit_item_id').value;
    let description = document.getElementById('edit_description').value.trim();
    let qty = parseFloat(document.getElementById('edit_qty').value) || 1;
    let rate = parseFloat(document.getElementById('edit_rate').value) || 0;
    let price = parseFloat(document.getElementById('edit_price').value);

    if (isNaN(price)) {
        price = qty * rate;
    }

    if (!description || isNaN(price) || price < 0) {
        alert('Invalid data');
        return;
    }

    fetch('/workshop/item/update/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCSRF(),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ description, qty, rate, price })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            alert(data.message);
            return;
        }

        let row = document.querySelector(`[data-item-id="${id}"]`);

        if (row) {
            row.querySelector('.item-desc').innerText = description;
            let qtyEl = row.querySelector('.item-qty');
            if (qtyEl) qtyEl.innerText = qty;
            let rateEl = row.querySelector('.item-rate');
            if (rateEl) rateEl.innerText = Number(rate).toFixed(3);
            let priceEl = row.querySelector('.item-price');
            if (priceEl) priceEl.innerText = Number(price).toFixed(3);

            let editBtn = row.querySelector('.btn-edit');
            if (editBtn) {
                editBtn.dataset.description = description;
                editBtn.dataset.qty = qty;
                editBtn.dataset.rate = rate;
                editBtn.dataset.price = price;
            }

            updateBillTotal(row);

            row.style.background = '#d1fae5';
            setTimeout(() => row.style.background = '', 1000);
        }

        closeItemModal();
    });
}

/* UPDATE TOTAL */
function updateBillTotal(row) {

    let container = row.closest('td');
    if (!container) return;

    let prices = container.querySelectorAll('.item-price');

    let total = 0;

    prices.forEach(el => {
        total += parseFloat(el.innerText) || 0;
    });

    let billRow = container.closest('tr').previousElementSibling;

    if (billRow) {
        let totalCell = billRow.querySelector('.bill-total');
        if (totalCell) {
            totalCell.innerText = total.toFixed(3);
        } else if (billRow.children[4]) {
            billRow.children[4].innerText = total.toFixed(3);
        }
    }
}

function handleEditItem(btn) {
    const id = btn.dataset.id;
    const description = btn.dataset.description;
    const qty = btn.dataset.qty;
    const rate = btn.dataset.rate;
    const price = btn.dataset.price;

    editItem(id, description, qty, rate, price);
}

//////////////////////////////////////////////////////////////
// ✅ SUGGESTIONS (SAFE + IMPROVED)
//////////////////////////////////////////////////////////////

const descriptionInput = document.getElementById('description');
const suggestionsBox = document.getElementById('suggestionsBox');

let debounceTimer;

if (descriptionInput && suggestionsBox) {

    descriptionInput.addEventListener('keyup', function () {

        let query = this.value.trim();

        clearTimeout(debounceTimer);

        if (query.length < 2) {
            suggestionsBox.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {

            fetch(`/item-suggestions?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {

                    suggestionsBox.innerHTML = '';

                    if (!data || data.length === 0) {
                        suggestionsBox.style.display = 'none';
                        return;
                    }

                    data.forEach(item => {

                        let div = document.createElement('div');
                        div.innerText = item;
                        div.style.padding = '8px';
                        div.style.cursor = 'pointer';

                        div.onclick = () => {
                            descriptionInput.value = item;
                            suggestionsBox.style.display = 'none';
                        };

                        div.onmouseover = () => div.style.background = '#f1f5f9';
                        div.onmouseout = () => div.style.background = 'white';

                        suggestionsBox.appendChild(div);
                    });

                    suggestionsBox.style.display = 'block';
                })
                .catch(() => {
                    suggestionsBox.style.display = 'none';
                });

        }, 300); // debounce
    });

    document.addEventListener('click', function (e) {
        if (!suggestionsBox.contains(e.target) && e.target !== descriptionInput) {
            suggestionsBox.style.display = 'none';
        }
    });
}

// Auto-calculate Amount = Qty * Rate on input
function initAutoCalc() {
    const q = document.getElementById('qty');
    const r = document.getElementById('rate');
    const p = document.getElementById('price');
    if (q && r && p) {
        const recalc = () => {
            const qVal = parseFloat(q.value) || 0;
            const rVal = parseFloat(r.value) || 0;
            if (qVal > 0 && rVal >= 0) {
                p.value = (qVal * rVal).toFixed(3);
            }
        };
        q.addEventListener('input', recalc);
        r.addEventListener('input', recalc);
    }

    const eq = document.getElementById('edit_qty');
    const er = document.getElementById('edit_rate');
    const ep = document.getElementById('edit_price');
    if (eq && er && ep) {
        const recalcEdit = () => {
            const qVal = parseFloat(eq.value) || 0;
            const rVal = parseFloat(er.value) || 0;
            if (qVal > 0 && rVal >= 0) {
                ep.value = (qVal * rVal).toFixed(3);
            }
        };
        eq.addEventListener('input', recalcEdit);
        er.addEventListener('input', recalcEdit);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAutoCalc);
} else {
    initAutoCalc();
}