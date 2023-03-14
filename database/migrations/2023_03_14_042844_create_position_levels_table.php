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
        Schema::create('position_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('descrition')->nullable();
            $table->double('minimum_wage')->nullable(); // luong toi thieu
            $table->double('maximum_wage')->nullable(); // luong toi da

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_levels');
    }
};
