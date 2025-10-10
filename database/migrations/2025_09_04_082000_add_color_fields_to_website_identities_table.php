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
            if (!Schema::hasColumn('website_identities', 'primary_color')) {
                $table->string('primary_color', 7)->default('#3B82F6')->comment('Warna utama (hex code)')->after('tagline');
            }
            if (!Schema::hasColumn('website_identities', 'secondary_color')) {
                $table->string('secondary_color', 7)->default('#1E40AF')->comment('Warna sekunder (hex code)')->after('primary_color');
            }
            if (!Schema::hasColumn('website_identities', 'accent_color')) {
                $table->string('accent_color', 7)->default('#EF4444')->comment('Warna aksen (hex code)')->after('secondary_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('website_identities', function (Blueprint $table) {
            $columns = ['primary_color', 'secondary_color', 'accent_color'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('website_identities', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
