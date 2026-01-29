<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hak_cipta', function (Blueprint $table) {
            $table->json('inventors')->nullable()->after('no_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::table('hak_cipta', function (Blueprint $table) {
            $table->dropColumn('inventors');
        });
    }
};
