<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->string('item_type');
            $table->foreignId('sales_product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sales_service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->decimal('quantity', 12, 2)->default(1);
            $table->string('unit', 50)->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->decimal('line_total', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};
