<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('learning_sessions', function (Blueprint $table): void {
            $table->json('courseware')->nullable();
            $table->unsignedSmallInteger('required_active_minutes')->default(120);
            $table->unsignedTinyInteger('passing_score')->default(60);
        });

        Schema::create('learning_session_progress', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('current_step')->default(0);
            $table->json('completed_steps')->nullable();
            $table->unsignedInteger('active_seconds')->default(0);
            $table->json('quiz_answers')->nullable();
            $table->decimal('quiz_score', 5, 2)->nullable();
            $table->text('practical_response')->nullable();
            $table->timestamp('practical_submitted_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'learning_session_id']);
            $table->index(['learning_session_id', 'completed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_session_progress');

        Schema::table('learning_sessions', function (Blueprint $table): void {
            $table->dropColumn(['courseware', 'required_active_minutes', 'passing_score']);
        });
    }
};
