<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paten', function (Blueprint $table) {
            $table->id();

            // token random buat ngelink antar-step (optional tapi recommended)
            $table->string('token', 64)->unique()->nullable();

            $table->string('no_pendaftaran')->unique(); // wajib & unik

            $table->enum('jenis_paten', ['Paten', 'Paten Sederhana']);
            $table->string('judul_paten');

            $table->text('nama_pencipta');
            $table->text('nip_nim');

            $table->enum('fakultas', [
                'Sekolah Vokasi',
                'Fakultas Teknik',
                'Fakultas Sains dan Matematika',
                'Fakultas Kesehatan Masyarakat',
                'Fakultas Kedokteran',
                'Fakultas Perikanan dan Ilmu Kelautan',
                'Fakultas Pertanian dan Peternakan',
                'Fakultas Ekonomika dan Bisnis',
                'Fakultas Hukum',
                'Fakultas Ilmu Sosial dan Ilmu Politik',
                'Fakultas Ilmu Budaya',
                'Fakultas Psikologi',
                'Sekolah Pasca Sarjana',
            ]);

            $table->string('no_hp');
            $table->string('email');

            $table->enum('prototipe', ['Sudah', 'Belum'])->default('Belum');

            $table->string('nilai_perolehan');

            $table->enum('sumber_dana', [
                'Universitas Diponegoro',
                'APBN/APBD/Swasta',
                'Mandiri',
            ])->default('Universitas Diponegoro');

            $table->enum('skema_penelitian', [
                'Penelitian Dasar (TKT 1 - 3)',
                'Penelitian Terapan (TKT 4 - 6)',
                'Penelitian Pengembangan (TKT 7 - 9)',
                'Bukan dihasilkan dari Skema Penelitian',
            ]);

            // File upload
            $table->string('draft_paten');
            $table->string('form_permohonan');
            $table->string('surat_kepemilikan');
            $table->string('surat_pengalihan');
            $table->string('scan_ktp');
            $table->string('tanda_terima');

            $table->string('gambar_prototipe')->nullable();
            $table->string('deskripsi_singkat_prototipe')->nullable();

            $table->enum('status', ['terkirim', 'proses', 'revisi', 'diterima', 'ditolak'])
                  ->default('terkirim');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paten');
    }
};
