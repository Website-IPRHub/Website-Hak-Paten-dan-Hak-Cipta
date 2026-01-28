<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Pakai raw SQL karena ENUM lebih aman via DB::statement
        DB::statement("
            ALTER TABLE paten
            MODIFY skema_penelitian ENUM(
                'Penelitian Dasar (TKT 1 - 3)',
                'Penelitian Terapan (TKT 4 - 6)',
                'Penelitian Pengembangan (TKT 7 - 9)',
                'Bukan dihasilkan dari Skema Penelitian'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        // balik ke string kalau rollback
        Schema::table('paten', function (Blueprint $table) {
            $table->string('skema_penelitian', 255)->change();
        });
    }
};
