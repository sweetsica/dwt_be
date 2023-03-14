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
        Schema::create('kpi_keys', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            //relationships
            $table->foreignId('unit_id')->constrained('units')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key if exists
        Schema::table('kpi_keys', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
        });
        Schema::dropIfExists('kpi_keys');
    }
};
