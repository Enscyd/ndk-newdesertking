// invoice.js

import { getCurrentTrip } from './trips.js';
import { calculateTotals } from './calculations.js';

/* =========================
   GLOBAL STATE (SYNCED)
========================= */
export let selectedTrips = window.selectedTrips || [];
window.selectedTrips = selectedTrips;


/* =========================
   ADD TRIP
========================= */
export function addTrips() {

    const currentTrip = getCurrentTrip();
    

    if (!currentTrip) {
        alert("Please select a trip first");
        return;
    }

    console.log("✅ Selected Trip:", currentTrip);

    // prevent duplicate
    const exists = selectedTrips.find(t => t.id === currentTrip.id);
    if (exists) {
        alert("This trip is already added");
        return;
    }

    // company validation
    if (
        selectedTrips.length > 0 &&
        selectedTrips[0].companyName !== currentTrip.companyName
    ) {
        alert("All trips must belong to same company");
        return;
    }

    // inputs (SAFE)
    const qtyInput = document.getElementById("qtyInput");
    const rentInput = document.getElementById("rentInput");
    const destInput = document.getElementById("destInput");

    const qty = parseFloat(qtyInput?.value) || 1;
    const rent = parseFloat(rentInput?.value) || currentTrip.tripAmount;

    // calculation
    const { taxable, vat, total } = calculateTotals(qty, rent);

const item = {
    id: currentTrip.id,
    companyId: parseInt(currentTrip.companyId), // 🔥 IMPORTANT FIX
    companyName: currentTrip.companyName,
    tripDate: currentTrip.tripDate || "",
    destination: currentTrip.destination || "",
    vehicleNo: currentTrip.vehicleNo,
    qty,
    rent,
    taxable,
    vat,
    total
};

// 🧪 DEBUG
console.log("✅ currentTrip:", currentTrip);
console.log("✅ item being added:", item);

    selectedTrips.push(item);

    console.log("📦 Updated Trips:", selectedTrips);

    renderInvoice();
    clearForm();
}


/* =========================
   RENDER INVOICE GRID
========================= */
export function renderInvoice() {

    const grid = document.getElementById("invoiceGrid");
    const totalEl = document.getElementById("grandTotal");
    const hiddenTotal = document.getElementById("grandTotalInput");
    const hiddenContainer = document.getElementById("hiddenTripsContainer");
    const companyInput = document.getElementById("companyIdInput");

    if (!grid) {
        console.error("❌ invoiceGrid not found");
        return;
    }

    let html = "";
    let hiddenHtml = "";
    let grandTotal = 0;

    // 🧪 DEBUG: check selected trips
    console.log("📦 selectedTrips:", selectedTrips);

    selectedTrips.forEach((trip, index) => {

        // 🧪 DEBUG each trip
        console.log(`🔍 Trip ${index}:`, trip);

        const total = parseFloat(trip.total) || 0;
        const taxable = parseFloat(trip.taxable) || 0;
        const vat = parseFloat(trip.vat) || 0;

        grandTotal += total;

        html += `
        <tr>
            <td>${index + 1}</td>
            <td>${window.invoiceNo ?? ""}</td>
            <td>${trip.companyName ?? "N/A"}</td>
            <td>${trip.tripDate ?? "N/A"}</td>
            <td>${trip.destination ?? ""}</td>
            <td>${trip.vehicleNo ?? ""}</td>
            <td>${trip.qty ?? 0}</td>
            <td>${trip.rent ?? 0}</td>
            <td>${taxable.toFixed(2)}</td>
            <td>${vat.toFixed(2)}</td>
            <td>${total.toFixed(2)}</td>
 <td>
        <button type="button"
            class="remove-btn bg-red-500 text-white px-2 py-1 rounded"
            data-index="${index}">
            Remove
        </button>
    </td>
            
        </tr>`;

        hiddenHtml += `
            <input type="hidden" name="items[${index}][tripId]" value="${trip.id}">
            <input type="hidden" name="items[${index}][description]" value="${trip.destination}">
            <input type="hidden" name="items[${index}][vehicleNo]" value="${trip.vehicleNo}">
            <input type="hidden" name="items[${index}][quantity]" value="${trip.qty}">
            <input type="hidden" name="items[${index}][rent]" value="${trip.rent}">
            <input type="hidden" name="items[${index}][taxableAmount]" value="${taxable}">
            <input type="hidden" name="items[${index}][vat]" value="${vat}">
            <input type="hidden" name="items[${index}][totalAmount]" value="${total}">
        `;
    });

    // ✅ Render table
    grid.innerHTML = html;

    // ✅ Render hidden inputs
    if (hiddenContainer) {
        hiddenContainer.innerHTML = hiddenHtml;
    } else {
        console.error("❌ hiddenTripsContainer not found");
    }

    // ✅ Totals
    if (totalEl) totalEl.innerText = grandTotal.toFixed(2);
    if (hiddenTotal) hiddenTotal.value = grandTotal.toFixed(2);

    // 🔥 FIX companyId (WITH DEBUG)
    if (companyInput && selectedTrips.length > 0) {

        const companyIdRaw = selectedTrips[0].companyId;
        const companyId = parseInt(companyIdRaw);

        console.log("🏢 Raw companyId:", companyIdRaw);
        console.log("🏢 Parsed companyId:", companyId);

        if (!isNaN(companyId)) {
            companyInput.value = companyId;
        } else {
            console.error("❌ Invalid companyId:", selectedTrips[0]);
            companyInput.value = "";
        }
    } else {
        console.warn("⚠️ No selectedTrips or companyInput missing");
    }
}


/* =========================
   REMOVE TRIP
========================= */
export function removeTrip(index) {
    index = parseInt(index);
    selectedTrips.splice(index, 1);
    renderInvoice();
}


/* =========================
   EVENT LISTENER
========================= */
document.addEventListener("click", function (e) {

    if (e.target.classList.contains("remove-btn")) {
        const index = e.target.dataset.index;
        removeTrip(index);
    }

});


/* =========================
   CLEAR FORM (SAFE)
========================= */
export function clearForm() {

    const dest = document.getElementById("destInput");
    const qty = document.getElementById("qtyInput");
    const rent = document.getElementById("rentInput");
    const taxable = document.getElementById("taxableInput");
    const vat = document.getElementById("vatInput");
    const total = document.getElementById("totalInput");

    if (dest) dest.value = "";
    if (qty) qty.value = 1;
    if (rent) rent.value = "";
    if (taxable) taxable.value = "";
    if (vat) vat.value = "";
    if (total) total.value = "";
}


/* =========================
   RESET ALL
========================= */
export function resetInvoice() {
    selectedTrips.length = 0;
    renderInvoice();
}