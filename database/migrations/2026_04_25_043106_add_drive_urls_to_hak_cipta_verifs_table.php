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
        Schema::table('hak_cipta_verifs', function (Blueprint $table) {
            $table->text('surat_permohonan_drive_url')->nullable();
            $table->text('surat_pernyataan_drive_url')->nullable();
            $table->text('surat_pengalihan_drive_url')->nullable();
            $table->text('tanda_terima_drive_url')->nullable();
            $table->text('scan_ktp_drive_url')->nullable();
            $table->text('hasil_ciptaan_drive_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hak_cipta_verifs', function (Blueprint $table) {
            $table->dropColumn([
                'surat_permohonan_drive_url',
                'surat_pernyataan_drive_url',
                'surat_pengalihan_drive_url',
                'tanda_terima_drive_url',
                'scan_ktp_drive_url',
                'hasil_ciptaan_drive_url',
            ]);
        });
    }
};
