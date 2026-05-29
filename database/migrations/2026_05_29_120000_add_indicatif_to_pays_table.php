<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the telephone calling code (indicatif) to each country so the phone
     * prefix can be decomposed per country instead of being glued onto the
     * `numero_telephone` string.
     */
    public function up(): void
    {
        Schema::table('pays', function (Blueprint $table) {
            // e.g. "+32" (Belgium), "+33" (France). Nullable so existing rows
            // (if any) don't break; the seeder fills the reference list.
            $table->string('indicatif', 8)->nullable()->after('nom');
        });
    }

    public function down(): void
    {
        Schema::table('pays', function (Blueprint $table) {
            $table->dropColumn('indicatif');
        });
    }
};
