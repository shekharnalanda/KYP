<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('courses')->where('code', 'AI-DM')->update(['minimum_exam_sessions' => 12]);
    }

    public function down(): void
    {
        DB::table('courses')->where('code', 'AI-DM')->update(['minimum_exam_sessions' => null]);
    }
};
