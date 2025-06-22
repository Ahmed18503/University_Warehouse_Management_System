<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseAudit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'auditor_id',
        'audit_date',
        'completed_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'audit_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function items()
    {
        return $this->hasMany(WarehouseAuditItem::class, 'audit_id');
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function complete(): void
    {
        if (!$this->isEditable()) {
            throw new \Exception('This audit cannot be completed in its current state.');
        }

        $this->status = self::STATUS_COMPLETED;
        $this->completed_at = now();
        $this->save();
    }

    public function cancel(): void
    {
        if (!$this->canBeCancelled()) {
            throw new \Exception('This audit cannot be cancelled.');
        }

        $this->status = self::STATUS_CANCELLED;
        $this->save();
    }

    public function getTotalDiscrepancyValue(): float
    {
        return $this->items->sum('total_discrepancy_value');
    }

    public function getItemsWithDiscrepancies()
    {
        return $this->items->filter(function ($item) {
            return $item->total_discrepancy !== 0;
        });
    }

    public function scopeInWarehouse($query, $warehouseId)
    {
        return $query->where('warehouse_id', $warehouseId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }
}
