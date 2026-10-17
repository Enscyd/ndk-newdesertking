let items = [];

/* CSRF */
function getCSRF() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

/* ADD ITEM */
function addItem() {
    let descEl = document.getElementById('description');
    let qtyEl = document.getElementById('qty');
    let rateEl = document.getElementById('rate');
    let priceEl = document.getElementById('price');

    if (!descEl || !qtyEl || !rateEl || !priceEl) return false;

    let desc = descEl.value.trim();
    let qty = parseFloat(qtyEl.value);
    let rate = parseFloat(rateEl.value);
    let price = parseFloat(priceEl.value);

    if (isNaN(qty) || qty <= 0) qty = 1;

    if (isNaN(price) && !isNaN(rate)) {
        price = qty * rate;
    } else if (isNaN(rate) && !isNaN(price)) {
        rate = price / qty;
    }

    if (!desc || isNaN(price) || price < 0) {
        alert('Please enter a valid description and rate/amount');
        return false;
    }

    price = Math.round(price * 1000) / 1000;
    rate = Math.round(rate * 1000) / 1000;

    items.push({ description: desc, qty: qty, rate: rate, price: price });

    descEl.value = '';
    qtyEl.value = '1';
    rateEl.value = '';
    priceEl.value = '';

    renderTable();
    return true;
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

        let displayQty = (Number(item.qty) % 1 === 0 ? Number(item.qty) : Number(item.qty).toFixed(2));
        html += '<tr>' +
            '<td>' + item.description + '</td>' +
            '<td>' + displayQty + '</td>' +
            '<td>' + Number(item.rate).toFixed(3) + '</td>' +
            '<td>' + Number(item.price).toFixed(3) + '</td>' +
            '<td>' +
                '<button type="button" onclick="deleteItem(' + i + ')" ' +
                'style="background:#dc2626;color:white;padding:3px 6px;border:none;border-radius:4px;cursor:pointer;">✕</button>' +
            '</td>' +
        '</tr>';
    });

    const itemsTable = document.getElementById('itemsTable');
    if (itemsTable) itemsTable.innerHTML = html;

    const totalEl = document.getElementById('total_amount');
    if (totalEl) totalEl.value = Number(total).toFixed(3);

    const itemsInput = document.getElementById('itemsInput');
    if (itemsInput) itemsInput.value = JSON.stringify(items);
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

            updateBillTotal(row, data.new_total);

            row.style.background = '#d1fae5';
            setTimeout(() => row.style.background = '', 1000);
        }

        closeItemModal();
    });
}

/* UPDATE TOTAL */
function updateBillTotal(row, serverTotal) {

    let container = row.closest('td');
    if (!container) return;

    let total = 0;
    if (serverTotal !== undefined && serverTotal !== null) {
        total = parseFloat(serverTotal) || 0;
    } else {
        let prices = container.querySelectorAll('.item-price');
        prices.forEach(el => {
            total += parseFloat(el.innerText.replace(/,/g, '')) || 0;
        });
    }

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

// Auto-calculate Amount = Qty * Rate on input (Bi-directional)
function initAutoCalc() {
    const q = document.getElementById('qty');
    const r = document.getElementById('rate');
    const p = document.getElementById('price');

    if (q && r && p) {
        q.addEventListener('input', () => {
            const qVal = parseFloat(q.value) || 0;
            const rVal = parseFloat(r.value);
            const pVal = parseFloat(p.value);

            if (qVal > 0 && !isNaN(rVal) && rVal >= 0 && r.value !== '') {
                p.value = (qVal * rVal).toFixed(3);
            } else if (qVal > 0 && !isNaN(pVal) && pVal >= 0 && p.value !== '') {
                r.value = (pVal / qVal).toFixed(3);
            }
        });

        r.addEventListener('input', () => {
            const qVal = parseFloat(q.value) || 1;
            const rVal = parseFloat(r.value);
            if (!isNaN(rVal) && rVal >= 0 && r.value !== '') {
                p.value = (qVal * rVal).toFixed(3);
            }
        });

        p.addEventListener('input', () => {
            const qVal = parseFloat(q.value) || 1;
            const pVal = parseFloat(p.value);
            if (!isNaN(pVal) && pVal >= 0 && p.value !== '') {
                r.value = (pVal / qVal).toFixed(3);
            }
        });
    }

    const eq = document.getElementById('edit_qty');
    const er = document.getElementById('edit_rate');
    const ep = document.getElementById('edit_price');

    if (eq && er && ep) {
        eq.addEventListener('input', () => {
            const qVal = parseFloat(eq.value) || 0;
            const rVal = parseFloat(er.value);
            const pVal = parseFloat(ep.value);

            if (qVal > 0 && !isNaN(rVal) && rVal >= 0 && er.value !== '') {
                ep.value = (qVal * rVal).toFixed(3);
            } else if (qVal > 0 && !isNaN(pVal) && pVal >= 0 && ep.value !== '') {
                er.value = (pVal / qVal).toFixed(3);
            }
        });

        er.addEventListener('input', () => {
            const qVal = parseFloat(eq.value) || 1;
            const rVal = parseFloat(er.value);
            if (!isNaN(rVal) && rVal >= 0 && er.value !== '') {
                ep.value = (qVal * rVal).toFixed(3);
            }
        });

        ep.addEventListener('input', () => {
            const qVal = parseFloat(eq.value) || 1;
            const pVal = parseFloat(ep.value);
            if (!isNaN(pVal) && pVal >= 0 && ep.value !== '') {
                er.value = (pVal / qVal).toFixed(3);
            }
        });
    }

    // Automatically add pending item when submitting form if description & price/rate are typed
    const form = document.getElementById('workshopBillForm') || document.querySelector('form[action*="workshop/store"]');
    if (form && !form.dataset.autoAddBound) {
        form.dataset.autoAddBound = 'true';
        form.addEventListener('submit', function(e) {
            const desc = document.getElementById('description')?.value.trim();
            const price = document.getElementById('price')?.value;
            const rate = document.getElementById('rate')?.value;
            if (desc && (price || rate)) {
                addItem();
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAutoCalc);
} else {
    initAutoCalc();
}