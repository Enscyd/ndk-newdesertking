<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company;
use App\Models\BillingItem;

class Billing extends Model
{
    protected $table = 'Billings';

    protected $fillable = [
        'invoiceNo',
        'companyId',
        'date',
        'billImage',
        'grandTotal'
    ];

    // Laravel will automatically manage created_at and updated_at
    public $timestamps = true;

    /**
     * Relationship: Billing has many BillingItems
     */
    public function items()
    {
        return $this->hasMany(BillingItem::class, 'billingId');
    }

    /**
     * Relationship: Billing belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'companyId');
    }
}