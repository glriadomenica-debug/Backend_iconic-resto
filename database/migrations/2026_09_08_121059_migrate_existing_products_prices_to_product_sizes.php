<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $products = DB::table('products')->get();

        foreach ($products as $product) {
            DB::table('product_sizes')->insert([
                [
                    'product_id' => $product->id,
                    'size' => 'Small',
                    'price' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'product_id' => $product->id,
                    'size' => 'Medium',
                    'price' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'product_id' => $product->id,
                    'size' => 'Large',
                    'price' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        DB::table('product_sizes')
            ->whereIn('size', ['Small', 'Medium', 'Large'])
            ->delete();
    }
};
