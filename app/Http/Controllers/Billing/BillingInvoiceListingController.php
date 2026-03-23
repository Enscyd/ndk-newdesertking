<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\Company;
use Illuminate\Http\Request;

class BillingInvoiceListingController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::all();

        return view('billing.index', compact('companies'));
    }


    public function filter(Request $request)
{
    $query = Billing::with(['company','items']);

    if ($request->filled('invoiceNo')) {
        $query->where('invoiceNo', 'like', '%' . $request->invoiceNo . '%');
    }

    if ($request->filled('companyId')) {
        $query->where('companyId', $request->companyId);
    }

    if ($request->filled('status')) {
        $query->where('paymentStatus', $request->status);
    }


    $invoices = $query->latest()->paginate(10)->withQueryString();

    $invoices->withPath(route('billing.filter'));
    

    return view('billing.partials.table', compact('invoices'))->render();
}

    public function markPaid($id)
    {
         $billing = Billing::findOrFail($id);

         $billing->update([ 'paymentStatus' => 'PAID' ]);

         return response()->json(['success' => true  ]);
    }

    public function deleteInvoice($id)
    {
     $billing = Billing::findOrFail($id);

        $billing->delete(); // cascade will delete items

        return response()->json([
        'success' => true
        ]);
    }
}