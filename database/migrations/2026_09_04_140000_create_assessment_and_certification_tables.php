<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title_hi');
            $table->string('title_en');
            $table->unsignedSmallInteger('duration_minutes')->default(60);
            $table->unsignedSmallInteger('total_questions')->default(0);
            $table->decimal('max_marks', 8, 2)->default(200);
            $table->string('status')->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_session_id')->nullable()->constrained()->nullOnDelete();
            $table->text('text_hi');
            $table->text('text_en');
            $table->string('type')->default('single_choice');
            $table->decimal('marks', 6, 2)->default(1);
            $table->decimal('negative_marks', 6, 2)->default(0);
            $table->text('explanation_hi')->nullable();
            $table->text('explanation_en')->nullable();
            $table->string('difficulty')->default('medium');
            $table->string('status')->default('draft');
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('option_key', 10);
            $table->text('text_hi');
            $table->text('text_en');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
            $table->unique(['question_id', 'option_key']);
        });

        Schema::create('exam_question', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->unique(['exam_id', 'question_id']);
        });

        Schema::create('exam_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->string('status')->default('in_progress');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->decimal('raw_exam_score', 8, 2)->default(0);
            $table->decimal('max_raw_score', 8, 2)->default(200);
            $table->timestamps();
            $table->unique(['exam_id', 'user_id', 'attempt_number']);
        });

        Schema::create('attempt_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 8, 2)->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
            $table->unique(['exam_attempt_id', 'question_id']);
        });

        Schema::create('results', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_attempt_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->decimal('exam_raw', 8, 2)->default(0);
            $table->decimal('lab_raw', 8, 2)->default(0);
            $table->decimal('classroom_raw', 8, 2)->default(0);
            $table->decimal('total_raw', 8, 2)->default(0);
            $table->decimal('exam_final', 6, 2)->default(0);
            $table->decimal('lab_final', 6, 2)->default(0);
            $table->decimal('classroom_final', 6, 2)->default(0);
            $table->decimal('final_score', 6, 2)->default(0);
            $table->string('result_status')->default('pending');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('certificates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('result_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('serial_number')->unique();
            $table->uuid('qr_token')->unique();
            $table->timestamp('issued_at')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('results');
        Schema::dropIfExists('attempt_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_question');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('exams');
    }
};
