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
        Schema::table('revisions', function (Blueprint $table) {
            if (!Schema::hasColumn('revisions', 'admin_file_name')) {
                $table->string('admin_file_name')->nullable()->after('file_path');
            }
            if (!Schema::hasColumn('revisions', 'pemohon_file_name')) {
                $table->string('pemohon_file_name')->nullable()->after('pemohon_file_path');
            }
            if (!Schema::hasColumn('revisions', 'pemohon_uploaded_at')) {
                $table->timestamp('pemohon_uploaded_at')->nullable()->after('pemohon_file_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            if (Schema::hasColumn('revisions', 'pemohon_uploaded_at')) $table->dropColumn('pemohon_uploaded_at');
            if (Schema::hasColumn('revisions', 'pemohon_file_name')) $table->dropColumn('pemohon_file_name');
            if (Schema::hasColumn('revisions', 'admin_file_name')) $table->dropColumn('admin_file_name');
        });
    }
};
