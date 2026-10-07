<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table): void {
            $table->unsignedTinyInteger('theory_video_day')->nullable()->after('assessment_prompt_hi');
            $table->string('theory_youtube_video_id', 11)->nullable()->after('theory_video_day');
        });
    }

    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table): void {
            $table->dropColumn(['theory_video_day', 'theory_youtube_video_id']);
        });
    }
};
