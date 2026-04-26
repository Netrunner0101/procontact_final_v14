<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portal_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('token_hash', 255)->unique();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'revoked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_portal_tokens');
    }
};
