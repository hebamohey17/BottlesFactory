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
        Schema::create('compositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('water_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bottle_size_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ingredient_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount_ml', 12, 3);
            $table->timestamps();

            $table->unique(['water_type_id', 'bottle_size_id', 'ingredient_id'], 'compositions_water_size_ingredient_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compositions');
    }
};
