<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingItem extends Model
{
    protected $table = 'BillingItems';

    protected $fillable = [
        'billingId',
        'tripId',        // required to track which trip was billed
        'description',
        'vehicleNo',
        'quantity',
        'rent',
        'taxableAmount',
        'vat',
        'totalAmount'
    ];

    // Enable Laravel timestamps (created_at, updated_at)
    public $timestamps = true;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billingId');
    }

    /*
    |--------------------------------------------------------------------------
    | Optional: Trip Relationship (recommended)
    |--------------------------------------------------------------------------
    */

    public function trip()
    {
        return $this->belongsTo(\App\Models\Trip::class, 'tripId');
    }
}