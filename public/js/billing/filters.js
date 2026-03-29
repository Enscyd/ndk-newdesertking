// filters.js

import { renderTrips, renderPagination, setTripCache } from './trips.js';

let currentRequest = null;
let debounceTimer = null;
let pageCache = {};
let isLoading = false;

/* =========================
   HELPERS
========================= */

function getCSRFToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || "";
}

function showLoadingOverlay() {
    const grid = document.getElementById("tripGrid");
    if (grid) grid.classList.add("opacity-50");
}

function hideLoadingOverlay() {
    const grid = document.getElementById("tripGrid");
    if (grid) grid.classList.remove("opacity-50");
}

function disablePagination() {
    document.querySelectorAll('.page-btn').forEach(btn => btn.disabled = true);
}

function enablePagination() {
    document.querySelectorAll('.page-btn').forEach(btn => btn.disabled = false);
}


/* =========================
   DEBOUNCE
========================= */
function debounceFilter(page = 1) {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        filterTrips(page);
    }, 300);
}


/* =========================
   FILTER TRIPS
========================= */
export async function filterTrips(page = 1) {

    if (isLoading) return;

    const company = document.getElementById("companyId")?.value.trim() || "";
    const vehicle = document.getElementById("vehicleNo")?.value.trim() || "";
    const tripDate = document.getElementById("tripDate")?.value || "";
    const tripMonth = document.getElementById("tripMonth")?.value || "";

    const cacheKey = JSON.stringify({ page, company, vehicle, tripDate, tripMonth });

    // ✅ CACHE HIT
    if (pageCache[cacheKey]) {
        const cached = pageCache[cacheKey];
        setTripCache(cached.data);
        renderTrips(cached.data);
        renderPagination(cached.meta);
        return;
    }

    isLoading = true;
    showLoadingOverlay();
    disablePagination();

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
            throw new Error(`Server error (${response.status})`);
        }

        const data = await response.json();

        const tripsData = Array.isArray(data?.data) ? data.data : data;

        if (!Array.isArray(tripsData)) {
            throw new Error("Invalid response format");
        }

        /* =========================
           NORMALIZE DATE
        ========================= */
        const normalizedTrips = tripsData.map(t => {
            const rawDate = t.tripDate || t.date;

            let formattedDate = "";

            if (rawDate) {
                const parsed = new Date(rawDate.replace(" ", "T"));
                if (!isNaN(parsed)) {
                    formattedDate = `${parsed.getMonth() + 1}-${parsed.getDate()}-${parsed.getFullYear()}`;
                }
            }

            return {
                ...t,
                tripDate: formattedDate
            };
        });

        /* =========================
           CACHE STORE
        ========================= */
        pageCache[cacheKey] = {
            data: normalizedTrips,
            meta: data
        };

        /* =========================
           RENDER
        ========================= */
        setTripCache(normalizedTrips);
        renderTrips(normalizedTrips);

        if (data && typeof data === "object" && "last_page" in data) {
            renderPagination(data);
        }

    } catch (error) {
        if (error.name !== "AbortError") {
            console.error("Filter error:", error);
        }
    } finally {
        isLoading = false;
        hideLoadingOverlay();
        enablePagination();
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

    pageCache = {}; // clear cache
    filterTrips(1);
}


/* =========================
   INIT FILTERS
========================= */
export function initFilters() {

    const today = new Date().toISOString().split("T")[0];

    const dateInput = document.getElementById("tripDate");
    if (dateInput && !dateInput.value) {
        dateInput.value = today;
    }

    // 🔁 attach debounce listeners
    ["companyId", "vehicleNo", "tripDate", "tripMonth"].forEach(id => {
        document.getElementById(id)?.addEventListener("change", () => debounceFilter(1));
    });

    filterTrips(1);
}


/* =========================
   GLOBAL ACCESS (IMPORTANT)
========================= */
window.filterTrips = filterTrips;