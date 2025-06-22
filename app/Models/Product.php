<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
        'product_category_id',
        'name',
        'description',
        'unit_id',
        'price',
    ];

    function unit()
    {
        return $this->belongsTo(Unit::class);
    }
    
    function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    function warehouseProducts()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_products')->withPivot(['quantity_good', 'min_stock_level']);
    }

    protected static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        $model->code = self::generateUniqueCode();
    });
}

protected static function generateUniqueCode($length = 8): string
{
    do {
        $code = Str::upper(Str::random($length));
    } while (self::where('code', $code)->exists());

    return $code;
}
    
    // function orders()
    // {
    //     return $this->belongsToMany(Order::class, 'order_product')->withPivot(['quantity', 'unit_price']);
    // }
    // function invoices()
    // {
    //     return $this->belongsToMany(Invoice::class,  'invoice_product')->withPivot(['quantity', 'unit_price']);
    // }

}
