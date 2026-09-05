<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 30)->unique();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->default('Bihar');
            $table->string('pin', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_number', 40)->unique();
            $table->foreignId('branch_id')->nullable()
                ->constrained('branches')->nullOnDelete();
            $table->foreignId('course_id')->nullable()
                ->constrained('courses')->nullOnDelete();

            $table->string('name', 150);
            $table->string('mobile', 20);
            $table->string('email')->nullable();
            $table->string('qualification', 120)->nullable();
            $table->text('message')->nullable();

            $table->string('status', 30)->default('new')->index();
            $table->text('admin_note')->nullable();
            $table->foreignId('handled_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('application_number', 40)->unique();

            $table->foreignId('branch_id')
                ->constrained('branches')->restrictOnDelete();
            $table->foreignId('course_id')
                ->constrained('courses')->restrictOnDelete();

            $table->string('name', 150);
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 20)->nullable();

            $table->string('mobile', 20);
            $table->string('email')->nullable();

            $table->string('father_name', 150)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->string('guardian_name', 150)->nullable();
            $table->string('guardian_mobile', 20)->nullable();

            $table->string('qualification', 120)->nullable();

            $table->text('address');
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->default('Bihar');
            $table->string('pin', 10)->nullable();

            $table->string('identity_type', 40)->nullable();
            $table->string('identity_number', 100)->nullable();

            $table->string('photo_path');
            $table->text('remarks')->nullable();

            $table->boolean('consent')->default(false);

            $table->string('status', 30)->default('submitted')->index();
            $table->text('admin_note')->nullable();

            $table->foreignId('approved_by')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['branch_id','status']);
            $table->index(['course_id','status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('branches');
    }
};
