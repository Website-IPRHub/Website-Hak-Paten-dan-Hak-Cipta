<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Rapihin data lama -> masuk ke 4 status baru
        // Anggap: Menunggu / Diajukan / null / kosong = Terkirim (baru kirim pengajuan)
        DB::statement("UPDATE paten_verifs SET status_verif='Terkirim' WHERE status_verif IS NULL OR status_verif='' OR status_verif='Menunggu' OR status_verif='Diajukan'");

        // Kalau masih ada nilai lain yang aneh, paksa jadi Terkirim supaya aman
        DB::statement("UPDATE paten_verifs SET status_verif='Terkirim' WHERE status_verif NOT IN ('Terkirim','Proses','Revisi','Approve')");

        // 2) Ubah kolom jadi ENUM
        DB::statement("
            ALTER TABLE paten_verifs
            MODIFY status_verif ENUM('Terkirim','Proses','Revisi','Approve')
            NOT NULL DEFAULT 'Terkirim'
        ");
    }

    public function down(): void
    {
        // Balikin ke string kalau rollback
        DB::statement("
            ALTER TABLE paten_verifs
            MODIFY status_verif VARCHAR(50) NOT NULL DEFAULT 'Terkirim'
        ");
    }
};
