<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiMessage;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $categories = Categories::with('product')->get();

            return ApiMessage::success('Success get categories data', $categories, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $rules = [
                'category_name' => 'required|string|max:100'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ApiMessage::error('Validation Error', $validator->errors(), 400);
            }

            $category = Categories::create([
                'category_name' => $request->category_name
            ]);

            return ApiMessage::success('Category successfully created', $category, 201);
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

            $category = Categories::with('product')->find($id);

            if (!$category) {
                return ApiMessage::error('Error', 'Category not found', 404);
            }

            return ApiMessage::success('Success', $category, 200);
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
            $category = Categories::find($id);
            if (!$category) {
                return ApiMessage::error('Error', 'Category not found', 404);
            }

            if ($request->has('category_name')) {
                $category->category_name = $request->category_name;
            }

            $category->save();

            return ApiMessage::success('Category successfully updated', $category, 200);
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

            $category = Categories::find($id);

            if (!$category) {
                return ApiMessage::error('Error', 'Category not found', 404);
            }

            if ($category->product()->count() > 0) {

                return ApiMessage::error('Error', 'Category still has products', 400);
            }

            $category->delete();

            return ApiMessage::success('Category successfully deleted', null, 200);
        } catch (\Throwable $th) {
            return ApiMessage::error($th->getMessage(), 500);
        }
    }
}
