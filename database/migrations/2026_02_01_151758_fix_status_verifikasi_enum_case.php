<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) normalisasi data lama (Titlecase -> lowercase)
        DB::statement("UPDATE status_verifikasi SET status = LOWER(status) WHERE status IS NOT NULL");

        // 2) default yang waras: terkirim (bukan approve)
        // 3) ubah enum ke lowercase biar nyambung sama controller
        DB::statement("
            ALTER TABLE status_verifikasi
            MODIFY status ENUM('terkirim','proses','revisi','approve')
            NOT NULL DEFAULT 'terkirim'
        ");
    }

    public function down(): void
    {
        // balikin (kalau perlu)
        DB::statement("
            ALTER TABLE status_verifikasi
            MODIFY status ENUM('Terkirim','Proses','Revisi','Approve')
            NOT NULL DEFAULT 'Terkirim'
        ");
    }
};
