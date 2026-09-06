<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->string('source', 40)->nullable()->after('mode');
            $table->timestamp('checked_in_at')->nullable()->after('attendance_date');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            $table->string('checkout_source', 40)->nullable()->after('checked_out_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table): void {
            $table->dropColumn([
                'source',
                'checked_in_at',
                'checked_out_at',
                'checkout_source',
            ]);
        });
    }
};
