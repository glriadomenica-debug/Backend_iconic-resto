<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedInteger('queue_number')
                ->nullable()
                ->after('id');

            $table->date('queue_date')
                ->nullable()
                ->after('queue_number');

            $table->index(
                ['queue_number', 'queue_date'],
                'transactions_queue_number_queue_date_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_queue_number_queue_date_index');

            $table->dropColumn([
                'queue_number',
                'queue_date',
            ]);
        });
    }
};
