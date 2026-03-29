<?php

namespace App\Http\Controllers;

use App\Models\Suggestion;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = Suggestion::orderBy('id', 'desc')->get();
        return view('suggestions.index', compact('suggestions'));
    }

    public function create()
    {
        $suggestions = Suggestion::orderBy('id', 'desc')->get();
        return view('suggestions.index', compact('suggestions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $suggestion = Suggestion::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Suggestion added',
                'data' => $suggestion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error adding suggestion'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $suggestion = Suggestion::findOrFail($id);

            $suggestion->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Suggestion updated',
                'data' => $suggestion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Update failed'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $suggestion = Suggestion::findOrFail($id);
            $suggestion->delete();

            return response()->json([
                'status' => true,
                'message' => 'Suggestion deleted'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Delete failed'
            ], 500);
        }
    }
}