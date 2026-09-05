<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_override_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->nullOnDelete();

            $table->string('action', 80);
            $table->text('reason');
            $table->json('details')->nullable();

            $table->timestamp('performed_at');
            $table->timestamps();

            $table->index(['student_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_override_logs');
    }
};
