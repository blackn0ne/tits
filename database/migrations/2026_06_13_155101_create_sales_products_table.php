<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('short_description')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('quantity')->default(0);
            $table->string('unit', 50)->default('шт');
            $table->string('status')->default('active');
            $table->string('kaspi_link')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_products');
    }
};
