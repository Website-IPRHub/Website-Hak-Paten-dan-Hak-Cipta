<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paten', function (Blueprint $table) {
            $table->string('skema_tkt_template_path')->nullable()->after('deskripsi_singkat_prototipe');
        });
    }

    public function down(): void
    {
        Schema::table('paten', function (Blueprint $table) {
            $table->dropColumn('skema_tkt_template_path');
        });
    }
};
