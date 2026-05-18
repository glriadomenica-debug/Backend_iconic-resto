<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVerification extends Model
{
    protected $table = 'payment_verification';
    protected $fillable = [
        'transaction_id',
        'verified_by',
        'verified_at',
        'payment_method',
    ];

    // public $timestamps = false;

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }
}
