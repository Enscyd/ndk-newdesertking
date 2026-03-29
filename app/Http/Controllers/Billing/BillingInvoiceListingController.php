<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BillingInvoiceListingController extends Controller
{
    // ===============================
    // 📄 INDEX PAGE (OPTIMIZED)
    // ===============================
    public function index()
    {
        $companies = Company::select('id', 'name')->get(); // ✅ optimized

        $invoices = Billing::with([
            'company:id,name',
            'items:id,billingId,description,vehicleNo,quantity,rent,taxableAmount,vat,totalAmount'
        ])
        ->whereDate('date', now()->toDateString()) // ✅ today filter
        ->latest()
        ->simplePaginate(10); // ✅ faster pagination

        return view('billing.index', compact('companies', 'invoices'));
    }


    // ===============================
    // 🔍 FILTER (AJAX OPTIMIZED)
    // ===============================
    public function filter(Request $request)
    {
        try {

            $query = Billing::with([
                'company:id,name',
                'items:id,billingId,description,vehicleNo,quantity,rent,taxableAmount,vat,totalAmount'
            ]);

            if ($request->filled('invoiceNo')) {
                $query->where('invoiceNo', 'like', '%' . $request->invoiceNo . '%');
            }

            if ($request->filled('companyId')) {
                $query->where('companyId', $request->companyId);
            }

            if ($request->filled('status')) {
                $query->where('paymentStatus', $request->status);
            }

            if ($request->filled('date')) {
                $query->whereDate('date', $request->date);
            }

            $invoices = $query->latest()
                ->simplePaginate(10)
                ->withQueryString(); // ✅ keep filters in pagination

            return view('billing.partials.table', compact('invoices'))->render();

        } catch (\Throwable $e) {

            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }


    // ===============================
    // 🗑 DELETE INVOICE (OPTIMIZED)
    // ===============================
    public function deleteInvoice($id)
    {
        try {
            $billing = Billing::findOrFail($id);

            // delete image
            if ($billing->billImage) {
                Storage::disk('public')->delete($billing->billImage);
            }

            // delete items (direct query faster)
            BillingItem::where('billingId', $billing->id)->delete();

            // delete invoice
            $billing->delete();

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // ===============================
    // ✅ MARK AS PAID
    // ===============================
    public function markPaid($id)
    {
        $billing = Billing::findOrFail($id);

        $billing->update([
            'paymentStatus' => 'PAID'
        ]);

        return response()->json(['success' => true]);
    }


    // ===============================
    // 🗑 DELETE ITEM
    // ===============================
    public function deleteItem($id)
    {
        try {
            BillingItem::where('id', $id)->delete(); // ✅ faster than find()

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // ===============================
    // 🖨 PRINT INVOICE
    // ===============================
    public function print($id)
    {
        $invoice = Billing::with([
            'company:id,name',
            'items:id,billingId,description,vehicleNo,quantity,rent,taxableAmount,vat,totalAmount'
        ])->findOrFail($id);

        return view('billing.print', compact('invoice'));
    }
   
    public function updateItem(Request $request, $id)
{
    try {
        $item = BillingItem::findOrFail($id);

        $item->update([
            'description'     => $request->description,
            'vehicleNo'       => $request->vehicleNo,
            'quantity'        => $request->quantity,
            'rent'            => $request->rent,
            'taxableAmount'   => $request->taxableAmount,
            'vat'             => $request->vat,
            'totalAmount'     => $request->totalAmount,
        ]);

        return response()->json(['success' => true]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    
}