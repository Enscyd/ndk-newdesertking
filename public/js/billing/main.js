// main.js

import { filterTrips, resetFilters, initFilters } from './filters.js';
import { addTrips } from './invoice.js';

/* =========================
   GLOBAL STATE
========================= */
window.selectedTrips = window.selectedTrips || [];


/* =========================
   INIT APP
========================= */
document.addEventListener("DOMContentLoaded", () => {

    initFilters();

    console.log("✅ Billing app initialized");

    /* =========================
       BUTTON EVENTS (FIXED)
    ========================= */

    document.getElementById("filterBtn")
        ?.addEventListener("click", () => filterTrips(1));

    document.getElementById("resetBtn")
        ?.addEventListener("click", resetFilters);

    document.getElementById("addTripsBtn")
        ?.addEventListener("click", addTrips);

    /* =========================
       FORM VALIDATION
    ========================= */

    document.getElementById("invoiceForm")
        ?.addEventListener("submit", (e) => {
            if (!window.selectedTrips || window.selectedTrips.length === 0) {
                e.preventDefault();
                alert("Please add at least one trip to invoice");
            }
        });

});