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
        if (!Schema::hasTable('marketing_patient_tasks')) {
            Schema::create('marketing_patient_tasks', function (Blueprint $table) {
                $table->id();
                $table->string('patient_id', 17); // no_rawat format: 2025/04/25/000001
                $table->unsignedBigInteger('category_id');
                $table->boolean('is_completed')->default(false);
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('completed_by')->nullable(); // user who completed the task
                $table->timestamps();

                $table->unique(['patient_id', 'category_id']);
                $table->index(['patient_id']);
                $table->index(['category_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_patient_tasks');
    }
};