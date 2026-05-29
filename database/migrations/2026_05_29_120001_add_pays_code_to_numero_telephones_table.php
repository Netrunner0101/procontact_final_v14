<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link each phone number to a country so the prefix (indicatif) is
     * normalised: the same national number can now coexist under two
     * different countries/prefixes (e.g. 470123456 in +32 and in +33).
     *
     * Relation: Pays (0,n) ---- (0,1) NumeroTelephone.
     * Nullable because phone numbers already exist in the table without a
     * country; the form sets it for new numbers.
     */
    public function up(): void
    {
        Schema::table('numero_telephones', function (Blueprint $table) {
            $table->char('pays_code', 2)->nullable()->after('numero_telephone');
            $table->foreign('pays_code')->references('code')->on('pays')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('numero_telephones', function (Blueprint $table) {
            $table->dropForeign(['pays_code']);
            $table->dropColumn('pays_code');
        });
    }
};
