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
        Schema::create('hak_cipta', function (Blueprint $table) {
            $table->id();

            $table->string('no_pendaftaran')->unique();
            $table->enum('jenis_cipta', ['Buku','Modul','Program Komputer','Karya Rekaman Video','Lainnya']);
            $table->string('judul_cipta');
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
                'Pasca Sarjana'
            ]);
            $table->string('no_hp');
            $table->string('email');
            $table->string('nilai_perolehan');
            $table->enum('sumber_dana', ['Universitas Diponegoro','APBN/APBD/Swasta','Mandiri'])
                ->default('Universitas Diponegoro');
            $table->enum('skema_penelitian', [
                'Penelitian Dasar (TKT 1 - 3)',
                'Penelitian Terapan (TKT 4 - 6)',
                'Penelitian Pengembangan (TKT 7 - 9)',
                'Bukan dihasilkan dari Skema Penelitian'
            ]);

            // DOKUMEN
            $table->string('surat_permohonan');
            $table->string('surat_pernyataan');
            $table->string('surat_pengalihan');
            $table->string('tanda_terima');
            $table->string('scan_ktp');
            $table->string('hasil_ciptaan');
            $table->string('link_ciptaan')->nullable();

            $table->enum('status', ['terkirim','proses','revisi','diterima','ditolak'])
                ->default('terkirim');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hak_cipta');
    }
};
