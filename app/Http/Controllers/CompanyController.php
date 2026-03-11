<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Show page
    public function create()
    {
        $companies = Company::orderBy('id','desc')->get();

        return view('company.add', compact('companies'));
    }

        public function store(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255'
            ]);

            Company::create($validated);

            return redirect()->back()->with('message','Company successfully saved!');
        }

    // Update company
        public function update(Request $request, $id)
        {
            $request->validate([
                'name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255'
            ]);

            $company = Company::findOrFail($id);

            $company->update([
                'name' => $request->name,
                'address' => $request->address
            ]);

            return response()->json([
                'success' => true
            ]);
        }

    // Delete company
    public function destroy($id)
    {
        $company = Company::findOrFail($id);

        $company->delete();

        return redirect()->back()->with('message','Company deleted successfully!');
    }
}