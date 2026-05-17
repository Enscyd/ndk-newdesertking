<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SparepartSupplier;

class SparepartSupplierController extends Controller
{
    public function index()
    {
        return response()->json(SparepartSupplier::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $supplier = SparepartSupplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        return response()->json(['success' => true, 'data' => $supplier]);
    }

    public function update(Request $request, $id)
    {
        $supplier = SparepartSupplier::findOrFail($id);

        $supplier->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        SparepartSupplier::destroy($id);

        return response()->json(['success' => true]);
    }
}