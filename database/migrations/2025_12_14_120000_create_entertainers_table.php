<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entertainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stage_name')->nullable();
            $table->text('bio')->nullable();
            $table->decimal('price_usd', 8, 2)->nullable();
            $table->string('pricing_notes')->nullable();
            $table->json('genres')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entertainers');
    }
};
