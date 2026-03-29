<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    protected $table = 'BillingItems'; // ✅ VERY IMPORTANT

    protected $fillable = [
        'billingId',
        'tripId',
        'description',
        'vehicleNo',
        'quantity',
        'rent',
        'taxableAmount',
        'vat',
        'totalAmount'
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billingId'); // ✅ correct
    }
}