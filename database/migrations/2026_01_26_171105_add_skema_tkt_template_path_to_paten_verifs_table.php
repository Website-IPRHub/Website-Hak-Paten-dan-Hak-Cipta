<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('paten_verifs', function (Blueprint $table) {
            if (!Schema::hasColumn('paten_verifs', 'skema_tkt_template_path')) {
                $table->string('skema_tkt_template_path')->nullable()->after('skema_penelitian');
            }
        });
    }

    public function down(): void
    {
        Schema::table('paten_verifs', function (Blueprint $table) {
            if (Schema::hasColumn('paten_verifs', 'skema_tkt_template_path')) {
                $table->dropColumn('skema_tkt_template_path');
            }
        });
    }
};
