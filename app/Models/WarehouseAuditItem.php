<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WarehouseAuditItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'audit_id',
        'product_id',
        'system_qty_good',
        'system_qty_obsolete',
        'counted_qty_good',
        'counted_qty_obsolete',
        'unit_cost',
        'notes',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    /**
     * Calculated Properties
     */

    protected function discrepancyGood(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->counted_qty_good - $this->system_qty_good,
        );
    }

    protected function discrepancyObsolete(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->counted_qty_obsolete - $this->system_qty_obsolete,
        );
    }

    protected function totalDiscrepancy(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->discrepancy_good + $this->discrepancy_obsolete,
        );
    }
    
    protected function totalDiscrepancyValue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total_discrepancy * $this->unit_cost,
        );
    }

    /**
     * Relationships
     */

    public function audit()
    {
        return $this->belongsTo(WarehouseAudit::class, 'audit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Methods
     */

    public function hasDiscrepancy(): bool
    {
        return $this->total_discrepancy !== 0;
    }

    /**
     * Scopes
     */

    public function scopeWithDiscrepancies($query)
    {
        return $query->where(function ($q) {
            $q->whereRaw('counted_qty_good != system_qty_good')
              ->orWhereRaw('counted_qty_obsolete != system_qty_obsolete');
        });
    }
}
