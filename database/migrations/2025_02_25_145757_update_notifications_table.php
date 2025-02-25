<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notifiable_id', 'notifiable_type']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->uuidMorphs('notifiable');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notifiable_id', 'notifiable_type']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->morphs('notifiable'); // Default (bigInteger-based) morphs
        });
    }
};
