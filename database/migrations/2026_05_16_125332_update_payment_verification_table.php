<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_verification', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->unsignedBigInteger('verified_by')
                ->nullable()
                ->change();
            $table->renameColumn('note', 'payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_verification', function (Blueprint $table) {
            $table->renameColumn('payment_method', 'note');
            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
