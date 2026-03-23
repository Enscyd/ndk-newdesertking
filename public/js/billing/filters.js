// filters.js

import { renderTrips, renderPagination, setTripCache } from './trips.js';

let currentRequest = null;

/* =========================
   HELPERS
========================= */

function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || "";
}

function showLoading(grid) {
    if (!grid) return;

    grid.innerHTML = `
        <tr>
            <td colspan="6" class="text-center py-3">
                Loading...
            </td>
        </tr>`;
}

function showError(grid, message) {
    if (!grid) return;

    grid.innerHTML = `
        <tr>
            <td colspan="6" class="text-center text-red-500 py-3">
                ${message}
            </td>
        </tr>`;
}


/* =========================
   FILTER TRIPS
========================= */
export async function filterTrips(page = 1) {

    const company = document.getElementById("companyId")?.value.trim() || "";
    const vehicle = document.getElementById("vehicleNo")?.value.trim() || "";
    const tripDate = document.getElementById("tripDate")?.value || "";
    const tripMonth = document.getElementById("tripMonth")?.value || "";

    const grid = document.getElementById("tripGrid");

    showLoading(grid);

    // cancel previous request
    if (currentRequest) {
        currentRequest.abort();
    }

    currentRequest = new AbortController();

    try {
        const response = await fetch(`/billing/filter-trips?page=${page}`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": getCSRFToken()
            },
            signal: currentRequest.signal,
            body: JSON.stringify({
                companyId: company,
                vehicleNo: vehicle,
                tripDate,
                tripMonth
            })
        });

        if (!response.ok) {
            const text = await response.text();
            console.error("SERVER ERROR RESPONSE:", text);
            throw new Error(`Server error (${response.status})`);
        }

        const data = await response.json();

        const tripsData = data?.data ?? data;

        if (!Array.isArray(tripsData)) {
            throw new Error("Invalid response format");
        }

        // ✅ Normalize & format date
        const normalizedTrips = tripsData.map(t => {
            const rawDate = t.tripDate || t.date;

            let formattedDate = "";

            if (rawDate) {
                const isoDate = rawDate.replace(" ", "T");
                const parsed = new Date(isoDate);

                if (!isNaN(parsed)) {
                    const day = parsed.getDate();
                    const month = parsed.getMonth() + 1;
                    const year = parsed.getFullYear();

                    formattedDate = `${month}-${day}-${year}`;
                }
            }

            return {
                ...t,
                tripDate: formattedDate
            };
        });

        // cache trips
        setTripCache(normalizedTrips);

        // render
        renderTrips(normalizedTrips);

        // pagination
        if (data?.current_page && data?.last_page) {
            renderPagination(data);
        }

    } catch (error) {

        // ignore aborted requests
        if (error.name === "AbortError") return;

        console.error("Filter error:", error);
        showError(grid, error.message);
    }
}


/* =========================
   RESET FILTERS
========================= */
export function resetFilters() {

    ["companyId", "vehicleNo", "tripDate", "tripMonth"].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = "";
    });

    filterTrips(1);
}


/* =========================
   AUTO LOAD (ON PAGE LOAD)
========================= */
export function initFilters() {

    const today = new Date().toISOString().split("T")[0];

    const dateInput = document.getElementById("tripDate");
    if (dateInput && !dateInput.value) {
        dateInput.value = today;
    }

    filterTrips(1);
}