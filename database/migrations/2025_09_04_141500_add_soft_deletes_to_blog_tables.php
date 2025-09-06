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
        if (!Schema::hasColumn('blogs', 'deleted_at')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        
        if (!Schema::hasColumn('blog_categories', 'deleted_at')) {
            Schema::table('blog_categories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
        
        if (!Schema::hasColumn('blog_tags', 'deleted_at')) {
            Schema::table('blog_tags', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('blog_tags', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};