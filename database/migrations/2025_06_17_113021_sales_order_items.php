<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_order_id'); // FK ke sales_orders
            $table->uuid('item_id'); // FK ke items
            $table->string('batch_no');
            $table->integer('quantity');
            $table->decimal('unit_selling_price', 15, 2);
            $table->decimal('total_price',15,2);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('sales_order_id')->references('id')->on('sales_orders')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            // Jika batch_no mau relasi ke tabel batches, bisa tambahkan FK juga, misal:
            $table->foreign('batch_no')->references('batch_no')->on('batches')->onDelete('restrict');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};
