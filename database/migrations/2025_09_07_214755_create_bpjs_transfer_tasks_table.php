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
        if (!Schema::hasTable('bpjs_transfer_tasks')) {
            Schema::create('bpjs_transfer_tasks', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bpjs_transfer_id'); // Foreign key ke bpjs_transfers
                $table->unsignedBigInteger('category_id'); // Foreign key ke marketing_categories
                $table->boolean('is_completed')->default(false); // Status completion
                $table->unsignedBigInteger('completed_by')->nullable(); // User yang complete
                $table->timestamp('completed_at')->nullable(); // Waktu completion
                $table->text('notes')->nullable(); // Catatan task
                $table->timestamps();

                // Constraints dan indexes
                $table->unique(['bpjs_transfer_id', 'category_id']); // One task per category per transfer
                $table->index(['bpjs_transfer_id']);
                $table->index(['category_id']);
                $table->index(['completed_by']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs_transfer_tasks');
    }
};
