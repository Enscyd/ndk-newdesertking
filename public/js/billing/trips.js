// trips.js

let tripCache = [];
let currentTrip = null;

/* =========================
   SET CACHE (called from filters.js)
========================= */
export function setTripCache(trips) {
    tripCache = trips || [];
}

/* =========================
   RENDER TRIPS
========================= */
export function renderTrips(trips) {

    console.log("🚀 Rendering Trips:", trips);

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
        <tr class="hover:bg-gray-50">
            <td class="border px-2 py-1 text-center">
                <input type="radio"
                       name="tripSelect"
                       class="trip-select"
                       data-id="${trip.id}">
            </td>
            <td class="border px-2 py-1">${trip.tripDate ?? ""}</td>
            <td class="border px-2 py-1">${trip.vehicleNo ?? ""}</td>
            <td class="border px-2 py-1">${trip.companyName ?? ""}</td>
            <td class="border px-2 py-1">${trip.destination ?? ""}</td>
            <td class="border px-2 py-1">${trip.tripAmount ?? 0}</td>
        </tr>`;
    });

    grid.innerHTML = html;
}


/* =========================
   SELECT TRIP (SAFE VERSION)
========================= */
export function selectTrip(id) {

    const trip = tripCache.find(t => t.id == id);
    if (!trip) return;

    currentTrip = trip;

    // ✅ SAFE DOM ACCESS (NO CRASH)
    const destInput = document.getElementById("destInput");
    const qtyInput = document.getElementById("qtyInput");
    const rentInput = document.getElementById("rentInput");
    const taxableInput = document.getElementById("taxableInput");
    const totalInput = document.getElementById("totalInput");

    if (destInput) destInput.value = trip.destination ?? "";
    if (qtyInput) qtyInput.value = 1;
    if (rentInput) rentInput.value = trip.tripAmount ?? 0;

    // basic calculation fallback (if calculateTotals not defined)
    if (taxableInput && totalInput && rentInput && qtyInput) {
        const qty = parseFloat(qtyInput.value || 0);
        const rent = parseFloat(rentInput.value || 0);

        const taxable = qty * rent;
        const vat = taxable * 0.05;
        const total = taxable + vat;

        taxableInput.value = taxable.toFixed(2);
        totalInput.value = total.toFixed(2);
    }

    // trigger external calculation if exists
    if (window.calculateTotals) {
        window.calculateTotals();
    }
}


/* =========================
   EVENT LISTENER (RADIO SELECT)
========================= */
document.addEventListener("change", function (e) {

    if (e.target.classList.contains("trip-select")) {
        const id = e.target.dataset.id;
        if (id) selectTrip(id);
    }

});


/* =========================
   GET CURRENT TRIP
========================= */
export function getCurrentTrip() {
    return currentTrip;
}


/* =========================
   PAGINATION
========================= */
export function renderPagination(data) {

    const container = document.getElementById("tripPagination");
    if (!container || !data || !data.last_page) return;

    let html = "";

    let start = Math.max(1, data.current_page - 2);
    let end = Math.min(data.last_page, data.current_page + 2);

    for (let i = start; i <= end; i++) {
        html += `
        <button
            data-page="${i}"
            class="page-btn px-3 py-1 border rounded mx-1
                   ${i === data.current_page ? 'bg-gray-200' : ''}">
            ${i}
        </button>`;
    }

    container.innerHTML = html;
}


/* =========================
   PAGINATION EVENTS
========================= */
document.addEventListener("click", function (e) {

    if (e.target.classList.contains("page-btn")) {
        const page = e.target.dataset.page;

        if (window.filterTrips && page) {
            window.filterTrips(page);
        }
    }

});