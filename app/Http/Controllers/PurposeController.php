<?php

namespace App\Http\Controllers;

use App\Models\Purpose;
use Illuminate\Http\Request;

class PurposeController extends Controller
{
    /**
     * Show Purpose Page (List)
     */
    public function index()
    {
        $purposes = Purpose::orderBy('id', 'desc')->get();

        return view('purposes.index', compact('purposes'));
    }


    /**
     * Show Create Page
     */
    public function create()
    {
        $purposes = Purpose::orderBy('id', 'desc')->get();

        return view('purposes.index', compact('purposes'));
    }


    /**
     * Store New Purpose (AJAX)
     */
    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $purpose = Purpose::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Purpose created successfully',
                'data' => $purpose
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Failed to create purpose'
            ], 500);

        }
    }


    /**
     * Update Purpose
     */
    public function update(Request $request, $id)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $purpose = Purpose::findOrFail($id);

            $purpose->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Purpose updated successfully',
                'data' => $purpose
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Update failed'
            ], 500);

        }
    }


    /**
     * Delete Purpose
     */
    public function destroy($id)
    {
        try {

            $purpose = Purpose::findOrFail($id);
            $purpose->delete();

            return response()->json([
                'status' => true,
                'message' => 'Purpose deleted successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Delete failed'
            ], 500);

        }
    }
}