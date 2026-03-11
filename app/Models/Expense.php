<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

protected $table = "Expenses";

public $timestamps = false;

protected $fillable = [
'employeeId',
'truckId',
'expenseDate',
'category',
'details',
'amount',
'image'
];


/* DRIVER RELATION */

public function employee()
{
return $this->belongsTo(Employee::class,'employeeId');
}


/* TRUCK RELATION */

public function truck()
{
return $this->belongsTo(Truck::class,'truckId');
}

}