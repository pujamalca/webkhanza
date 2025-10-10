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
        Schema::table('marketing_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('marketing_categories', 'category_type')) {
                $table->enum('category_type', ['patient_marketing', 'bpjs_transfer'])
                    ->default('patient_marketing')
                    ->after('description')
                    ->comment('Type of category: patient_marketing for Data Pasien Marketing, bpjs_transfer for Pindah BPJS');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marketing_categories', function (Blueprint $table) {
            if (Schema::hasColumn('marketing_categories', 'category_type')) {
                $table->dropColumn('category_type');
            }
        });
    }
};
