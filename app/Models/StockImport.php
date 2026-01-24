<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockImport extends Model
{
    protected $fillable = [
        'supplier_id',
        'user_id',
        'invoice_number',
        'total_cost',
        'import_date',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stockImportItems()
    {
        return $this->hasMany(StockImportItem::class);
    }
}

