<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Billing extends Model
{
    protected $table = 'billings'; // ✅ MUST match schema

    protected $fillable = [
        'invoiceNo',
        'companyId',
        'date',
        'billImage',
        'paymentStatus',
        'grandTotal'
    ];

    protected $casts = [
        'date' => 'datetime',
        'grandTotal' => 'float',
    ];

    protected $attributes = [
        'paymentStatus' => 'UNPAID', // ✅ matches enum
    ];

    // =========================
    // RELATIONSHIPS
    // =========================

    public function items()
    {
        return $this->hasMany(BillingItem::class, 'billingId'); // ✅ correct
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'companyId'); // ✅ correct
    }

    // =========================
    // ACCESSORS
    // =========================

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => number_format($this->grandTotal, 2)
        );
    }

    protected function paymentStatusBadge(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->paymentStatus === 'PAID'
                ? 'success'
                : 'danger'
        );
    }
}