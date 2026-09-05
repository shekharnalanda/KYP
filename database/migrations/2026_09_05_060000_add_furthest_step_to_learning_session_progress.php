<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_session_progress', function (Blueprint $table): void {
            $table->unsignedTinyInteger('furthest_step')->default(0)->after('current_step');
        });
    }

    public function down(): void
    {
        Schema::table('learning_session_progress', function (Blueprint $table): void {
            $table->dropColumn('furthest_step');
        });
    }
};
