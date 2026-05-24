<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->string('rue')->nullable();
            $table->string('numero_rue', 20)->nullable();
            $table->string('code_postal', 20)->nullable();
            $table->string('ville')->nullable();
            $table->char('pays_code', 2)->nullable();
            $table->boolean('is_principale')->default(false);
            $table->timestamps();

            $table->foreign('pays_code')->references('code')->on('pays')->nullOnDelete();
            $table->index(['addressable_type', 'addressable_id', 'is_principale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adresses');
    }
};
