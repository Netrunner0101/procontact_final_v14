<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('note_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->cascadeOnDelete();
            $table->string('titre');
            $table->text('commentaire');
            $table->timestamps();

            $table->index(['user_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('note_templates');
    }
};
