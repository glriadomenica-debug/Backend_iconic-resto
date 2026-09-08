<?php

namespace App\Http\Controllers\Api;

use App\Models\Products;
use App\Models\ProductSize;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiMessage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $products = Products::with([
                'category',
                'sizes',
            ])->paginate(9);

            return ApiMessage::success(
                'Success get products',
                $products,
                200
            );
        } catch (\Throwable $th) {
            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $rules = [
                'category_id' => 'required|exists:categories,id',
                'product_name' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|string',
                'sizes' => 'required|array|min:1',
                'sizes.*.size' => [
                    'required',
                    'string',
                    'in:Small,Medium,Large',
                ],

                'sizes.*.price' => [
                    'required',
                    'numeric',
                    'min:0.01',
                ],
            ];

            $validator = Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                return ApiMessage::error(
                    'Validation Error',
                    $validator->errors(),
                    400
                );
            }

            DB::beginTransaction();

            $product = Products::create([
                'category_id' => $request->category_id,
                'product_name' => $request->product_name,
                'price' => $request->sizes[0]['price'],
                'stock' => $request->stock,
                'image' => $request->image,
            ]);

            foreach ($request->sizes as $size) {

                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $size['size'],
                    'price' => $size['price'],
                ]);
            }

            DB::commit();

            $product->load([
                'category',
                'sizes',
            ]);

            return ApiMessage::success(
                'Product successfully created',
                $product,
                201
            );
        } catch (\Throwable $th) {

            DB::rollBack();

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $product = Products::with([
                'category',
                'sizes',
            ])->find($id);

            if (!$product) {

                return ApiMessage::error(
                    'Error',
                    'Product not found',
                    404
                );
            }

            return ApiMessage::success(
                'Success',
                $product,
                200
            );
        } catch (\Throwable $th) {

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        string $id
    ) {
        try {
            $product = Products::find($id);
            if (!$product) {

                return ApiMessage::error(
                    'Error',
                    'Product not found',
                    404
                );
            }
            $rules = [
                'category_id' => 'required|exists:categories,id',
                'product_name' => 'required|string|max:255',
                'stock' => 'required|integer|min:0',
                'image' => 'nullable|string',
                'sizes' => 'required|array|min:1',
                'sizes.*.size' => [
                    'required',
                    'string',
                    'in:Small,Medium,Large',
                ],

                'sizes.*.price' => [
                    'required',
                    'numeric',
                    'min:0.01',
                ],
            ];

            $validator = Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {

                return ApiMessage::error(
                    'Validation Error',
                    $validator->errors(),
                    400
                );
            }

            DB::beginTransaction();

            $product->update([
                'category_id' => $request->category_id,
                'product_name' => $request->product_name,
                'price' => $request->sizes[0]['price'],
                'stock' => $request->stock,
                'image' => $request->image,
            ]);

            $product->sizes()->delete();

            foreach ($request->sizes as $size) {

                ProductSize::create([
                    'product_id' => $product->id,
                    'size' => $size['size'],
                    'price' => $size['price'],
                ]);
            }

            DB::commit();

            $product->load([
                'category',
                'sizes',
            ]);

            return ApiMessage::success(
                'Product successfully updated',
                $product,
                200
            );
        } catch (\Throwable $th) {

            DB::rollBack();

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {

            $product = Products::find($id);

            if (!$product) {

                return ApiMessage::error(
                    'Error',
                    'Product not found',
                    404
                );
            }

            if (
                $product
                ->transactionDetails()
                ->count() > 0
            ) {

                return ApiMessage::error(
                    'Error',
                    'Product already used in transaction',
                    400
                );
            }
            $product->delete();

            return ApiMessage::success(
                'Product successfully deleted',
                null,
                200
            );
        } catch (\Throwable $th) {

            return ApiMessage::error(
                $th->getMessage(),
                500
            );
        }
    }
}
