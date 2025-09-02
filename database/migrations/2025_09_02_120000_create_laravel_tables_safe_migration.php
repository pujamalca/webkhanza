<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar tabel yang AMAN untuk dikelola oleh Laravel
     * HANYA tabel ini yang akan di-drop/rollback
     */
    protected $laravelTables = [
        'migrations',
        'cache',
        'cache_locks', 
        'sessions',
        'jobs',
        'job_batches',
        'failed_jobs',
        'activity_log',
        'permissions',
        'roles',
        'model_has_permissions',
        'model_has_roles',
        'role_has_permissions',
        'personal_access_tokens',
        'password_reset_tokens',
        'absents',
        'cutis',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create Laravel-specific tables only if they don't exist
        
        // Cache tables
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }

        // Session table
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // Job tables
        if (!Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('queue')->index();
                $table->longText('payload');
                $table->unsignedTinyInteger('attempts');
                $table->unsignedInteger('reserved_at')->nullable();
                $table->unsignedInteger('available_at');
                $table->unsignedInteger('created_at');
            });
        }

        if (!Schema::hasTable('job_batches')) {
            Schema::create('job_batches', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->string('name');
                $table->integer('total_jobs');
                $table->integer('pending_jobs');
                $table->integer('failed_jobs');
                $table->longText('failed_job_ids');
                $table->mediumText('options')->nullable();
                $table->integer('cancelled_at')->nullable();
                $table->integer('created_at');
                $table->integer('finished_at')->nullable();
            });
        }

        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // Activity Log table
        if (!Schema::hasTable('activity_log')) {
            Schema::create('activity_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('log_name')->nullable();
                $table->text('description');
                $table->nullableMorphs('subject', 'subject');
                $table->nullableMorphs('causer', 'causer');
                $table->json('properties')->nullable();
                $table->string('batch_uuid')->nullable();
                $table->timestamps();
                $table->index('log_name');
            });
        }

        // Personal Access Tokens
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Password Reset Tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Spatie Permission tables
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();

                $table->unique(['name', 'guard_name']);
            });
        }

        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'model_id', 'model_type'],
                    'model_has_permissions_permission_model_type_primary');
            });
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type'], 'model_has_roles_model_id_model_type_index');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['role_id', 'model_id', 'model_type'],
                    'model_has_roles_role_model_type_primary');
            });
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');

                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');

                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

                $table->primary(['permission_id', 'role_id'], 'role_has_permissions_permission_id_role_id_primary');
            });
        }

        // Pegawai Cluster tables
        if (!Schema::hasTable('absents')) {
            Schema::create('absents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->date('date');
                $table->time('check_in')->nullable();
                $table->time('check_out')->nullable();
                $table->string('check_in_photo')->nullable();
                $table->string('check_out_photo')->nullable();
                $table->enum('status', ['present', 'late', 'absent', 'sick', 'permission'])->default('present');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['employee_id', 'date'], 'unique_employee_date');
                $table->index(['date', 'status']);
                $table->index('employee_id');
            });
        }

        if (!Schema::hasTable('cutis')) {
            Schema::create('cutis', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('leave_type', ['tahunan', 'sakit', 'darurat', 'melahirkan', 'menikah', 'lainnya'])->default('tahunan');
                $table->text('reason');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
                $table->index(['employee_id', 'status']);
                $table->index(['start_date', 'end_date']);
                $table->index('status');
            });
        }

        echo "✅ Laravel tables created safely\n";
    }

    /**
     * Reverse the migrations.
     * HANYA menghapus tabel Laravel, TIDAK menyentuh tabel aplikasi desktop
     */
    public function down(): void
    {
        echo "🔄 Rolling back Laravel tables only...\n";
        
        // Drop dalam urutan yang benar (foreign key dependencies)
        foreach (array_reverse($this->laravelTables) as $table) {
            if (Schema::hasTable($table)) {
                Schema::dropIfExists($table);
                echo "  ❌ Dropped table: {$table}\n";
            }
        }
        
        echo "✅ Laravel tables rollback completed. Desktop app tables preserved.\n";
    }
};