<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) beresin data lama supaya cocok sama enum baru
        DB::table('hak_cipta')
            ->whereIn('status', ['Draft', 'draft', '', null])
            ->update(['status' => 'Proses']);

        DB::table('hak_cipta')
            ->whereIn('status', ['proses'])
            ->update(['status' => 'Proses']);

        DB::table('hak_cipta')
            ->whereIn('status', ['terkirim'])
            ->update(['status' => 'Terkirim']);

        DB::table('hak_cipta')
            ->whereIn('status', ['revisi'])
            ->update(['status' => 'Revisi']);

        DB::table('hak_cipta')
            ->whereIn('status', ['diterima', 'approve'])
            ->update(['status' => 'Approve']);

        // 2) ubah enum + set default
        DB::statement("
            ALTER TABLE hak_cipta
            MODIFY status ENUM('Terkirim','Proses','Revisi','Approve')
            NOT NULL DEFAULT 'Proses'
        ");
    }

    public function down(): void
    {
        // Balikin ke enum lama (isi sesuai enum lamamu)
        DB::statement("
            ALTER TABLE hak_cipta
            MODIFY status ENUM('terkirim','proses','revisi','diterima')
            NOT NULL DEFAULT 'proses'
        ");

        // (opsional) mapping balik kalau perlu
        // DB::table('hak_cipta')->where('status','Approve')->update(['status'=>'diterima']);
    }
};

