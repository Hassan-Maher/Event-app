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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->decimal('price', 10, 2); // السعر الأساسي
            $table->decimal('offer', 10, 2)->nullable(); // قيمة الخصم
            $table->decimal('final_price', 10, 2); // السعر النهائي بعد الخصم

            $table->string('payment_method'); // visa, cash, applepay, etc
            $table->enum('status', [
                'waiting',
                'accepted',
                'rejected',
                'cancelled',
                'semi_accepted',
                'paid'
            ])->default('waiting');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->double('latitude');
            $table->double('longitude');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
