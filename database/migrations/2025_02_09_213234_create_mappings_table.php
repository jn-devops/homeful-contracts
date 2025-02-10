<?php

use App\Enums\{MappingCategory, MappingSource, MappingType};
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
        Schema::create('mappings', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('path');
            $table->string('source')->default(MappingSource::default()->value);
            $table->string('title')->nullable();
            $table->string('type')->default(MappingType::default()->value);
            $table->string('default')->nullable();
            $table->string('category')->default(MappingCategory::default()->value);
            $table->string('transformer')->nullable();
            $table->json('options')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamp('disabled_at')->nullable();
            $table->timestamp('deprecated_at')->nullable();
            //TODO: add disabled_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mappings');
    }
};
