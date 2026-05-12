<?php

namespace App\Http\Controllers\Api;

use App\Models\Transactions;
use App\Http\Controllers\Controller;
use App\Helpers\ApiMessage;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $transactions = Transactions::with([
                'user',
                'transactionDetails.product'
            ])->get();
            return ApiMessage::success('Success get transactions', $transactions, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $transaction = Transactions::with(['user', 'transactionDetails.product'])->find($id);

            if (!$transaction) {
                return ApiMessage::error('Error', 'Transaction not found', 404);
            }
            return ApiMessage::success('Success', $transaction, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transactions $transactions)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $transaction = Transactions::find($id);
            if (!$transaction) {
                return ApiMessage::error('Error', 'Transaction not found', 404);
            }
            $transaction->delete();

            return ApiMessage::success('Transaction successfully deleted', null, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }
}
