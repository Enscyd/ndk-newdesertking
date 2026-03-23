<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Company;
use App\Models\BillingItem;

class Billing extends Model
{
    /*
    |--------------------------------------------------------------------------
    | TABLE
    |--------------------------------------------------------------------------
    */
    protected $table = 'billings'; // ✅ use lowercase (Laravel standard)


    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNMENT
    |--------------------------------------------------------------------------
    */
    protected $fillable = [
        'invoiceNo',
        'companyId',
        'date',
        'billImage',
        'paymentStatus', // ✅ ADDED
        'grandTotal'
    ];


    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    */
    protected $casts = [
        'date' => 'datetime',
        'grandTotal' => 'float',
    ];


    /*
    |--------------------------------------------------------------------------
    | DEFAULT ATTRIBUTES
    |--------------------------------------------------------------------------
    */
    protected $attributes = [
        'paymentStatus' => 'UnPaid', // ✅ default
    ];


    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    /**
     * Billing has many BillingItems
     */
    public function items()
    {
        return $this->hasMany(BillingItem::class, 'billingId');
    }

    /**
     * Billing belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'companyId');
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (OPTIONAL UI HELPERS)
    |--------------------------------------------------------------------------
    */

    /**
     * Get formatted total (optional)
     */
    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->grandTotal, 2)
        );
    }

    /**
     * Payment status badge class (for UI)
     */
    protected function paymentStatusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->paymentStatus === 'Paid'
                ? 'success'
                : 'danger'
        );
    }
}