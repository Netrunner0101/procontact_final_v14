<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activites - filtered by user_id in almost every query
        Schema::table('activites', function (Blueprint $table) {
            $table->index('user_id');
        });

        // Contacts - filtered by user_id and status_id
        Schema::table('contacts', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status_id');
        });

        // Rendez-vous - filtered by user_id, contact_id, activite_id, date_debut
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('contact_id');
            $table->index('activite_id');
            $table->index('date_debut');
        });

        // Notes - filtered by user_id, rendez_vous_id, activite_id
        Schema::table('notes', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('rendez_vous_id');
        });

        // Rappels - filtered by rendez_vous_id
        Schema::table('rappels', function (Blueprint $table) {
            $table->index('rendez_vous_id');
        });

        // Emails - filtered by contact_id
        Schema::table('emails', function (Blueprint $table) {
            $table->index('contact_id');
        });

        // Numero telephones - filtered by contact_id
        Schema::table('numero_telephones', function (Blueprint $table) {
            $table->index('contact_id');
        });

        // Statistiques - filtered by activite_id, rendez_vous_id, contact_id
        Schema::table('statistiques', function (Blueprint $table) {
            $table->index('activite_id');
            $table->index('contact_id');
        });
    }

    public function down(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status_id']);
        });
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['contact_id']);
            $table->dropIndex(['activite_id']);
            $table->dropIndex(['date_debut']);
        });
        Schema::table('notes', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['rendez_vous_id']);
        });
        Schema::table('rappels', function (Blueprint $table) {
            $table->dropIndex(['rendez_vous_id']);
        });
        Schema::table('emails', function (Blueprint $table) {
            $table->dropIndex(['contact_id']);
        });
        Schema::table('numero_telephones', function (Blueprint $table) {
            $table->dropIndex(['contact_id']);
        });
        Schema::table('statistiques', function (Blueprint $table) {
            $table->dropIndex(['activite_id']);
            $table->dropIndex(['contact_id']);
        });
    }
};
