<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cutis')) {
            Schema::create('cutis', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id', 20);
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('leave_type', ['tahunan', 'sakit', 'darurat', 'melahirkan', 'menikah', 'lainnya'])
                      ->default('tahunan');
                $table->text('reason');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->string('approved_by', 20)->nullable();
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->index(['employee_id', 'status']);
                $table->index(['start_date', 'end_date']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cutis');
    }
};