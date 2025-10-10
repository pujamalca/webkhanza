<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if we need to modify the columns (if they're not already string type)
        if (Schema::hasTable('model_has_permissions')) {
            $columns = DB::select("SHOW COLUMNS FROM model_has_permissions WHERE Field = 'model_id'");
            if (!empty($columns)) {
                $type = $columns[0]->Type ?? '';
                // Only modify if it's not already varchar/string
                if (stripos($type, 'bigint') !== false || stripos($type, 'int') !== false) {
                    Schema::table('model_has_permissions', function (Blueprint $table) {
                        $table->string('model_id', 20)->change();
                    });
                }
            }
        }

        if (Schema::hasTable('model_has_roles')) {
            $columns = DB::select("SHOW COLUMNS FROM model_has_roles WHERE Field = 'model_id'");
            if (!empty($columns)) {
                $type = $columns[0]->Type ?? '';
                // Only modify if it's not already varchar/string
                if (stripos($type, 'bigint') !== false || stripos($type, 'int') !== false) {
                    Schema::table('model_has_roles', function (Blueprint $table) {
                        $table->string('model_id', 20)->change();
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('model_has_permissions')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('model_id')->change();
            });
        }

        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('model_id')->change();
            });
        }
    }
};
