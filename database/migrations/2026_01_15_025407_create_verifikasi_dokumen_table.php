<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('verifikasi_dokumen', function (Blueprint $table) {
            $table->id();
            $table->enum('ref_type', ['paten', 'cipta']);
            $table->unsignedBigInteger('ref_id');
            $table->string('doc_key'); // contoh: draft_paten, scan_ktp, hasil_ciptaan, dll
            $table->enum('status', ['pending', 'ok', 'revisi'])->default('pending');
            $table->text('note')->nullable();
            $table->string('admin_attachment_path')->nullable(); // file hasil revisi admin (opsional)
            $table->timestamp('requested_at')->nullable();
            $table->timestamps();

            $table->unique(['ref_type', 'ref_id', 'doc_key']);
            $table->index(['ref_type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifikasi_dokumen');
    }
};

