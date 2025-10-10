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
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id')) {
            // Only change if id is not already a string
            $columnType = Schema::getColumnType('users', 'id');
            if ($columnType !== 'string') {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('id', 20)->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->id()->change();
        });
    }
};
