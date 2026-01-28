<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            if (!Schema::hasColumn('revisions', 'doc_key')) {
                $table->string('doc_key', 100)->nullable();
            }

            if (!Schema::hasColumn('revisions', 'pemohon_file_path')) {
                $table->string('pemohon_file_path')->nullable();
            }

            if (!Schema::hasColumn('revisions', 'state')) {
                $table->string('state', 30)->default('pending');
            }

            if (!Schema::hasColumn('revisions', 'is_read_admin')) {
                $table->boolean('is_read_admin')->default(false);
            }

            if (!Schema::hasColumn('revisions', 'is_read_pemohon')) {
                $table->boolean('is_read_pemohon')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            if (Schema::hasColumn('revisions', 'doc_key')) $table->dropColumn('doc_key');
            if (Schema::hasColumn('revisions', 'pemohon_file_path')) $table->dropColumn('pemohon_file_path');
            if (Schema::hasColumn('revisions', 'state')) $table->dropColumn('state');
            if (Schema::hasColumn('revisions', 'is_read_admin')) $table->dropColumn('is_read_admin');
            if (Schema::hasColumn('revisions', 'is_read_pemohon')) $table->dropColumn('is_read_pemohon');
        });
    }
};



