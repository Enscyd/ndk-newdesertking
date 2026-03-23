<form id="invoiceForm"
method="POST"
action="{{ route('billing.store') }}"
enctype="multipart/form-data">

@csrf

<input type="hidden" name="grandTotal" id="grandTotalInput">
<input type="hidden" name="invoiceNo" value="{{ $nextInvoice }}">
<input type="hidden" name="companyId" id="companyIdInput">

<!-- 🔥 REQUIRED -->
<div id="hiddenTripsContainer"></div>

<div class="bg-white p-5 rounded shadow">

<h2 class="text-lg font-semibold mb-4">Invoice Trips</h2>

<table class="w-full border text-sm">
<thead class="bg-gray-100">
<tr>
<th>Sr</th>
<th>Invoice No</th>
<th>Company</th>
<th>Date</th>
<th>Description</th>
<th>Vehicle</th>
<th>Qty</th>
<th>Rent</th>
<th>Taxable</th>
<th>VAT</th>
<th>Total</th>
<th>Action</th>
</tr>
</thead>

<tbody id="invoiceGrid"></tbody>

<tfoot>
<tr>
<td colspan="10" class="text-right font-semibold">Grand Total</td>
<td id="grandTotal">0</td>
<td></td>
</tr>
</tfoot>
</table>

<div class="flex justify-end gap-4 mt-4 items-end">

    <div class="flex flex-col">
        <label class="mb-1 text-sm font-medium">Payment Status</label>
        <select name="paymentStatus" required class="border px-3 py-2 rounded">
            <option value="UNPAID">Unpaid</option>
            <option value="PAID">Paid</option>
        </select>
    </div>

    <input type="file" name="billImage" class="border px-3 py-2">

    <button type="submit"
        class="bg-green-600 text-white px-6 py-2 rounded">
        Save To Bill Book
    </button>

</div>

</div>
</form>