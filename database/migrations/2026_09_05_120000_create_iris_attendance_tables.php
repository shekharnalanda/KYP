<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iris_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->longText('left_template')->nullable();
            $table->longText('right_template')->nullable();
            $table->string('device_model', 50)->default('Mantra MIS100V2');
            $table->string('device_reference')->nullable();
            $table->unsignedSmallInteger('quality_score')->nullable();
            $table->timestamp('enrolled_at')->nullable();
            $table->foreignId('enrolled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('iris_attendance_events', function (Blueprint $table): void {
            $table->id();
            $table->uuid('event_uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 20)->index();
            $table->string('device_reference')->nullable()->index();
            $table->string('matched_eye', 10)->nullable();
            $table->decimal('match_score', 8, 3)->nullable();
            $table->timestamp('captured_at')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'learning_session_id', 'captured_at'], 'iris_user_session_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iris_attendance_events');
        Schema::dropIfExists('iris_profiles');
    }
};
