<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAccount extends Model
{
    protected $table = 'employee_accounts';

    protected $primaryKey = 'id';

     public $timestamps = false;

    protected $fillable = [
        'employeeId',
        'date',
        'type',
        'amount',
        'remarks'
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2'
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employeeId', 'id');
    }
}