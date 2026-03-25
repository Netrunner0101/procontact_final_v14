<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('is_shared_with_client')->default(false)->after('commentaire');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('portal_token')->nullable()->unique()->after('status_id');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn('is_shared_with_client');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('portal_token');
        });
    }
};
