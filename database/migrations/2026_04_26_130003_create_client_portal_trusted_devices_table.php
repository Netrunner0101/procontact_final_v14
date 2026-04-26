<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portal_trusted_devices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('cookie_hash', 255)->unique();
            $table->string('user_agent_hash', 64)->nullable();
            $table->string('ip_address_first_seen', 45)->nullable();
            $table->timestamp('last_used_at');
            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_portal_trusted_devices');
    }
};
