<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sparepart;
use App\Models\SparepartCategory;
use App\Models\SparepartSupplier;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        $query = Sparepart::with('category');

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('part_number', 'like', '%' . $request->search . '%');
        }

        $spareparts = $query->get();
        $categories = SparepartCategory::all();
        $suppliers = SparepartSupplier::all();

        return view('sparepart.index', compact('spareparts','categories','suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'part_number' => 'required|unique:sparepart,part_number',
        ]);

        $sparepart = Sparepart::create([
            'name' => $request->name,
            'part_number' => $request->part_number,
            'category_id' => $request->category_id
        ]);

        return response()->json(['success' => true, 'data' => $sparepart]);
    }

    public function show($id)
    {
        $sparepart = Sparepart::with('category')->findOrFail($id);
        return response()->json($sparepart);
    }

    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'part_number' => 'required|unique:sparepart,part_number,' . $id,
        ]);

        $sparepart->update([
            'name' => $request->name,
            'part_number' => $request->part_number,
            'category_id' => $request->category_id
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $sparepart->delete();

        return response()->json(['success' => true]);
    }
}