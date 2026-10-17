<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkshopBill;
use App\Models\WorkshopItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkshopBillController extends Controller
{
    /**
     * Ensure qty and rate columns exist on workshopitems table
     */
    protected function ensureWorkshopColumnsExist()
    {
        try {
            if (!Schema::hasColumn('workshopitems', 'qty')) {
                Schema::table('workshopitems', function ($table) {
                    $table->decimal('qty', 10, 2)->default(1);
                });
            }
            if (!Schema::hasColumn('workshopitems', 'rate')) {
                Schema::table('workshopitems', function ($table) {
                    $table->decimal('rate', 10, 3)->default(0);
                });
            }
        } catch (\Throwable $e) {
            // Silently ignore if already exists or permission issues
        }
    }

    /**
     * Display bills + filters + items
     */
    public function create(Request $request)
    {
        $this->ensureWorkshopColumnsExist();

        $query = WorkshopBill::with('items');

        // Filters
        if ($request->filled('vehicle_no')) {
            $query->where('vehicle_no', 'like', '%' . $request->vehicle_no . '%');
        }

        if ($request->filled('bill_no')) {
            $query->where('bill_no', 'like', '%' . $request->bill_no . '%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Sorting
        $allowedSorts = ['id', 'date', 'bill_no', 'vehicle_no', 'name', 'total_amount', 'payment_status'];
        $sort = in_array($request->get('sort'), $allowedSorts) ? $request->get('sort') : 'id';
        $direction = strtolower($request->get('direction')) === 'asc' ? 'asc' : 'desc';

        // Pagination
        $perPage = (int) $request->get('per_page', 25);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 25;
        }

        $bills = $query->orderBy($sort, $direction)->paginate($perPage)->withQueryString();

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

        return view('workshop.create', compact('bills', 'nextBillNo', 'sort', 'direction', 'perPage'));
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
            'items' => 'nullable'
        ]);

        $this->ensureWorkshopColumnsExist();

        $items = json_decode($request->items, true);

        // Fallback: if items array is empty but form inputs have description and rate/price
        if ((!is_array($items) || empty($items)) && $request->filled('description')) {
            $desc = trim($request->description);
            $q = $request->filled('qty') && (float)$request->qty > 0 ? (float)$request->qty : 1;
            $r = $request->filled('rate') ? (float)$request->rate : 0;
            $p = $request->filled('price') ? (float)$request->price : round($q * $r, 3);
            if ($r == 0 && $q > 0 && $p > 0) {
                $r = round($p / $q, 3);
            }
            if (!empty($desc) && ($p > 0 || $r > 0)) {
                $items = [
                    [
                        'description' => $desc,
                        'qty' => $q,
                        'rate' => $r,
                        'price' => $p,
                    ]
                ];
            }
        }

        if (!is_array($items) || empty($items)) {
            return back()->with('error', 'Please add at least one item');
        }

        DB::beginTransaction();

        try {
            $total = 0;

            foreach ($items as &$item) {
                if (empty($item['description'])) {
                    return back()->with('error', 'Invalid item description');
                }

                $qty = isset($item['qty']) && is_numeric($item['qty']) && (float)$item['qty'] > 0 ? (float)$item['qty'] : 1;
                $rate = isset($item['rate']) && is_numeric($item['rate']) ? (float)$item['rate'] : (isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] / $qty : 0);
                $price = isset($item['price']) && is_numeric($item['price']) ? (float)$item['price'] : round($qty * $rate, 3);

                $item['qty'] = $qty;
                $item['rate'] = $rate;
                $item['price'] = $price;

                $total += (float) $price;
            }
            unset($item);

            $total = round($total, 3);

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

            $hasQtyCol = Schema::hasColumn('workshopitems', 'qty');
            $hasRateCol = Schema::hasColumn('workshopitems', 'rate');

            // Create items
            foreach ($items as $item) {
                $itemData = [
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'billId' => $bill->id
                ];

                if ($hasQtyCol) {
                    $itemData['qty'] = $item['qty'];
                }
                if ($hasRateCol) {
                    $itemData['rate'] = $item['rate'];
                }

                WorkshopItem::create($itemData);
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
                    'message' => 'Already marked as paid',
                    'status' => 'PAID'
                ]);
            }

            $bill->update([
                'payment_status' => 'PAID'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marked as Paid successfully',
                'status' => 'PAID'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark bill as UNPAID
     */
    public function markUnpaid($id)
    {
        try {
            $bill = WorkshopBill::findOrFail($id);

            if ($bill->payment_status === 'UNPAID') {
                return response()->json([
                    'success' => true,
                    'message' => 'Already marked as unpaid',
                    'status' => 'UNPAID'
                ]);
            }

            $bill->update([
                'payment_status' => 'UNPAID'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Marked as Unpaid successfully',
                'status' => 'UNPAID'
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
            $billId = $item->billId;
            $item->delete();

            // Recalculate and update bill total
            $newTotal = WorkshopItem::where('billId', $billId)->sum('price');
            WorkshopBill::where('id', $billId)->update(['total_amount' => $newTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully',
                'new_total' => $newTotal
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
            $this->ensureWorkshopColumnsExist();

            $request->validate([
                'description' => 'required|string|max:255',
                'qty' => 'nullable|numeric|min:0.01',
                'rate' => 'nullable|numeric|min:0',
                'price' => 'nullable|numeric|min:0'
            ]);

            $item = WorkshopItem::findOrFail($id);

            $qty = $request->filled('qty') ? (float)$request->qty : (float)($item->qty ?? 1);
            if ($qty <= 0) $qty = 1;

            $rate = $request->filled('rate') 
                ? (float)$request->rate 
                : (float)($item->rate ?? $request->price ?? $item->price);
            
            $price = $request->filled('price') && !$request->filled('rate')
                ? (float)$request->price
                : round($qty * $rate, 3);

            $updateData = [
                'description' => $request->description,
                'price' => $price
            ];

            if (Schema::hasColumn('workshopitems', 'qty')) {
                $updateData['qty'] = $qty;
            }
            if (Schema::hasColumn('workshopitems', 'rate')) {
                $updateData['rate'] = $rate;
            }

            $item->update($updateData);

            // Recalculate and update bill total in database
            $newTotal = WorkshopItem::where('billId', $item->billId)->sum('price');
            WorkshopBill::where('id', $item->billId)->update(['total_amount' => $newTotal]);

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
                'new_total' => $newTotal,
                'item' => [
                    'id' => $item->id,
                    'description' => $item->description,
                    'qty' => $qty,
                    'rate' => $rate,
                    'price' => $price
                ]
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

        // Recalculate and synchronize total_amount from items
        $calculatedTotal = $bill->items->sum(function ($item) {
            $itemQty = isset($item->qty) && (float) $item->qty > 0 ? (float) $item->qty : 1;
            $itemRate = isset($item->rate) && (float) $item->rate > 0 ? (float) $item->rate : (float) ($item->price ?? 0);
            return isset($item->price) ? (float) $item->price : ($itemQty * $itemRate);
        });

        if (abs((float) $bill->total_amount - $calculatedTotal) > 0.0001) {
            $bill->update(['total_amount' => $calculatedTotal]);
            $bill->total_amount = $calculatedTotal;
        }

        $pdf = Pdf::loadView('workshop.pdf', compact('bill'));

        return $pdf->download('bill-' . $bill->bill_no . '.pdf');
    }

    /**
     * Instant browser print invoice
     */
    public function printInvoice($id)
    {
        $bill = WorkshopBill::with('items')->findOrFail($id);

        $calculatedTotal = $bill->items->sum(function ($item) {
            $itemQty = isset($item->qty) && (float) $item->qty > 0 ? (float) $item->qty : 1;
            $itemRate = isset($item->rate) && (float) $item->rate > 0 ? (float) $item->rate : (float) ($item->price ?? 0);
            return isset($item->price) ? (float) $item->price : ($itemQty * $itemRate);
        });

        if (abs((float) $bill->total_amount - $calculatedTotal) > 0.0001) {
            $bill->update(['total_amount' => $calculatedTotal]);
            $bill->total_amount = $calculatedTotal;
        }

        return view('workshop.print', compact('bill'));
    }

    /**
     * Edit bill
     */
    public function edit(Request $request, $id)
    {
        $bill = WorkshopBill::with('items')->findOrFail($id);

        $allowedSorts = ['id', 'date', 'bill_no', 'vehicle_no', 'name', 'total_amount', 'payment_status'];
        $sort = in_array($request->get('sort'), $allowedSorts) ? $request->get('sort') : 'id';
        $direction = strtolower($request->get('direction')) === 'asc' ? 'asc' : 'desc';

        $perPage = (int) $request->get('per_page', 25);
        if ($perPage < 5 || $perPage > 100) {
            $perPage = 25;
        }

        $bills = WorkshopBill::with('items')
            ->orderBy($sort, $direction)
            ->paginate($perPage)
            ->withQueryString();

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

        return view('workshop.create', compact('bill', 'bills', 'nextBillNo', 'sort', 'direction', 'perPage'));
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