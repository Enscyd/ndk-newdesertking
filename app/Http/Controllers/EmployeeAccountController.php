<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeAccount;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeAccountController extends Controller
{

    public function index(Request $request)
    {
        $employees = Employee::all();

        // No pre-loading needed (AJAX handles data)
        $entries = collect();
        $credits = 0;
        $debits  = 0;
        $net     = 0;

        return view('employee_accounts.index', compact(
            'employees','entries','credits','debits','net'
        ));
    }


    public function store(Request $request)
    {
        try {

            EmployeeAccount::create([
                'employeeId' => $request->employeeId,
                'date'       => Carbon::parse($request->date)->format('Y-m-d'),
                'type'       => $request->type,
                'amount'     => $request->amount,
                'remarks'    => $request->remarks,
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function filter(Request $request)
    {
        $employeeId = $request->employeeId;
        $month      = $request->month;

        $startDate = null;
        $endDate   = null;

        if ($month) {
            try {
                $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
                $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Invalid month format'
                ], 400);
            }
        }

        // ================= GET ALL DATA (IMPORTANT) =================
        $query = EmployeeAccount::query();

        if ($employeeId) {
            $query->where('employeeId', $employeeId);
        }

        $allEntries = $query->orderBy('date', 'asc')->get();

        // ================= RUNNING BALANCE =================
        $balance = 0;

        foreach ($allEntries as $entry) {

            if ($entry->type === 'CREDIT') {
                $balance += $entry->amount;
            } else {
                $balance -= $entry->amount;
            }

            // Attach dynamic field
            $entry->running_balance = $balance;
        }

        // ================= FILTER MONTH =================
        $entries = $allEntries;

        if ($startDate && $endDate) {
            $entries = $allEntries->filter(function ($item) use ($startDate, $endDate) {
                return Carbon::parse($item->date)->between($startDate, $endDate);
            })->values();
        }

        // ================= SUMMARY =================
        $credits = $entries->where('type', 'CREDIT')->sum('amount');
        $debits  = $entries->where('type', 'DEBIT')->sum('amount');
        $net     = $credits - $debits;

        return response()->json([
            'entries' => $entries,
            'credits' => $credits,
            'debits'  => $debits,
            'net'     => $net
        ]);
    }


    public function destroy($id)
    {
        try {

            $entry = EmployeeAccount::findOrFail($id);
            $entry->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }


public function pdf(Request $request)
{
    $employeeId = $request->employeeId;
    $month      = $request->month;

    if (!$employeeId || !$month) {
        return back()->with('error', 'Employee and Month required');
    }

    $employee = Employee::findOrFail($employeeId);

    $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $endDate   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    $entries = EmployeeAccount::where('employeeId', $employeeId)
        ->whereBetween('date', [$startDate, $endDate])
        ->orderBy('date', 'asc')
        ->get();

    $credits = $entries->where('type', 'CREDIT')->sum('amount');
    $debits  = $entries->where('type', 'DEBIT')->sum('amount');
    $net     = $credits - $debits;

    // OPTIONAL: Running balance inside PDF
    $balance = 0;
    foreach ($entries as $e) {
        if ($e->type == 'CREDIT') {
            $balance += $e->amount;
        } else {
            $balance -= $e->amount;
        }
        $e->running_balance = $balance;
    }

    $pdf = Pdf::loadView('employee_accounts.pdf', [
        'employee' => $employee,
        'entries'  => $entries,
        'credits'  => $credits,
        'debits'   => $debits,
        'net'      => $net,
        'month'    => $month
    ]);

    return $pdf->download('salary-slip-'.$employee->employeeName.'.pdf');
}
}