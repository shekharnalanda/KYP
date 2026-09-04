<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table): void {
            $table->text('objectives_hi')->nullable()->after('title_en');
            $table->longText('lesson_content_hi')->nullable()->after('objectives_hi');
            $table->text('classroom_notes_hi')->nullable()->after('lesson_content_hi');
            $table->text('lab_activity_hi')->nullable()->after('classroom_notes_hi');
            $table->text('assessment_prompt_hi')->nullable()->after('lab_activity_hi');
        });
    }

    public function down(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table): void {
            $table->dropColumn(['objectives_hi', 'lesson_content_hi', 'classroom_notes_hi', 'lab_activity_hi', 'assessment_prompt_hi']);
        });
    }
};
