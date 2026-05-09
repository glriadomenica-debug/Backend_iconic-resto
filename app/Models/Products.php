<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'category_id',
        'product_name',
        'price',
        'stock',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetails::class, 'product_id');
    }
}
