<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeAccount;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeAccountController extends Controller
{
    public function index()
    {
        $employees = Employee::all();

        return view('employee_accounts.index', [
            'employees' => $employees,
            'entries' => collect(),
            'credits' => 0,
            'debits' => 0,
            'net' => 0
        ]);
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $request->validate([
            'employeeId' => 'required|exists:employees,id',
            'month' => 'required|integer|min:1|max:12',
            'date' => 'required|date',
            'type' => 'required|in:CREDIT,DEBIT',
            'amount' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:255'
        ]);

        try {
            EmployeeAccount::create([
                'employeeId' => (int) $request->employeeId,
                'month' => (int) $request->month,
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
                'type' => $request->type,
                'amount' => $request->amount,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Entry saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Save failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // ================= FILTER =================
    public function filter(Request $request)
    {
        try {
            $query = EmployeeAccount::query();

            if ($request->filled('employeeId')) {
                $query->where('employeeId', (int) $request->employeeId);
            }

            if ($request->filled('month')) {
                $query->where('month', (int) $request->month);
            }

            $entries = $query
                ->orderBy('date', 'asc')
                ->get();

            $balance = 0;

            foreach ($entries as $entry) {
                $balance += $entry->type === 'CREDIT'
                    ? $entry->amount
                    : -$entry->amount;

                $entry->running_balance = $balance;
            }

            $credits = $entries->where('type', 'CREDIT')->sum('amount');
            $debits = $entries->where('type', 'DEBIT')->sum('amount');
            $net = $credits - $debits;

            return response()->json([
                'entries' => $entries,
                'credits' => $credits,
                'debits' => $debits,
                'net' => $net
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Filter failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        try {
            $entry = EmployeeAccount::findOrFail($id);
            $entry->delete();

            return response()->json([
                'success' => true,
                'message' => 'Entry deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Delete failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // ================= PDF =================
    public function pdf(Request $request)
    {
        $request->validate([
            'employeeId' => 'required|exists:employees,id',
            'month' => 'nullable'
        ]);

        try {
            $employeeId = (int) $request->employeeId;
            $month = $request->month ? (int) $request->month : null;

            $employee = Employee::findOrFail($employeeId);

            $query = EmployeeAccount::where('employeeId', $employeeId);

            if ($month) {
                $query->where('month', $month);
            }

            $entries = $query
                ->orderBy('date', 'asc')
                ->get();

            $credits = $entries->where('type', 'CREDIT')->sum('amount');
            $debits = $entries->where('type', 'DEBIT')->sum('amount');
            $net = $credits - $debits;

            $balance = 0;

            foreach ($entries as $entry) {
                $balance += $entry->type === 'CREDIT'
                    ? $entry->amount
                    : -$entry->amount;

                $entry->running_balance = $balance;
            }

            $monthName = $month
                ? Carbon::create()->month((int) $month)->format('F')
                : 'All Months';

            $pdf = Pdf::loadView('employee_accounts.pdf', [
                'employee' => $employee,
                'entries' => $entries,
                'credits' => $credits,
                'debits' => $debits,
                'net' => $net,
                'monthName' => $monthName
            ]);

            $fileName = 'salary-slip-' . $employee->employeeName;

            if ($month) {
                $fileName .= '-' . strtolower($monthName);
            }

            $fileName .= '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with(
                'error',
                'PDF generation failed: ' . $e->getMessage()
            );
        }
    }
}