<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SparepartStock;
use App\Models\Sparepart;

class SparepartStockController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'sparepart_id' => 'required|exists:sparepart,id',
            'type' => 'required|in:IN,OUT',
            'quantity' => 'required|numeric|min:1'
        ]);

        // Check stock before OUT
        if ($request->type == 'OUT') {
            $sparepart = Sparepart::find($request->sparepart_id);

            if ($sparepart->quantity < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock'
                ], 400);
            }
        }

        $stock = SparepartStock::create([
            'sparepart_id' => $request->sparepart_id,
            'supplier_id' => $request->supplier_id,
            'type' => $request->type,
            'quantity' => $request->quantity,
            'note' => $request->note
        ]);

        return response()->json(['success' => true, 'data' => $stock]);
    }

    public function history($id)
    {
        $history = SparepartStock::with('supplier')
                    ->where('sparepart_id', $id)
                    ->latest()
                    ->get();

        return response()->json($history);
    }

    public function destroy($id)
    {
        SparepartStock::destroy($id);

        return response()->json(['success' => true]);
    }
}