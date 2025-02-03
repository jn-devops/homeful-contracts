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
        Schema::create('requirement_matrices', function (Blueprint $table) {
            $table->id();
            $table->json('requirements');
            $table->string('civil_status')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('market_segment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_matrices');
    }
};
