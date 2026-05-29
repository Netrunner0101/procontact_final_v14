<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move the single `users.telephone` column to the `numero_telephones`
     * table so that an independent (admin) user can own zero or many phone
     * numbers, mirroring how Contact phone numbers already work.
     */
    public function up(): void
    {
        // A phone may now belong directly to a user (no contact attached).
        Schema::table('numero_telephones', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->nullable()->change();
        });

        // Preserve existing data: migrate each user's single phone into the
        // relation (user_id set, contact_id null = the user's own number).
        if (Schema::hasColumn('users', 'telephone')) {
            DB::table('users')
                ->whereNotNull('telephone')
                ->where('telephone', '!=', '')
                ->orderBy('id')
                ->each(function ($user) {
                    DB::table('numero_telephones')->insert([
                        'user_id' => $user->id,
                        'contact_id' => null,
                        'numero_telephone' => $user->telephone,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('telephone');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'telephone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('telephone')->nullable()->after('email');
            });
        }

        // Restore the first user-owned phone back onto the column.
        DB::table('numero_telephones')
            ->whereNotNull('user_id')
            ->whereNull('contact_id')
            ->orderBy('id')
            ->get()
            ->groupBy('user_id')
            ->each(function ($phones, $userId) {
                DB::table('users')
                    ->where('id', $userId)
                    ->update(['telephone' => $phones->first()->numero_telephone]);
            });

        // Remove the user-only rows we created, then make contact_id required
        // again now that no orphan (contact-less) rows remain.
        DB::table('numero_telephones')
            ->whereNotNull('user_id')
            ->whereNull('contact_id')
            ->delete();

        Schema::table('numero_telephones', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->nullable(false)->change();
        });
    }
};
