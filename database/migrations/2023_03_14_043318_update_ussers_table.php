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
        //
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('departement_id')->nullable()->constrained('departements')->onDelete('cascade');
            $table->foreignId('position_id')->nullable()->constrained('positions')->onDelete('cascade');
            $table->foreignId('position_level_id')->nullable()->constrained('position_levels')->onDelete('cascade');
            $table->double('salary_fund')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['position_id']);
            $table->dropForeign(['position_level_id']);
            $table->dropColumn(['departement_id', 'position_id', 'position_level_id', 'salary_fund']);
        });
    }
};
