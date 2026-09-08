<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'customer_name',
        'table_number',
        'total_price',
        'payment_method',
        'payment_status',
        'kitchen_status',
        'status',
        'customer_token',
        'queue_number',
        'queue_date',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetails::class, 'transaction_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }
    public function paymentVerification()
    {
        return $this->hasOne(PaymentVerification::class, 'transaction_id');
    }
}
