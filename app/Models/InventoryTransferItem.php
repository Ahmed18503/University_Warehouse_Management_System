<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransferItem extends Model
{
    // Item Type Constants
    const TYPE_GOOD = 'good';
    const TYPE_OBSOLETE = 'obsolete';

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'transfer_id',
        'product_id',
        'product_name',
        'product_code',
        'unit_name',
        'requested_quantity',
        'approved_quantity',
        'shipped_quantity',
        'received_quantity',
        'item_type',
        'status',
        'notes',
        'rejection_reason',
        'return_reason'
    ];

    protected $casts = [
        'requested_quantity' => 'decimal:2',
        'approved_quantity' => 'decimal:2',
        'shipped_quantity' => 'decimal:2',
        'received_quantity' => 'decimal:2',
    ];

    /**
     * Get the transfer this item belongs to
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(InventoryTransfer::class, 'transfer_id');
    }

    /**
     * Get the product being transferred
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if this item can transition to the given status
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $allowedTransitions = [
            self::STATUS_PENDING => [
                self::STATUS_APPROVED,
                self::STATUS_REJECTED
            ],
            self::STATUS_APPROVED => [
                self::STATUS_SHIPPED
            ],
            self::STATUS_SHIPPED => [
                self::STATUS_RECEIVED,
                self::STATUS_CANCELLED
            ],
            self::STATUS_RECEIVED => []  // Final state
        ];

        return isset($allowedTransitions[$this->status]) && 
               in_array($newStatus, $allowedTransitions[$this->status]);
    }

    public function validate()
    {
        // Check requested quantity is positive
        if ($this->requested_quantity <= 0) {
            throw new \InvalidArgumentException('يجب أن تكون الكمية المطلوبة أكبر من صفر');
        }

        // Check approved quantity doesn't exceed requested
        if ($this->approved_quantity !== null) {
            if ($this->approved_quantity <= 0) {
                throw new \InvalidArgumentException('يجب أن تكون الكمية المعتمدة أكبر من صفر');
            }
            if ($this->approved_quantity > $this->requested_quantity) {
                throw new \InvalidArgumentException('لا يمكن أن تتجاوز الكمية المعتمدة الكمية المطلوبة');
            }
        }

        // Check shipped quantity matches approved quantity
        if ($this->shipped_quantity !== null) {
            if ($this->shipped_quantity <= 0) {
                throw new \InvalidArgumentException('يجب أن تكون الكمية المشحونة أكبر من صفر');
            }
            if ($this->shipped_quantity !== $this->approved_quantity) {
                throw new \InvalidArgumentException('يجب أن تكون الكمية المشحونة مساوية للكمية المعتمدة');
            }
        }

        // Check received quantity matches shipped quantity
        if ($this->received_quantity !== null) {
            if ($this->received_quantity <= 0) {
                throw new \InvalidArgumentException('يجب أن تكون الكمية المستلمة أكبر من صفر');
            }
            if ($this->received_quantity !== $this->shipped_quantity) {
                throw new \InvalidArgumentException('يجب أن تكون الكمية المستلمة مساوية للكمية المشحونة');
            }
        }

        // Validate required product details
        if (empty($this->product_name)) {
            throw new \InvalidArgumentException('اسم المنتج مطلوب');
        }
        if (empty($this->product_code)) {
            throw new \InvalidArgumentException('كود المنتج مطلوب');
        }
        if (empty($this->unit_name)) {
            throw new \InvalidArgumentException('اسم الوحدة مطلوب');
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($item) {
            $item->validate();
        });
    }
}
