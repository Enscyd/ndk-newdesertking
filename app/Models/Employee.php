<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';

    protected $fillable = [
        'employeeName',
        'employeePhoneNo'
    ];

    public $timestamps = false;
}