<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDeletionRequest extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity_to_delete',
        'reason',
        'requested_by_user_id',
        'approved_by_user_id',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
} 