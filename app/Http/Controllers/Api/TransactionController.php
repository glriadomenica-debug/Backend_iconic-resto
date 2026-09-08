<?php

namespace App\Http\Controllers\Api;

use App\Models\Transactions;
use App\Http\Controllers\Controller;
use App\Helpers\ApiMessage;
use App\Models\Products;
use App\Models\TransactionDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $transactions = Transactions::with([
                'transactionDetails.product'
            ])->orderBy('created_at', 'desc')
                ->paginate(10);
            return ApiMessage::success('Success get transactions', $transactions, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'customer_name' => 'required|string|max:100',
                'table_number' => 'required|numeric',
                'payment_method' => 'required|in:cashier_payment,self_payment',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.qty' => 'required|integer|min:1',
            ]);

            $result = DB::transaction(function () use ($request) {

                // ==========================================
                // 1. CHECK ALL STOCK FIRST
                // ==========================================
                foreach ($request->items as $item) {
                    $product = Products::where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->first();

                    if (!$product) {
                        throw new \Exception(
                            "Product with ID {$item['product_id']} not found."
                        );
                    }

                    if ($product->stock < $item['qty']) {
                        throw new \Exception(
                            "Only {$product->stock} {$product->product_name} available."
                        );
                    }
                }

                // ==========================================
                // 2. DETERMINE QUEUE PERIOD
                // ==========================================
                $now = Carbon::now('Asia/Dili');

                if ($now->hour < 8) {
                    // Before 08:00 → belongs to previous queue day
                    $queueDate = $now->copy()->subDay()->toDateString();
                } else {
                    // 08:00 onwards → belongs to today
                    $queueDate = $now->toDateString();
                }

                // ==========================================
                // 3. GET NEXT QUEUE NUMBER
                // ==========================================
                $lastQueue = Transactions::whereDate('queue_date', $queueDate)
                    ->lockForUpdate()
                    ->max('queue_number');

                $queueNumber = ($lastQueue ?? 0) + 1;

                // ==========================================
                // 4. CREATE TRANSACTION
                // ==========================================
                $transaction = Transactions::create([
                    'customer_name' => $request->customer_name,
                    'table_number' => $request->table_number,
                    'total_price' => 0,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'unpaid',
                    'kitchen_status' => 'pending',
                    // 'status' => 'pending',
                    'customer_token' => $request->customer_token,
                    'queue_number' => $queueNumber,
                    'queue_date' => $queueDate,
                ]);

                $total = 0;

                // ==========================================
                // 5. CREATE DETAILS + REDUCE STOCK
                // ==========================================
                foreach ($request->items as $item) {

                    $product = Products::where('id', $item['product_id'])
                        ->lockForUpdate()
                        ->first();

                    $subtotal = $product->price * $item['qty'];

                    TransactionDetails::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'qty' => $item['qty'],
                        'price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $item['qty']);

                    $total += $subtotal;
                }

                // ==========================================
                // 6. UPDATE TOTAL
                // ==========================================
                $transaction->update([
                    'total_price' => $total,
                ]);

                return $transaction->load('transactionDetails.product');
            });

            return ApiMessage::success(
                'Transaction created successfully',
                $result,
                201
            );
        } catch (\Throwable $th) {

            if (
                str_contains(
                    strtolower($th->getMessage()),
                    'available'
                )
            ) {
                return ApiMessage::error(
                    $th->getMessage(),
                    409
                );
            }

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }


    // public function store(Request $request)
    // {
    //     DB::beginTransaction();

    //     try {

    //         $request->validate([
    //             'customer_name' => 'required|string|max:100',
    //             'table_number' => 'required|numeric',
    //             'payment_method' => 'required|in:cashier_payment,self_payment',
    //             'items' => 'required|array|min:1',
    //             'items.*.product_id' => 'required|exists:products,id',
    //             'items.*.qty' => 'required|integer|min:1',
    //         ]);

    //         $transaction = Transactions::create([
    //             'customer_name' => $request->customer_name,
    //             'table_number' => $request->table_number,
    //             'total_price' => 0,
    //             'payment_method' => $request->payment_method,
    //             'status' => 'pending',
    //             'customer_token' => $request->customer_token,
    //         ]);

    //         $total = 0;

    //         foreach ($request->items as $item) {

    //             $product = Products::findOrFail($item['product_id']);

    //             // check stock
    //             if ($product->stock < $item['qty']) {
    //                 return ApiMessage::error(
    //                     "Stock {$product->name} tidak cukup",
    //                     400
    //                 );
    //             }
    //             //calcula subtotal
    //             $subtotal = $product->price * $item['qty'];

    //             TransactionDetails::create([
    //                 'transaction_id' => $transaction->id,
    //                 'product_id' => $product->id,
    //                 'qty' => $item['qty'],
    //                 'price' => $product->price,
    //                 'subtotal' => $subtotal
    //             ]);
    //             // kurangi stock
    //             $product->stock -= $item['qty'];
    //             $product->save();

    //             $total += $subtotal;
    //         }

    //         $transaction->update([
    //             'total_price' => $total
    //         ]);
    //         DB::commit();

    //         return ApiMessage::success(
    //             'Transaction created successfully',
    //             $transaction->load('transactionDetails.product'),
    //             201
    //         );
    //     } catch (\Throwable $th) {
    //         DB::rollBack();
    //         return ApiMessage::error($th->getMessage(), 500);
    //     }
    // }


    public function show(string $id)
    {
        try {
            $transaction = Transactions::with(['transactionDetails.product'])->find($id);

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
    public function update(Request $request, $id)
    {
        $transaction = Transactions::findOrFail($id);

        $request->validate([
            'kitchen_status' => 'required|in:pending,cooking,ready,served',
        ]);

        $transaction->update([
            'kitchen_status' => $request->kitchen_status,
        ]);

        return ApiMessage::success(
            'Kitchen status updated',
            $transaction,
            200
        );
    }

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

    public function myOrders($token)
    {
        $orders = Transactions::with([
            'transactionDetails.product'
        ])
            ->where('customer_token', $token)
            ->latest()
            ->get();

        return response()->json([
            'message' => 'My Orders',
            'data' => $orders
        ]);
    }


    public function kitchenOrders()
    {
        try {
            $transactions = Transactions::with([
                'transactionDetails.product'
            ])
                ->whereIn('kitchen_status', [
                    'pending',
                    'cooking',
                ])
                ->orderBy('created_at', 'asc')
                ->get();

            return ApiMessage::success(
                'Success get kitchen orders',
                $transactions,
                200
            );
        } catch (\Throwable $th) {
            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    public function report()
    {
        $transactions = Transactions::with([
            'transactionDetails.product'
        ])->get();

        return response()->json([
            'status' => 'success',
            'data' => $transactions
        ]);
    }

    public function analytics()
    {
        $transactions = Transactions::with('transactionDetails.product')
            ->where('payment_status', 'paid')
            ->get();

        return response()->json([
            'data' => $transactions
        ]);
    }
}
