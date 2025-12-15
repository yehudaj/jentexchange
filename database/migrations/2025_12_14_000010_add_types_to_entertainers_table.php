<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('entertainers', 'types')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->json('types')->nullable()->after('genres');
            });
        }
        if (!Schema::hasColumn('entertainers', 'audiences')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->json('audiences')->nullable()->after('types');
            });
        }
    }

    public function down()
    {
        Schema::table('entertainers', function (Blueprint $table) {
            if (Schema::hasColumn('entertainers', 'audiences')) {
                $table->dropColumn('audiences');
            }
            if (Schema::hasColumn('entertainers', 'types')) {
                $table->dropColumn('types');
            }
        });
    }
};
