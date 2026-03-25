<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            if (!Schema::hasColumn('notes', 'priorite')) {
                $table->string('priorite')->default('Normale')->after('commentaire');
            }
            if (!Schema::hasColumn('notes', 'contact_id')) {
                $table->unsignedBigInteger('contact_id')->nullable()->after('activite_id');
            }
            if (!Schema::hasColumn('notes', 'is_shared_with_client')) {
                $table->boolean('is_shared_with_client')->default(false)->after('commentaire');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn(['priorite', 'contact_id', 'is_shared_with_client']);
        });
    }
};
