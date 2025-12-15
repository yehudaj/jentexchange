<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('role');
        });

        // Backfill existing 'role' string into 'roles' JSON array
        if (app()->runningInConsole()) {
            \DB::table('users')->select('id', 'role')->orderBy('id')->chunk(100, function ($users) {
                foreach ($users as $u) {
                    if ($u->role) {
                        \DB::table('users')->where('id', $u->id)->update(['roles' => json_encode([$u->role])]);
                    }
                }
            });
        } else {
            // runtime fallback
            foreach (\DB::table('users')->get() as $u) {
                if ($u->role) {
                    \DB::table('users')->where('id', $u->id)->update(['roles' => json_encode([$u->role])]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('roles');
        });
    }
};
