<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_session_progress', function (Blueprint $table): void {
            $table->json('activity_states')->nullable()->after('quiz_answers');
        });
    }

    public function down(): void
    {
        Schema::table('learning_session_progress', function (Blueprint $table): void {
            $table->dropColumn('activity_states');
        });
    }
};
