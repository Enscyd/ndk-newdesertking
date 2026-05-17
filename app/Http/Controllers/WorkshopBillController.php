<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkshopBill;
use App\Models\WorkshopItem;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkshopBillController extends Controller
{
    /**
     * Display bills + filters + items
     */
    public function create(Request $request)
    {
        $query = WorkshopBill::with('items');

        // Filters
        if ($request->filled('vehicle_no')) {
            $query->where('vehicle_no', 'like', '%' . $request->vehicle_no . '%');
        }

        if ($request->filled('bill_no')) {
            $query->where('bill_no', 'like', '%' . $request->bill_no . '%');
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $bills = $query->orderBy('id', 'desc')->get();

        /**
         * Auto next bill number
         * Format: NDK-2026-001
         */
        $year = date('Y');

        $lastBill = WorkshopBill::where('bill_no', 'like', "NDK-$year-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->bill_no, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nextBillNo = 'NDK-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('workshop.create', compact('bills', 'nextBillNo'));
    }

    /**
     * Store new bill + items
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_no' => 'required|string|max:50',
            'date' => 'required|date',
            'payment_status' => 'required|in:PAID,UNPAID',
            'items' => 'required'
        ]);

        DB::beginTransaction();

        try {
            $items = json_decode($request->items, true);

            if (!is_array($items) || empty($items)) {
                return back()->with('error', 'Please add at least one item');
            }

            $total = 0;

            foreach ($items as $item) {
                if (
                    empty($item['description']) ||
                    !isset($item['price']) ||
                    !is_numeric($item['price'])
                ) {
                    return back()->with('error', 'Invalid item data');
                }

                $total += (float) $item['price'];
            }

            /**
             * Auto bill number generate
             * Format: NDK-2026-001
             */
            $year = date('Y');

            $lastBill = WorkshopBill::where('bill_no', 'like', "NDK-$year-%")
                ->lockForUpdate()
                ->orderBy('id', 'desc')
                ->first();

            if ($lastBill) {
                $lastNumber = (int) substr($lastBill->bill_no, -3);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $billNo = 'NDK-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Create bill
            $bill = WorkshopBill::create([
                'bill_no' => $billNo,
                'vehicle_no' => $request->vehicle_no,
                'name' => $request->name,
                'date' => $request->date,
                'payment_status' => $request->payment_status,
                'total_amount' => $total,
            ]);

            // Create items
            foreach ($items as $item) {
                WorkshopItem::create([
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'billId' => $bill->id
                ]);
            }

            DB::commit();

            return redirect()->route('workshop.create')
                ->with('success', 'Bill saved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Mark bill as PAID
     */
    public function markPaid($id)
    {
        try {
            $bill = WorkshopBill::findOrFail($id);

            if ($bill->payment_status === 'PAID') {
                return response()->json([
                    'success' => true,
                    'message' => 'Already marked as paid'
                ]);
            }

            $bill->update([
                'payment_status' => 'PAID'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marked as Paid successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete bill + items
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $bill = WorkshopBill::findOrFail($id);

            WorkshopItem::where('billId', $bill->id)->delete();
            $bill->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bill deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete single item
     */
    public function deleteItem($id)
    {
        try {
            $item = WorkshopItem::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update single item (modal)
     */
    public function updateItem(Request $request, $id)
    {
        try {
            $request->validate([
                'description' => 'required|string|max:255',
                'price' => 'required|numeric|min:0'
            ]);

            $item = WorkshopItem::findOrFail($id);

            $item->update([
                'description' => $request->description,
                'price' => $request->price
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate PDF
     */
    public function generatePDF($id)
    {
        $bill = WorkshopBill::with('items')->findOrFail($id);

        $pdf = Pdf::loadView('workshop.pdf', compact('bill'));

        return $pdf->download('bill-' . $bill->bill_no . '.pdf');
    }

    /**
     * Edit bill
     */
    public function edit($id)
    {
        $bill = WorkshopBill::with('items')->findOrFail($id);

        $bills = WorkshopBill::with('items')
            ->orderBy('id', 'desc')
            ->get();

        $year = date('Y');

        $lastBill = WorkshopBill::where('bill_no', 'like', "NDK-$year-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBill) {
            $lastNumber = (int) substr($lastBill->bill_no, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $nextBillNo = 'NDK-' . $year . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        return view('workshop.create', compact('bill', 'bills', 'nextBillNo'));
    }

    public function itemSuggestions(Request $request)
    {
        $search = $request->query('q');

        $items = DB::table('suggestions')
            ->where('name', 'LIKE', "%{$search}%")
            ->limit(10)
            ->pluck('name');

        return response()->json($items);
    }
}