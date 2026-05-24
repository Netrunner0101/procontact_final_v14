<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rue', 'numero_rue', 'ville', 'code_postal', 'pays']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn(['rue', 'numero', 'ville', 'code_postal', 'pays']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rue')->nullable();
            $table->string('numero_rue')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->nullable();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('rue')->nullable();
            $table->string('numero')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->nullable();
        });
    }
};
