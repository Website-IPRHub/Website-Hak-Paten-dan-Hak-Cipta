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
        if (!Schema::hasColumn('revisions', 'pemohon_file_name')) {
            $table->string('pemohon_file_name')->nullable()->after('pemohon_file_path');
        }
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revisions', function (Blueprint $table) {
            //
        });
    }
};
