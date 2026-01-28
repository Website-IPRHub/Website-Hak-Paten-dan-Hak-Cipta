<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paten', function (Blueprint $table) {
            $table->id();

            // Nomor pendaftaran (unik)
            $table->string('no_pendaftaran', 20)->unique();

            // Data utama
            $table->enum('jenis_paten', ['Paten', 'Paten Sederhana']);
            $table->string('judul_paten', 255);

            // Inventor JSON (kalau mau simpan semua inventor)
            $table->json('inventors')->nullable();

            // Ringkasan inventor pertama (opsional buat list)
            $table->string('nama_pencipta', 255)->nullable();
            $table->string('nip_nim', 255)->nullable();
            $table->string('fakultas', 255)->nullable();
            $table->string('no_hp', 255)->nullable();
            $table->string('email', 255)->nullable();

            // Data tambahan
            $table->enum('prototipe', ['Sudah', 'Belum']);
            $table->string('nilai_perolehan', 255);
            $table->string('sumber_dana', 255);
            $table->string('skema_penelitian', 255);

            // Draft paten (isi teks)
            $table->longText('draft_paten')->nullable();

            // Dokumen (path file)
            $table->string('form_permohonan')->nullable();
            $table->string('surat_kepemilikan')->nullable();
            $table->string('surat_pengalihan')->nullable();
            $table->string('scan_ktp')->nullable();
            $table->string('tanda_terima')->nullable();

            // Prototipe
            $table->string('gambar_prototipe')->nullable();
            $table->text('deskripsi_singkat_prototipe')->nullable();

<<<<<<< Updated upstream:database/migrations/2026_01_26_133601_create_paten_table.php
=======
            // Verifikasi admin
            $table->enum('status_verif', ['Terkirim', 'Proses', 'Revisi', 'Approve'])
                ->default('Terkirim');
            $table->text('catatan_verif')->nullable();

>>>>>>> Stashed changes:database/migrations/2026_01_13_010931_create_paten_table.php
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paten');
    }
};
