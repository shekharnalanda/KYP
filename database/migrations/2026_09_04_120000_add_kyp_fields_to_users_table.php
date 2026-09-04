<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 20)->nullable()->unique()->after('email');
            $table->string('student_id', 40)->nullable()->unique()->after('phone');
            $table->string('role', 30)->default('student')->index()->after('student_id');
            $table->string('status', 20)->default('active')->index()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'student_id', 'role', 'status']);
        });
    }
};
