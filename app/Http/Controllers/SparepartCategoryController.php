<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SparepartCategory;

class SparepartCategoryController extends Controller
{
    public function index()
    {
        $categories = SparepartCategory::all();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:sparepart_category,name'
        ]);

        $category = SparepartCategory::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = SparepartCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:sparepart_category,name,' . $id
        ]);

        $category->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        SparepartCategory::destroy($id);

        return response()->json(['success' => true]);
    }
}