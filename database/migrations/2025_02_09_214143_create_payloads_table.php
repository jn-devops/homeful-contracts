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
        Schema::create('payloads', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('contract_id');
            $table->string('mapping_code');
            $table->string('value');
            $table->string('format')->nullable();
            $table->string('remarks')->nullable();
            $table->foreign('mapping_code')
                ->references('code')
                ->on('mappings')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payloads');
    }
};
