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
        Schema::create('target_log_kpi_key', function (Blueprint $table) {
            $table->id();
            $table->integer('quantity');
            $table->foreignId('kpi_key_id')->constrained('kpi_keys')->onDelete('cascade');
            $table->foreignId('target_log_id')->constrained('target_logs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop foreign key
        Schema::table('target_log_kpi_key', function (Blueprint $table) {
            $table->dropForeign(['kpi_key_id']);
            $table->dropForeign(['target_log_id']);
        });
        Schema::dropIfExists('target_log_kpi_key');
    }
};
