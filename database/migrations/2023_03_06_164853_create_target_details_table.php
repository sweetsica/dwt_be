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
        Schema::create('target_details', function (Blueprint $table) {
            $table->id();
            //relationships
            $table->foreignId('target_id')->constrained('targets')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            //infos:
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('executionPlan')->nullable();
            $table->string('status')->default('progress');
            $table->integer('quantity');
            $table->double('manday');
            $table->date('startDate');
            $table->date('deadline');
            $table->text('managerComment')->nullable();
            $table->text('managerManDay')->nullable();
            //more info will be added later

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_details');
    }
};
