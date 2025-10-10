<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('absents')) {
            Schema::create('absents', function (Blueprint $table) {
                $table->id();
                $table->string('employee_id', 20);
                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->date('date');
                $table->time('check_in')->nullable();
                $table->time('check_out')->nullable();
                $table->string('check_in_photo')->nullable();
                $table->string('check_out_photo')->nullable();
                $table->enum('status', ['hadir', 'tidak_hadir', 'terlambat', 'izin'])->default('hadir');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['employee_id', 'date']);
                $table->index(['employee_id', 'date']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('absents');
    }
};