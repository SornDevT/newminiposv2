<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockImportItem extends Model
{
    protected $fillable = [
        'stock_import_id',
        'product_id',
        'quantity',
        'cost_price',
        'total',
    ];

    public function stockImport()
    {
        return $this->belongsTo(StockImport::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
