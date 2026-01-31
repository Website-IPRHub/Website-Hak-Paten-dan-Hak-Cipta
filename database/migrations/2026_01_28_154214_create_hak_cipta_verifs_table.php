<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hak_cipta_verifs', function (Blueprint $table) {
            $table->id();

            $table->string('no_pendaftaran')->nullable();
            $table->string('jenis_cipta')->nullable();
            $table->string('judul_cipta')->nullable();

            $table->string('nama_pencipta')->nullable();
            $table->string('nip_nim')->nullable();
            $table->string('fakultas')->nullable();

            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();

            $table->string('nilai_perolehan')->nullable();
            $table->string('skema_penelitian')->nullable();
            $table->string('sumber_dana')->nullable();

            $table->string('surat_permohonan')->nullable();
            $table->string('surat_pernyataan')->nullable();
            $table->string('surat_pengalihan')->nullable();
            $table->string('tanda_terima')->nullable();

            $table->string('scan_ktp')->nullable();
            $table->string('hasil_ciptaan')->nullable();
            $table->string('link_ciptaan')->nullable();

            $table->string('status')->nullable();

            $table->json('inventors')->nullable(); // inventors json
            $table->string('skema_tkt_template_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hak_cipta_verifs');
    }
};
