<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = [
        'category_name'
    ];

    public function product()
    {
        return $this->hasMany(Products::class, 'category_id');
    }
}
