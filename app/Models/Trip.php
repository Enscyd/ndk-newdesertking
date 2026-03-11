<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'Trips';

    protected $fillable = [
        'companyId',
        'destinationId',
        'employeeId',
        'truckId',
        'tripType',
        'driverAmount',
        'tripDate',
        'tripAmount',
        'isOmani',
        'omaniName',
        'omaniAmount',
        'image'
    ];

    public $timestamps = false;

public function company()
{
    return $this->belongsTo(Company::class,'companyId');
}

public function destination()
{
    return $this->belongsTo(Destination::class,'destinationId');
}

public function employee()
{
    return $this->belongsTo(Employee::class,'employeeId');
}

public function truck()
{
    return $this->belongsTo(Truck::class,'truckId');
}
}