<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('user_profiles', 'pending_photo_path')) {
                $table->string('pending_photo_path')->nullable()->after('photo_path');
            }

            if (!Schema::hasColumn('user_profiles', 'photo_status')) {
                $table->string('photo_status', 20)->default('approved')->after('pending_photo_path');
            }

            if (!Schema::hasColumn('user_profiles', 'photo_submitted_at')) {
                $table->timestamp('photo_submitted_at')->nullable()->after('photo_status');
            }

            if (!Schema::hasColumn('user_profiles', 'photo_reviewed_at')) {
                $table->timestamp('photo_reviewed_at')->nullable()->after('photo_submitted_at');
            }

            if (!Schema::hasColumn('user_profiles', 'photo_reviewed_by_admin_id')) {
                $table->foreignId('photo_reviewed_by_admin_id')->nullable()->after('photo_reviewed_at')->constrained('admins')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('user_profiles', 'photo_reviewed_by_admin_id')) {
                $table->dropConstrainedForeignId('photo_reviewed_by_admin_id');
            }

            $columns = array_filter([
                Schema::hasColumn('user_profiles', 'pending_photo_path') ? 'pending_photo_path' : null,
                Schema::hasColumn('user_profiles', 'photo_status') ? 'photo_status' : null,
                Schema::hasColumn('user_profiles', 'photo_submitted_at') ? 'photo_submitted_at' : null,
                Schema::hasColumn('user_profiles', 'photo_reviewed_at') ? 'photo_reviewed_at' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
