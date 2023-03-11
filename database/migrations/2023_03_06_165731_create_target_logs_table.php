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
        Schema::create('target_logs', function (Blueprint $table) {
            $table->id();
            //relationships
            $table->foreignId('target_detail_id')->constrained('target_details')->onDelete('cascade');
            //informations
            $table->text('note')->nullable();
            $table->integer('quantity');
            $table->string('status')->default('progress');
            $table->text('files')->nullable();; // comma separated file names
            $table->string('noticedStatus')->nullable();;
            $table->date('noticedDate')->nullable();
            $table->date('reportedDate');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_logs');
    }
};
