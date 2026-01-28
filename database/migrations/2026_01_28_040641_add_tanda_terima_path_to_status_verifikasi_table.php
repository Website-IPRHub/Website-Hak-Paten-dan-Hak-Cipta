<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('status_verifikasi', function (Blueprint $table) {
            if (!Schema::hasColumn('status_verifikasi', 'tanda_terima_pdf')) {
                $table->string('tanda_terima_pdf')->nullable()->after('sertifikat_path');
            }
        });
    }
    public function down(): void
    {
        Schema::table('status_verifikasi', function (Blueprint $table) {
            if (Schema::hasColumn('status_verifikasi', 'tanda_terima_path')) {
                $table->dropColumn('tanda_terima_path');
            }
        });
    }

};
