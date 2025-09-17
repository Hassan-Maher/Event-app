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
        Schema::table('products', function (Blueprint $table) {
            $table->string('first_option')->nullable();
            $table->decimal('first_price', 8, 2)->nullable();

            $table->string('second_option')->nullable();
            $table->decimal('second_price', 8, 2)->nullable();

            $table->string('third_option')->nullable();
            $table->decimal('third_price', 8, 2)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
