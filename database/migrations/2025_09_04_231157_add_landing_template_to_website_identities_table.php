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
        Schema::table('website_identities', function (Blueprint $table) {
            $table->enum('landing_template', ['default', 'doctor', 'clinic', 'hospital', 'pharmacy'])
                  ->default('default')
                  ->after('description')
                  ->comment('Landing page template type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_identities', function (Blueprint $table) {
            $table->dropColumn('landing_template');
        });
    }
};
