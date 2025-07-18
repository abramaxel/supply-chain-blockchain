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
        Schema::create('blockchain_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('block_hash')->unique();
            $table->string('previous_hash')->nullable();
            $table->string('entity_type'); // e.g. 'batch', 'purchase_order'
            $table->string('entity_id');   // e.g. batch_no, po_id, etc
            $table->string('action');      // CREATE, UPDATE, TRANSFER, DELETE
            $table->longText('data');        // Snapshot perubahan
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key ke users, nullable jika system
            $table->timestamp('created_at')->useCurrent();

            // Relasi ke tabel users (optional, jika ada table users)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blockchain_logs');
    }
};
