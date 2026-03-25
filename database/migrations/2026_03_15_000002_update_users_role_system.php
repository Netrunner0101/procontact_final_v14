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
        // Step 1: Add role_id and contact_id columns
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('restrict');
            $table->foreignId('contact_id')->nullable()->after('admin_user_id')->constrained('contacts')->onDelete('set null');
        });

        // Step 2: Migrate existing data from enum 'role' to role_id FK
        DB::statement("UPDATE users SET role_id = 1 WHERE role = 'admin' OR role IS NULL");
        DB::statement("UPDATE users SET role_id = 2 WHERE role = 'client'");

        // Step 3: Make role_id NOT NULL now that all rows have a value
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable(false)->change();
        });

        // Step 4: Drop old enum column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add enum column
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'client'])->default('admin')->after('id');
        });

        // Migrate data back
        DB::statement("UPDATE users SET role = 'admin' WHERE role_id = 1");
        DB::statement("UPDATE users SET role = 'client' WHERE role_id = 2");

        // Drop new columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['contact_id']);
            $table->dropColumn('contact_id');
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
