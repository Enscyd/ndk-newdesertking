<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

protected $table = "expenses";

public $timestamps = true;
const CREATED_AT = 'createdAt';
const UPDATED_AT = null;

protected $fillable = [
'employeeId',
'truckId',
'expenseDate',
'category',
'details',
'amount',
'image',
'createdAt'
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