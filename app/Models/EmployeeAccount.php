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
        'month',   // ✅ NEW FIELD ADDED
        'date',
        'type',
        'amount',
        'remarks'
    ];

    protected $casts = [
        'month'  => 'integer',   // ✅ NEW CAST
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

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR (OPTIONAL)
    |--------------------------------------------------------------------------
    */
    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->month)->format('F');
    }
}