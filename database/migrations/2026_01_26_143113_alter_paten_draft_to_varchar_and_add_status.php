<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        // 1) draft_paten jadi varchar(255)
        DB::statement("ALTER TABLE paten MODIFY draft_paten VARCHAR(255) NULL");

        // 2) tambah kolom status enum
        DB::statement("
            ALTER TABLE paten
            ADD status ENUM('terkirim','proses','revisi','diterima','ditolak')
            NOT NULL DEFAULT 'terkirim'
            AFTER deskripsi_singkat_prototipe
        ");
    }

    public function down(): void
    {
        // rollback: hapus kolom status
        Schema::table('paten', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // rollback: draft_paten balik ke longText (kayak sebelumnya)
        DB::statement("ALTER TABLE paten MODIFY draft_paten LONGTEXT NULL");
    }
};
