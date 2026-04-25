<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_name', 100);
            $table->string('item_code', 50)->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('unit_of_measure', 20)->default('pcs');
            $table->decimal('quantity_in_stock', 10, 2)->default(0);
            $table->integer('reorder_level')->default(10);
            $table->integer('critical_level')->default(5);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->text('supplier_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_item_ingredients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_item_id');
            $table->unsignedBigInteger('inventory_id');
            $table->decimal('quantity_needed', 10, 3);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['menu_item_id', 'inventory_id'], 'unique_recipe');
        });
    }
    public function down(): void {
        Schema::dropIfExists('menu_item_ingredients');
        Schema::dropIfExists('inventory');
    }
};