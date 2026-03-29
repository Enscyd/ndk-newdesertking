// trips.js

let tripCache = [];
let currentTrip = null;

/* ========================= */
export function setTripCache(trips) {
    tripCache = trips || [];
}

/* ========================= */
export function renderTrips(trips) {

    const grid = document.getElementById("tripGrid");
    if (!grid) return;

    if (!trips || !trips.length) {
        grid.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-3">
                No trips found
            </td>
        </tr>`;
        return;
    }

    let html = "";

    trips.forEach(trip => {
        html += `
        <tr class="hover:bg-gray-50 transition">
            <td class="border px-2 py-1 text-center">
                <input type="radio"
                       name="tripSelect"
                       class="trip-select"
                       data-id="${trip.id}">
            </td>
            <td class="border px-2 py-1">${trip.tripDate}</td>
            <td class="border px-2 py-1">${trip.vehicleNo}</td>
            <td class="border px-2 py-1">${trip.companyName}</td>
            <td class="border px-2 py-1">${trip.destination}</td>
            <td class="border px-2 py-1">${trip.tripAmount}</td>
        </tr>`;
    });

    grid.innerHTML = html;
}

/* ========================= */
export function renderPagination(data) {

    const container = document.getElementById("tripPagination");
    if (!container || !data.last_page) return;

    let html = "";

    if (data.current_page > 1) {
        html += `<button class="page-btn px-2" data-page="${data.current_page - 1}">Prev</button>`;
    }

    let start = Math.max(1, data.current_page - 2);
    let end = Math.min(data.last_page, data.current_page + 2);

    for (let i = start; i <= end; i++) {
        html += `
        <button
            data-page="${i}"
            class="page-btn px-3 py-1 border rounded mx-1
                   ${i === data.current_page ? 'bg-blue-500 text-white' : ''}">
            ${i}
        </button>`;
    }

    if (data.current_page < data.last_page) {
        html += `<button class="page-btn px-2" data-page="${data.current_page + 1}">Next</button>`;
    }

    container.innerHTML = html;
}

/* ========================= */
document.addEventListener("click", function (e) {

    if (e.target.classList.contains("page-btn")) {
        const page = e.target.dataset.page;
        if (page && window.filterTrips) {
            window.filterTrips(page);
        }
    }

   if (e.target.classList.contains("trip-select")) {

    const id = e.target.dataset.id;
    const trip = tripCache.find(t => t.id == id);
    if (!trip) return;

    currentTrip = trip;

    // get inputs safely
    const destInput = document.getElementById("destInput");
    const qtyInput = document.getElementById("qtyInput");
    const rentInput = document.getElementById("rentInput");
    const taxableInput = document.getElementById("taxableInput");
    const vatInput = document.getElementById("vatInput");
    const totalInput = document.getElementById("totalInput");

    // default values
    const qty = 1;
    const rent = parseFloat(trip.tripAmount || 0);

    // calculations
    const taxable = qty * rent;
    const vat = taxable * 0.05;
    const total = taxable + vat;

    // set values (safe)
    if (destInput) destInput.value = trip.destination || "";
    if (qtyInput) qtyInput.value = qty;
    if (rentInput) rentInput.value = rent;

    if (taxableInput) taxableInput.value = taxable.toFixed(2);
    if (vatInput) vatInput.value = vat.toFixed(2);
    if (totalInput) totalInput.value = total.toFixed(2);
}

});

/* ========================= */
export function getCurrentTrip() {
    return currentTrip;
}