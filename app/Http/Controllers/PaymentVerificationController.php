<?php

namespace App\Http\Controllers;

use App\Models\PaymentVerification;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentVerificationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $transactionId)
    {
        $transaction = Transactions::findOrFail($transactionId);

        // simpan verifikasi pembayaran
        $paymentVerification = PaymentVerification::create([
            'transaction_id' => $transaction->id,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'payment_method' => $request->payment_method,
        ]);

        // update status transaction
        $transaction->update([
            'status' => 'paid'
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
        $paymentVerification = PaymentVerification::with([
            'transaction',
            'verifier'
        ])->where('transaction_id', $transactionId)->first();

        if (!$paymentVerification) {
            return response()->json([
                'message' => 'Payment verification not found'
            ], 404);
        }

        return response()->json($paymentVerification);
    }
}
