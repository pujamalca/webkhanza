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
        Schema::create('resep_template_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('template_id');
            $table->string('kode_brng', 15);
            $table->decimal('jumlah', 8, 2);
            $table->text('aturan_pakai');
            $table->timestamps();

            $table->foreign('template_id')->references('id')->on('resep_templates')->onDelete('cascade');
            $table->index('kode_brng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_template_details');
    }
};
