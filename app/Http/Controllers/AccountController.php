<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Purpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountController extends Controller
{
    public function index(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | GET PURPOSES (FOR FILTER DROPDOWN)
    |--------------------------------------------------------------------------
    */
    $purposes = Purpose::orderBy('name')->get();

    /*
    |--------------------------------------------------------------------------
    | USE COMMON LEDGER LOGIC (IMPORTANT)
    |--------------------------------------------------------------------------
    */
    $data = $this->getLedgerData($request);

    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */
    return view('accounts.index', array_merge(
        $data,
        ['purposes' => $purposes]
    ));
}


    public function store(Request $request)
    {
        try {

            $request->validate([
                'purpose_id' => 'required',
                'type' => 'required|in:INCOME,EXPENSE',
                'amount' => 'required|numeric|min:0',
                'date' => 'required|date'
            ]);

            $account = Account::create([
                'purposeId' => $request->purpose_id,
                'description' => $request->description,
                'type' => $request->type,
                'amount' => $request->amount,
                'date' => $request->date
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Saved successfully',
                'data' => $account
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Save failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
{
    try {

        $account = Account::findOrFail($id);
        $account->delete();

        return response()->json([
            'status' => true,
            'message' => 'Deleted successfully'
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => false,
            'message' => 'Delete failed'
        ], 500);
    }
}

public function exportPdf(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | USE SAME LOGIC AS TABLE (NO DUPLICATION)
    |--------------------------------------------------------------------------
    */
    $data = $this->getLedgerData($request);

    $pdf = Pdf::loadView('accounts.pdf', $data);

    return $pdf->download('ledger.pdf');
}



private function getLedgerData($request)
{
    $month = $request->month;
    $year = $request->year;
    $purposeId = $request->purpose_id;
    $type = $request->type;

    $query = Account::with('purpose');

    /*
    |--------------------------------------------------------------------------
    | FLEXIBLE FILTER LOGIC
    |--------------------------------------------------------------------------
    */

    // MONTH + YEAR
    if ($month && $year) {
        $query->whereMonth('date', $month)
              ->whereYear('date', $year);
    }

    // ONLY YEAR (YEARLY REPORT)
    elseif ($year) {
        $query->whereYear('date', $year);
    }

    // NO FILTER = ALL DATA (IMPORTANT FIX)

    // PURPOSE
    if ($purposeId) {
        $query->where('purposeId', $purposeId);
    }

    // TYPE
    if ($type) {
        $query->where('type', $type);
    }

    $accounts = $query->orderBy('date')->get();

    /*
    |--------------------------------------------------------------------------
    | OPENING BALANCE
    |--------------------------------------------------------------------------
    */

    $opening = 0;

    if ($month && $year) {
        $startDate = \Carbon\Carbon::create($year, $month, 1);
    } elseif ($year) {
        $startDate = \Carbon\Carbon::create($year, 1, 1);
    } else {
        $startDate = null;
    }

    if ($startDate) {
        $opening = Account::where('date', '<', $startDate)
            ->when($purposeId, fn($q) => $q->where('purposeId', $purposeId))
            ->sum(\DB::raw("
                CASE 
                    WHEN type = 'INCOME' THEN amount 
                    ELSE -amount 
                END
            "));
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATIONS
    |--------------------------------------------------------------------------
    */
    $income = $accounts->where('type', 'INCOME')->sum('amount');
    $expense = $accounts->where('type', 'EXPENSE')->sum('amount');

    /*
    |--------------------------------------------------------------------------
    | RUNNING BALANCE
    |--------------------------------------------------------------------------
    */
    $running = $opening;

    foreach ($accounts as $acc) {
        $running += ($acc->type == 'INCOME') ? $acc->amount : -$acc->amount;
        $acc->running_balance = $running;
    }

    /*
    |--------------------------------------------------------------------------
    | NET
    |--------------------------------------------------------------------------
    */
    $net = $opening + $income - $expense;

    return compact('accounts','opening','income','expense','net','month','year');
}
}
