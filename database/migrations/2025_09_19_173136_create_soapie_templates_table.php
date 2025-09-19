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
        Schema::create('soapie_templates', function (Blueprint $table) {
            $table->id();
            $table->string('nama_template');
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->text('intervention')->nullable();
            $table->text('evaluation')->nullable();
            $table->string('nip'); // User who created the template
            $table->boolean('is_public')->default(false); // Public templates can be used by all users
            $table->string('kategori')->nullable(); // Category like "Umum", "Anak", "Bedah", etc.
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['nip']);
            $table->index(['is_public']);
            $table->index(['kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soapie_templates');
    }
};
