<?php

namespace App\Http\Controllers\Api;

use App\Models\Products;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiMessage;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // $products = Products::with('category')->get();
            $products = Products::with('category')->paginate(6);

            return ApiMessage::success('Success get products', $products, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(),   500);
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
                'product_name' => 'required|string',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'image' => 'nullable|string'
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ApiMessage::error('Validation Error', $validator->errors(), 400);
            }
            $product = Products::create($request->all());
            return ApiMessage::success('Product successfully created', $product, 201);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Products::with('category')->find($id);

            if (!$product) {
                return ApiMessage::error('Error', 'Product not found', 404);
            }
            return ApiMessage::success('Success', $product, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $product = Products::find($id);
            if (!$product) {
                return ApiMessage::error('Error', 'Product not found', 404);
            }
            $product->update($request->all());
            return ApiMessage::success('Product successfully updated', $product, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
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
                return ApiMessage::error('Error', 'Product not found', 404);
            }

            if ($product->transactionDetails()->count() > 0) {

                return ApiMessage::error('Error', 'Product already used in transaction', 400);
            }
            $product->delete();

            return ApiMessage::success('Product successfully deleted', null, 200);
        } catch (\Throwable $th) {

            return ApiMessage::error($th->getMessage(), 500);
        }
    }
}
