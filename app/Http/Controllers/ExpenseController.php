<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Employee;
use App\Models\Truck;

class ExpenseController extends Controller
{

public function index()
{
    $employees = Employee::all();
    $trucks = Truck::all();

    $expenses = Expense::orderBy('expenseDate','desc')->get();

    return view('expense.expense',compact(
        'employees',
        'trucks',
        'expenses'
    ));
}


public function store(Request $request)
{

$image = null;

if($request->hasFile('image')){
$image = $request->file('image')->store('bills','public');
}

Expense::create([
'employeeId'=>$request->employeeId,
'truckId'=>$request->truckId,
'expenseDate'=>$request->expenseDate,
'category'=>$request->category,
'details'=>$request->details,
'amount'=>$request->amount,
'image'=>$image
]);

return response()->json(['success'=>true]);

}


public function filter(Request $request)
{

$search = $request->search;

$expenses = Expense::where('category','like','%'.$search.'%')
->orWhere('details','like','%'.$search.'%')
->orderBy('expenseDate','desc')
->get();

return view('partials.expense_rows',compact('expenses'))->render();

}


public function edit($id)
{

$expense = Expense::find($id);

return response()->json($expense);

}


public function update(Request $request,$id)
{

$expense = Expense::find($id);

$image = $expense->image;

if($request->hasFile('image')){
$image = $request->file('image')->store('bills','public');
}

$expense->update([
'employeeId'=>$request->employeeId,
'truckId'=>$request->truckId,
'expenseDate'=>$request->expenseDate,
'category'=>$request->category,
'details'=>$request->details,
'amount'=>$request->amount,
'image'=>$image
]);

return response()->json(['success'=>true]);

}


public function destroy($id)
{

Expense::destroy($id);

return response()->json(['success'=>true]);

}


public function fetchExpenses()
{

$expenses = Expense::orderBy('expenseDate','desc')->get();

return view('partials.expense_rows',compact('expenses'))->render();

}

}