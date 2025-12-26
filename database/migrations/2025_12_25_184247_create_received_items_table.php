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
        Schema::create('received_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receiving_id')->constrained()->cascadeOnDelete();
            $table->foreignId('delivery_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('received_quantity', 10, 2);
            $table->text('item_note')->nullable();
            $table->date('recorded_date');
            $table->timestamps();
            
            $table->index('receiving_id');
            $table->index('delivery_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('received_items');
    }
};
