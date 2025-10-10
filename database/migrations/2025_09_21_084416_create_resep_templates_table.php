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
        if (!Schema::hasTable('resep_templates')) {
            Schema::create('resep_templates', function (Blueprint $table) {
                $table->id();
                $table->string('nama_template');
                $table->text('keterangan')->nullable();
                $table->string('nip', 20);
                $table->boolean('is_public')->default(false);
                $table->string('kategori')->nullable();
                $table->timestamps();

                $table->index(['nip', 'is_public']);
                $table->index('kategori');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_templates');
    }
};
