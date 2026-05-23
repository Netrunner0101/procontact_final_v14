<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('gdpr_consent_token', 64)->nullable()->unique();
            $table->timestamp('gdpr_consent_requested_at')->nullable();
            $table->timestamp('gdpr_consent_signed_at')->nullable();
            $table->timestamp('gdpr_consent_declined_at')->nullable();
            $table->string('gdpr_consent_version', 16)->nullable();
            $table->string('gdpr_consent_ip', 45)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropUnique(['gdpr_consent_token']);
            $table->dropColumn([
                'gdpr_consent_token',
                'gdpr_consent_requested_at',
                'gdpr_consent_signed_at',
                'gdpr_consent_declined_at',
                'gdpr_consent_version',
                'gdpr_consent_ip',
            ]);
        });
    }
};
