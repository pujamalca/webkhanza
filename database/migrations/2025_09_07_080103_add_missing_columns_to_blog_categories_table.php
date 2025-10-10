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
        Schema::table('blog_categories', function (Blueprint $table) {
            // Add missing columns that the model expects
            if (!Schema::hasColumn('blog_categories', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('image');
                $table->foreign('parent_id')->references('id')->on('blog_categories')->onDelete('cascade');
            }

            if (!Schema::hasColumn('blog_categories', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('meta_keywords');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blog_categories', function (Blueprint $table) {
            if (Schema::hasColumn('blog_categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }

            if (Schema::hasColumn('blog_categories', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
