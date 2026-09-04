<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('total_sessions');
            $table->unsignedSmallInteger('total_hours');
            $table->unsignedSmallInteger('minimum_exam_sessions')->nullable();
            $table->unsignedTinyInteger('position')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('learning_sessions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('session_number');
            $table->string('title_hi');
            $table->string('title_en')->nullable();
            $table->string('delivery_mode', 20)->default('blended');
            $table->unsignedSmallInteger('duration_minutes')->default(120);
            $table->string('content_status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['course_id', 'session_number']);
        });

        Schema::create('enrollments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('active')->index();
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'course_id']);
        });

        Schema::create('attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('attendance_date')->index();
            $table->string('mode', 20)->index();
            $table->string('status', 20)->default('present')->index();
            $table->string('biometric_reference')->nullable();
            $table->unsignedSmallInteger('minutes_completed')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'learning_session_id', 'mode']);
        });

        Schema::create('activity_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_session_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('started')->index();
            $table->decimal('score', 6, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'learning_session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_records');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('learning_sessions');
        Schema::dropIfExists('courses');
    }
};
