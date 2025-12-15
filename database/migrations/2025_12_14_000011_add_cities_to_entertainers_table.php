<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('entertainers', 'cities')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->json('cities')->nullable()->after('audiences');
            });
        }
    }

    public function down()
    {
        Schema::table('entertainers', function (Blueprint $table) {
            if (Schema::hasColumn('entertainers', 'cities')) {
                $table->dropColumn('cities');
            }
        });
    }
};
