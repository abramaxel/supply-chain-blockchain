<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('item_id'); // FK ke items
            $table->string('batch_no')->unique(); // Batch number harus unik!
            $table->string('batch_hash', 64)->unique(); // Hash untuk validasi batch
            $table->integer('quantity')->default(0);
            $table->enum('location', [
                'Gudang A', 'Gudang B', 'Gudang C', 'Gudang D', 'Gudang E'
            ]);
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            // Foreign key constraint ke items
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            // FK ke items: item tidak bisa dihapus jika batch masih ada
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
