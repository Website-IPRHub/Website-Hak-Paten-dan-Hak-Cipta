<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('status_verifikasi', function (Blueprint $table) {
            $table->timestamp('terkirim_at')->nullable()->after('status');
            $table->timestamp('proses_at')->nullable()->after('terkirim_at');
            $table->timestamp('revisi_at')->nullable()->after('proses_at');
            $table->timestamp('approve_at')->nullable()->after('revisi_at');
        });
    }

    public function down(): void
    {
        Schema::table('status_verifikasi', function (Blueprint $table) {
            $table->dropColumn([
                'terkirim_at',
                'proses_at',
                'revisi_at',
                'approve_at',
            ]);
        });
    }
};