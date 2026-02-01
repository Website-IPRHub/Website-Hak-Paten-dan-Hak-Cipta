<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('revisions', function (Blueprint $table) {
            $table->id(); // bigint unsigned auto increment

            $table->enum('type', ['paten', 'cipta']);
            $table->string('ref_type', 20)->nullable(); // nullable DEFAULT NULL
            $table->unsignedBigInteger('ref_id');

            $table->string('doc_key', 120)->nullable(); // varchar(120) NULL
            $table->string('state', 20)->default('requested'); // NOT NULL default requested

            $table->enum('from_role', ['admin', 'pemohon']);

            $table->text('note')->nullable();
            $table->string('file_path', 255)->nullable();

            $table->boolean('is_read_admin')->default(false);   // tinyint(1) default 0
            $table->boolean('is_read_pemohon')->default(false); // tinyint(1) default 0

            $table->timestamps(); // created_at & updated_at timestamp NULL

            $table->string('pemohon_file_path', 255)->nullable();

            // Index sesuai icon "key" yang kelihatan di phpMyAdmin (type & ref_id)
            $table->index('type');
            $table->index('ref_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
    }
};
