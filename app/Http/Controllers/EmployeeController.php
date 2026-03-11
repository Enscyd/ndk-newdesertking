<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

class EmployeeController extends Controller
{

    public function index()
    {
        $employees = Employee::all();

        return view('employee.employee', compact('employees'));
    }


    public function store(Request $request)
    {
        $employee = Employee::create([
            'employeeName' => $request->employeeName,
            'employeePhoneNo' => $request->employeePhoneNo
        ]);

        return response()->json([
            'success' => true,
            'data' => $employee
        ]);
    }


    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $employee->update([
            'employeeName' => $request->employeeName,
            'employeePhoneNo' => $request->employeePhoneNo
        ]);

        return response()->json([
            'success' => true
        ]);
    }


    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);

        $employee->delete();

        return response()->json([
            'success' => true
        ]);
    }

}