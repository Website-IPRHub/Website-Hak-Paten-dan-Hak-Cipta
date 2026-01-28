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
        Schema::create('status_verifikasi', function (Blueprint $table) {
            $table->id();

            $table->enum('ref_type', ['paten', 'cipta']);
            $table->unsignedBigInteger('ref_id');

            $table->enum('status', [
                'Terkirim',
                'Proses',
                'Revisi',
                'Approve',
            ])->default('Approve');

            $table->string('sertifikat_path')->nullable();
            $table->timestamp('emailed_at')->nullable();

            $table->timestamps();

            $table->unique(['ref_type', 'ref_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_verifikasi');
    }
};
