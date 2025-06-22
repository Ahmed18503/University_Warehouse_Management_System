<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class InventoryTransfer extends Model
{
    // Status Constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_SOURCE_APPROVAL = 'pending_source_approval';
    const STATUS_SOURCE_REJECTED = 'source_rejected';
    const STATUS_SOURCE_APPROVED = 'source_approved';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_RECEIVED = 'received';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'source_warehouse_id',
        'destination_warehouse_id',
        'created_by_user_id',
        'source_approved_by_user_id',
        'destination_approved_by_user_id',
        'shipped_by_user_id',
        'received_by_user_id',
        'status',
        'notes',
        'rejection_reason',
        'return_notes',
        'submitted_at',
        'source_approved_at',
        'shipped_at',
        'received_at',
        'completed_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'source_approved_at' => 'datetime',
        'shipped_at' => 'datetime',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function validate()
    {
        if ($this->source_warehouse_id === $this->destination_warehouse_id) {
            throw new \InvalidArgumentException('لا يمكن أن يكون المخزن المصدر والوجهة نفس المخزن');
        }
    }

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($transfer) {
            $transfer->validate();
        });
    }

    /**
     * Check if the transfer can transition to the given status
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $allowedTransitions = [
            self::STATUS_DRAFT => [
                self::STATUS_PENDING_SOURCE_APPROVAL,
                self::STATUS_CANCELLED
            ],
            self::STATUS_PENDING_SOURCE_APPROVAL => [
                self::STATUS_SOURCE_APPROVED,
                self::STATUS_SOURCE_REJECTED
            ],
            self::STATUS_SOURCE_APPROVED => [
                self::STATUS_SHIPPED,
                self::STATUS_CANCELLED
            ],
            self::STATUS_SHIPPED => [
                self::STATUS_RECEIVED,
                self::STATUS_CANCELLED
            ]
        ];

        return isset($allowedTransitions[$this->status]) && 
               in_array($newStatus, $allowedTransitions[$this->status]);
    }

    /**
     * Approve transfer items at source warehouse
     */
    public function approveItems(array $approvedQuantities, int $approverId)
    {
        if ($this->status !== self::STATUS_PENDING_SOURCE_APPROVAL) {
            throw new \InvalidArgumentException('لا يمكن الموافقة على النقل في هذه الحالة');
        }

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                if (!isset($approvedQuantities[$item->id])) {
                    throw new \InvalidArgumentException('يجب تحديد الكمية المعتمدة لكل الأصناف');
                }

                $approvedQty = $approvedQuantities[$item->id];
                if ($approvedQty > $item->requested_quantity) {
                    throw new \InvalidArgumentException('لا يمكن أن تتجاوز الكمية المعتمدة الكمية المطلوبة');
                }

                $item->approved_quantity = $approvedQty;
                $item->status = InventoryTransferItem::STATUS_APPROVED;
                $item->save();
            }

            $this->status = self::STATUS_SOURCE_APPROVED;
            $this->source_approved_by_user_id = $approverId;
            $this->source_approved_at = now();
            $this->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Ship the transfer and deduct quantities from source warehouse
     */
    public function ship(int $shipperId)
    {
        if ($this->status !== self::STATUS_SOURCE_APPROVED) {
            throw new \InvalidArgumentException('لا يمكن شحن النقل في هذه الحالة');
        }

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                // Deduct from source warehouse
                $sourceProduct = WarehouseProduct::where('warehouse_id', $this->source_warehouse_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                $columnToUpdate = $item->item_type === InventoryTransferItem::TYPE_GOOD ? 'quantity_good' : 'quantity_obsolete';
                
                if ($sourceProduct->$columnToUpdate < $item->approved_quantity) {
                    throw new \InvalidArgumentException('الكمية المتوفرة غير كافية في المخزن المصدر');
                }

                $sourceProduct->$columnToUpdate -= $item->approved_quantity;
                $sourceProduct->save();

                $item->shipped_quantity = $item->approved_quantity;
                $item->status = InventoryTransferItem::STATUS_SHIPPED;
                $item->save();
            }

            $this->status = self::STATUS_SHIPPED;
            $this->shipped_by_user_id = $shipperId;
            $this->shipped_at = now();
            $this->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Receive the transfer and add quantities to destination warehouse
     */
    public function receive(int $receiverId)
    {
        if ($this->status !== self::STATUS_SHIPPED) {
            throw new \InvalidArgumentException('لا يمكن استلام النقل في هذه الحالة');
        }

        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                // Add to destination warehouse
                $destProduct = WarehouseProduct::firstOrCreate(
                    [
                        'warehouse_id' => $this->destination_warehouse_id,
                        'product_id' => $item->product_id
                    ],
                    [
                        'quantity_good' => 0,
                        'quantity_obsolete' => 0
                    ]
                );

                $columnToUpdate = $item->item_type === InventoryTransferItem::TYPE_GOOD ? 'quantity_good' : 'quantity_obsolete';
                $destProduct->$columnToUpdate += $item->shipped_quantity;
                $destProduct->save();

                $item->received_quantity = $item->shipped_quantity;
                $item->status = InventoryTransferItem::STATUS_RECEIVED;
                $item->save();
            }

            $this->status = self::STATUS_RECEIVED;
            $this->received_by_user_id = $receiverId;
            $this->received_at = now();
            $this->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel the transfer
     */
    public function cancel($reason = null)
    {
        if (!in_array($this->status, [
            self::STATUS_DRAFT,
            self::STATUS_SOURCE_APPROVED,
            self::STATUS_SHIPPED
        ])) {
            throw new \InvalidArgumentException('لا يمكن إلغاء النقل في هذه الحالة');
        }

        // If cancelled after shipping, return quantities to source warehouse
        if ($this->status === self::STATUS_SHIPPED) {
            DB::beginTransaction();
            try {
                foreach ($this->items as $item) {
                    $sourceProduct = WarehouseProduct::where('warehouse_id', $this->source_warehouse_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    $columnToUpdate = $item->item_type === InventoryTransferItem::TYPE_GOOD ? 'quantity_good' : 'quantity_obsolete';
                    $sourceProduct->$columnToUpdate += $item->shipped_quantity;
                    $sourceProduct->save();

                    $item->status = InventoryTransferItem::STATUS_CANCELLED;
                    $item->save();
                }

                $this->status = self::STATUS_CANCELLED;
                $this->rejection_reason = $reason;
                $this->save();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } else {
            $this->status = self::STATUS_CANCELLED;
            $this->rejection_reason = $reason;
            $this->save();

            foreach ($this->items as $item) {
                $item->status = InventoryTransferItem::STATUS_CANCELLED;
                $item->save();
            }
        }
    }

    /**
     * Get all items in this transfer
     */
    public function items(): HasMany
    {
        return $this->hasMany(InventoryTransferItem::class, 'transfer_id');
    }

    /**
     * Get the source warehouse
     */
    public function sourceWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'source_warehouse_id');
    }

    /**
     * Get the destination warehouse
     */
    public function destinationWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    /**
     * Get the user who created the transfer
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the user who approved the transfer at source
     */
    public function sourceApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'source_approved_by_user_id');
    }

    /**
     * Get the user who approved the transfer at destination
     */
    public function destinationApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'destination_approved_by_user_id');
    }

    /**
     * Get the user who shipped the transfer
     */
    public function shipper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shipped_by_user_id');
    }

    /**
     * Get the user who received the transfer
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by_user_id');
    }
}
