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
        Schema::create('contacts_update_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('contacts_id');
            $table->string('field', 550)->nullable()->change();
            $table->longText('field')->nullable()->change();
            $table->longText('from')->nullable()->change();
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts_update_logs');
    }
};
