<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE paten_verifs 
          MODIFY status_verif ENUM('Draft','Menunggu','Diajukan','Disetujui','Ditolak')
          NOT NULL DEFAULT 'Draft'");
    }

    public function down(): void
    {
        DB::statement("UPDATE paten_verifs SET status_verif='Draft' WHERE status_verif='Menunggu'");

        DB::statement("ALTER TABLE paten_verifs 
          MODIFY status_verif ENUM('Draft','Diajukan','Disetujui','Ditolak')
          NOT NULL DEFAULT 'Draft'");
    }
};
