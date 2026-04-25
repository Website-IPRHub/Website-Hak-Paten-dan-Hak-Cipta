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
        Schema::table('pemohons', function (Blueprint $table) {
            $table->longText('google_drive_token')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('pemohons', function (Blueprint $table) {
            $table->dropColumn('google_drive_token');
        });
    }
};
