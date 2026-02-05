<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('verifikasi_dokumen', function (Blueprint $table) {
            if (!Schema::hasColumn('verifikasi_dokumen', 'admin_attachment_name')) {
                $table->string('admin_attachment_name')->nullable()->after('admin_attachment_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifikasi_dokumen', function (Blueprint $table) {
            if (Schema::hasColumn('verifikasi_dokumen', 'admin_attachment_name')) {
                $table->dropColumn('admin_attachment_name');
            }
         });
    }
};
