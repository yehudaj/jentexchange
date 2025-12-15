<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('entertainers', 'pricing_packages')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->json('pricing_packages')->nullable()->after('cities');
            });
        }
    }

    public function down()
    {
        Schema::table('entertainers', function (Blueprint $table) {
            if (Schema::hasColumn('entertainers', 'pricing_packages')) {
                $table->dropColumn('pricing_packages');
            }
        });
    }
};
