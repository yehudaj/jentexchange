<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('entertainers', function (Blueprint $table) {
            if (!Schema::hasColumn('entertainers', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('entertainers', 'stage_name')) {
                $table->string('stage_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('entertainers', 'bio')) {
                $table->text('bio')->nullable()->after('stage_name');
            }
            if (!Schema::hasColumn('entertainers', 'price_usd')) {
                $table->decimal('price_usd', 10, 2)->nullable()->after('bio');
            }
            if (!Schema::hasColumn('entertainers', 'pricing_notes')) {
                $table->text('pricing_notes')->nullable()->after('price_usd');
            }
            if (!Schema::hasColumn('entertainers', 'genres')) {
                $table->json('genres')->nullable()->after('pricing_notes');
            }
            if (!Schema::hasColumn('entertainers', 'types')) {
                $table->json('types')->nullable()->after('genres');
            }
            if (!Schema::hasColumn('entertainers', 'audiences')) {
                $table->json('audiences')->nullable()->after('types');
            }
            if (!Schema::hasColumn('entertainers', 'cities')) {
                $table->json('cities')->nullable()->after('audiences');
            }
            if (!Schema::hasColumn('entertainers', 'pricing_packages')) {
                $table->json('pricing_packages')->nullable()->after('cities');
            }
            if (!Schema::hasColumn('entertainers', 'video_links')) {
                $table->json('video_links')->nullable()->after('pricing_packages');
            }
            if (!Schema::hasColumn('entertainers', 'profile_image_path')) {
                $table->string('profile_image_path')->nullable()->after('video_links');
            }
            if (!Schema::hasColumn('entertainers', 'background_image_path')) {
                $table->string('background_image_path')->nullable()->after('profile_image_path');
            }
        });
    }

    public function down()
    {
        // Don't drop columns in down to avoid accidental data loss in this sync migration
    }
};
