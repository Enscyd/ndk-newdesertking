let items = [];

/* CSRF */
function getCSRF() {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute('content') : '';
}

/* ADD ITEM */
function addItem() {
    let desc = document.getElementById('description').value.trim();
    let price = parseFloat(document.getElementById('price').value);

    if (!desc || isNaN(price) || price <= 0) {
        alert('Please enter valid item details');
        return;
    }

    items.push({ description: desc, price: price });

    document.getElementById('description').value = '';
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
            '<td>' + item.price + '</td>' +
            '<td>' +
                '<button onclick="deleteItem(' + i + ')" ' +
                'style="background:#dc2626;color:white;padding:4px 8px;border:none;border-radius:5px;">Delete</button>' +
            '</td>' +
        '</tr>';
    });

    document.getElementById('itemsTable').innerHTML = html;
    document.getElementById('total_amount').value = total;
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
        alert(data.message || 'Updated');
        location.reload();
    });
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
function editItem(id, description, price) {
    document.getElementById('edit_item_id').value = id;
    document.getElementById('edit_description').value = description || '';
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
    let price = parseFloat(document.getElementById('edit_price').value);

    if (!description || isNaN(price)) {
        alert('Invalid data');
        return;
    }

    fetch('/workshop/item/update/' + id, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCSRF(),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ description, price })
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
            row.querySelector('.item-price').innerText = price;

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
        billRow.children[3].innerText = total; // ✅ FIXED INDEX (date column added)
    }
}

function handleEditItem(btn) {
    const id = btn.dataset.id;
    const description = btn.dataset.description;
    const price = btn.dataset.price;

    editItem(id, description, price);
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