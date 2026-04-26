<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_portal_otps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->string('code_hash', 255);
            $table->string('email_hash', 64);
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['contact_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_portal_otps');
    }
};
