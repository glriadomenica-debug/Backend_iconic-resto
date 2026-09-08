<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PaymentVerification;
use App\Models\Transactions;
use Illuminate\Http\Request;

class PaymentVerificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $transactionId)
    {
        $transaction = Transactions::findOrFail($transactionId);

        // Jangan buat verification duplicate
        if ($transaction->payment_status === 'paid') {
            return response()->json([
                'message' => 'Payment already verified'
            ], 409);
        }

        $paymentVerification = PaymentVerification::create([
            'transaction_id' => $transaction->id,
            'verified_by' => $request->verified_by,
            'verified_at' => now(),
            'payment_method' => $request->payment_method,
        ]);

        // HANYA ubah PAYMENT STATUS
        $transaction->update([
            'payment_status' => 'paid',
        ]);

        return response()->json([
            'message' => 'Payment verified successfully',
            'data' => $paymentVerification
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($transactionId)
    {
        $paymentVerification = PaymentVerification::where('transaction_id', $transactionId)->first();

        if (!$paymentVerification) {
            return response()->json([
                'message' => 'Payment verification not found'
            ], 404);
        }

        return response()->json($paymentVerification);
    }
}
