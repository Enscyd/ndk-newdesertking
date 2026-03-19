let selectedTrips = [];
let currentTrip = null;
let tripCache = [];

/* =========================
FILTER TRIPS
========================= */

function filterTrips(page = 1){

let company = document.getElementById("companyId")?.value || "";
let vehicle = document.getElementById("vehicleNo")?.value || "";
let tripDate = document.getElementById("tripDate")?.value || "";
let tripMonth = document.getElementById("tripMonth")?.value || "";

fetch("/billing/filterTrips?page="+page,{

method:"POST",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content
},

body:JSON.stringify({
companyId:company,
vehicleNo:vehicle,
tripDate:tripDate,
tripMonth:tripMonth
})

})
.then(res => {

if(!res.ok){
throw new Error("Server returned "+res.status);
}

return res.json();

})
.then(data=>{

if(!data || !data.data){
console.error("Invalid response", data);
alert("Failed to load trips");
return;
}

tripCache = data.data;

renderTrips(data.data);
renderPagination(data);

})
.catch(err=>{
console.error("Trip Load Error:",err);
alert("Failed to load trips");
});

}


/* =========================
RENDER TRIPS
========================= */

function renderTrips(trips){

let html="";

if(!trips.length){
html = `<tr><td colspan="6" class="text-center py-3">No trips found</td></tr>`;
document.getElementById("tripGrid").innerHTML = html;
return;
}

trips.forEach(trip=>{

html+=`

<tr class="hover:bg-gray-50">

<td class="border px-2 py-1 text-center">

<input type="radio"
name="tripSelect"
onclick="selectTrip(${trip.id})">

</td>

<td class="border px-2 py-1">${trip.tripDate ?? ""}</td>
<td class="border px-2 py-1">${trip.vehicleNo ?? ""}</td>
<td class="border px-2 py-1">${trip.companyName ?? ""}</td>
<td class="border px-2 py-1">${trip.destination ?? ""}</td>
<td class="border px-2 py-1">${trip.tripAmount ?? 0}</td>

</tr>

`;

});

document.getElementById("tripGrid").innerHTML = html;

}


/* =========================
PAGINATION
========================= */

function renderPagination(data){

let html="";

if(!data.last_page) return;

for(let i=1;i<=data.last_page;i++){

html+=`<button
onclick="filterTrips(${i})"
class="px-3 py-1 border rounded mx-1 hover:bg-gray-100">
${i}
</button>`;

}

document.getElementById("tripPagination").innerHTML = html;

}


/* =========================
SELECT TRIP
========================= */

function selectTrip(id){

let trip = tripCache.find(t=>t.id==id);

if(!trip) return;

currentTrip = trip;

document.getElementById("destInput").value = trip.destination ?? "";
document.getElementById("rentInput").value = trip.tripAmount ?? 0;

calculateTotals();

}


/* =========================
CALCULATE TOTALS
========================= */

if(document.getElementById("qtyInput")){
document.getElementById("qtyInput").addEventListener("input",calculateTotals);
}

if(document.getElementById("rentInput")){
document.getElementById("rentInput").addEventListener("input",calculateTotals);
}

function calculateTotals(){

let qty = parseFloat(document.getElementById("qtyInput")?.value) || 0;
let rent = parseFloat(document.getElementById("rentInput")?.value) || 0;

let taxable = qty * rent;
let vatAmount = taxable * 0.05;
let total = taxable + vatAmount;

document.getElementById("taxableInput").value = taxable.toFixed(2);
document.getElementById("vatInput").value = "5%";
document.getElementById("totalInput").value = total.toFixed(2);

}


/* =========================
ADD TRIP
========================= */

function addTrips(){

if(!currentTrip){
alert("Please select a trip first");
return;
}

let exists = selectedTrips.find(t => t.id === currentTrip.id);

if(exists){
alert("This trip is already added to invoice");
return;
}

if(selectedTrips.length>0){

if(selectedTrips[0].companyName !== currentTrip.companyName){
alert("All trips must belong to the same company");
return;
}

}

let qty = parseFloat(document.getElementById("qtyInput").value) || 1;
let rent = parseFloat(document.getElementById("rentInput").value) || currentTrip.tripAmount;

let taxable = qty * rent;
let vatAmount = taxable * 0.05;
let total = taxable + vatAmount;

let item = {

id: currentTrip.id,
companyName: currentTrip.companyName,
tripDate: currentTrip.tripDate,
destination: document.getElementById("destInput").value,
vehicleNo: currentTrip.vehicleNo,

qty: qty,
rent: rent,
taxable: taxable,
vat: vatAmount,
total: total

};

selectedTrips.push(item);

renderInvoice();
clearItemForm();

}


/* =========================
RENDER INVOICE GRID
========================= */

function renderInvoice(){

let html="";
let grandTotal=0;

selectedTrips.forEach((trip,index)=>{

grandTotal+=trip.total;

html+=`

<tr>

<td class="border px-2 py-1">${index+1}</td>

<td class="border px-2 py-1">${window.invoiceNo ?? ""}</td>

<td class="border px-2 py-1">${trip.companyName}</td>
<td class="border px-2 py-1">${trip.tripDate}</td>

<td class="border px-2 py-1">${trip.destination}</td>

<td class="border px-2 py-1">${trip.vehicleNo}</td>

<td class="border px-2 py-1">${trip.qty}</td>

<td class="border px-2 py-1">${trip.rent}</td>

<td class="border px-2 py-1">${trip.taxable.toFixed(2)}</td>
<td class="border px-2 py-1">5%</td>
<td class="border px-2 py-1">${trip.total.toFixed(2)}</td>

<td class="border px-2 py-1">

<button type="button"
onclick="removeTrip(${index})"
class="bg-red-500 text-white px-2 py-1 rounded">
Remove
</button>

<input type="hidden" name="trips[${index}][id]" value="${trip.id}">
<input type="hidden" name="trips[${index}][destination]" value="${trip.destination}">
<input type="hidden" name="trips[${index}][vehicleNo]" value="${trip.vehicleNo}">
<input type="hidden" name="trips[${index}][qty]" value="${trip.qty}">
<input type="hidden" name="trips[${index}][rent]" value="${trip.rent}">
<input type="hidden" name="trips[${index}][taxable]" value="${trip.taxable}">
<input type="hidden" name="trips[${index}][vat]" value="${trip.vat}">
<input type="hidden" name="trips[${index}][total]" value="${trip.total}">

</td>

</tr>

`;

});

document.getElementById("invoiceGrid").innerHTML = html;

document.getElementById("grandTotal").innerText = grandTotal.toFixed(2);

let hiddenTotal = document.getElementById("grandTotalInput");

if(hiddenTotal){
hiddenTotal.value = grandTotal.toFixed(2);
}

}


/* =========================
REMOVE TRIP
========================= */

function removeTrip(index){

selectedTrips.splice(index,1);
renderInvoice();

}


/* =========================
CLEAR FORM
========================= */

function clearItemForm(){

document.getElementById("destInput").value="";
document.getElementById("qtyInput").value=1;
document.getElementById("rentInput").value="";
document.getElementById("taxableInput").value="";
document.getElementById("vatInput").value="5%";
document.getElementById("totalInput").value="";

currentTrip=null;

}


/* =========================
RESET FILTER
========================= */

function resetFilters(){

document.getElementById("companyId").value="";
document.getElementById("vehicleNo").value="";
document.getElementById("tripDate").value="";
document.getElementById("tripMonth").value="";

filterTrips(1);

}


/* =========================
AUTO LOAD TODAY TRIPS
========================= */

document.addEventListener("DOMContentLoaded", function(){

let today = new Date().toISOString().split("T")[0];

if(document.getElementById("tripDate")){
document.getElementById("tripDate").value = today;
}

filterTrips(1);

});