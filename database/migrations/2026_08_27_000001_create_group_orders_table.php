<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedBigInteger('host_id')->nullable();
            $table->string('host_name');
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('group_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_order_id')->constrained('group_orders')->onDelete('cascade');
            $table->string('member_name');
            $table->foreignId('dish_id')->constrained('dishes')->onDelete('cascade');
            $table->integer('quantity')->default(1);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_order_items');
        Schema::dropIfExists('group_orders');
    }
};
