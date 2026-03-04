<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validate input
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 2. Insert into "Companies" table (via Eloquent Model)
        Company::create($data);

        // 3. Send success response
        return back()->with('message', 'Company successfully saved to MySQL!');
    }
}