<?php

namespace App\Http\Controllers\Api;

use App\Models\TransactionDetails;
use App\Http\Controllers\Controller;
use App\Helpers\ApiMessage;

class TransactionDetailsController extends Controller
{
    public function index()
    {
        try {

            $details = TransactionDetails::with([
                'transaction',
                'product'
            ])->get();

            return ApiMessage::success(
                'Success get transaction details',
                $details,
                200
            );
        } catch (\Throwable $th) {

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    public function show(string $id)
    {
        try {
            $detail = TransactionDetails::with(['transaction', 'product'])->find($id);
            if (!$detail) {
                return ApiMessage::error('Error', 'Transaction detail not found', 404);
            }
            return ApiMessage::success('Success', $detail, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }
}
