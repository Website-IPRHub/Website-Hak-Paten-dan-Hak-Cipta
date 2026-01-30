<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) status enum: SAMAIN sama yang dipakai controller
        DB::statement("
            ALTER TABLE hak_cipta
            MODIFY COLUMN status ENUM('Draft','Proses','Revisi','Approve','Terkirim')
            NOT NULL
            DEFAULT 'Draft'
        ");

        // 2) kolom dokumen: boleh null (karena upload per-step)
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN surat_permohonan VARCHAR(255) NULL");
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN surat_pernyataan VARCHAR(255) NULL");
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN surat_pengalihan VARCHAR(255) NULL");
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN tanda_terima VARCHAR(255) NULL");
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN scan_ktp VARCHAR(255) NULL");
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN hasil_ciptaan VARCHAR(255) NULL");
        DB::statement("ALTER TABLE hak_cipta MODIFY COLUMN link_ciptaan VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // balikannya opsional (boleh kamu sesuaikan ke enum lama kamu)
        // minimal: jangan bikin down ngawur kalau kamu ga tau enum sebelumnya.
    }
};
