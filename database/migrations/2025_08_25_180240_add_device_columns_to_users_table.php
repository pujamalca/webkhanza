<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'device_token')) {
                $table->string('device_token')->nullable();
            }
            if (!Schema::hasColumn('users', 'device_info')) {
                $table->string('device_info')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'device_token')) {
                $table->dropColumn('device_token');
            }
            if (Schema::hasColumn('users', 'device_info')) {
                $table->dropColumn('device_info');
            }
        });
    }

};
