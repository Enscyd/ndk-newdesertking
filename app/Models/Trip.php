<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $table = 'trips';

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
        'image',
        'overrides',
    ];

    public $timestamps = false;

    protected $casts = [
        'tripDate' => 'datetime',
        'overrides' => 'array',
    ];

    private const ALLOWED_OVERRIDE_FIELDS = ['truck'];

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

    public function billingItems()
    {
        return $this->hasMany(\App\Models\BillingItem::class,'tripId');
    }

    public function displayTruckNumber(): string
    {
        $override = $this->overrides['truck'] ?? null;

        if (is_string($override) && trim($override) !== '') {
            return trim($override);
        }

        return $this->truck->truckNumber ?? '';
    }

    public function isTruckOverridden(): bool
    {
        $override = $this->overrides['truck'] ?? null;

        return is_string($override) && trim($override) !== '';
    }

    public function setFieldOverride(string $field, ?string $value): void
    {
        if (!in_array($field, self::ALLOWED_OVERRIDE_FIELDS, true)) {
            return;
        }

        $overrides = $this->overrides ?? [];

        if ($value === null || trim($value) === '') {
            unset($overrides[$field]);
        } else {
            $overrides[$field] = trim($value);
        }

        $this->overrides = empty($overrides) ? null : $overrides;
        $this->save();
    }

    public function clearFieldOverride(string $field): void
    {
        $this->setFieldOverride($field, null);
    }
}
