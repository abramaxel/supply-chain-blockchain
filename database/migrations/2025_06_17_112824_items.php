<?php
// database/migrations/2025_06_17_000001_create_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->enum('type', ['raw_material', 'finished_good'])->default('raw_material');
            $table->string('unit', 32);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
