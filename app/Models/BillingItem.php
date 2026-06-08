<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    protected $table = 'billingitems'; // ✅ VERY IMPORTANT

    protected $fillable = [
        'billingId',
        'tripId',
        'tripDate',
        'description',
        'vehicleNo',
        'quantity',
        'rent',
        'taxableAmount',
        'vat',
        'totalAmount'
    ];

    protected $casts = [
        'tripDate' => 'datetime',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billingId'); // ✅ correct
    }
}