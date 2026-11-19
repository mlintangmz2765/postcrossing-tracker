<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-card "sent from" places.
 *
 * origins holds places the owner has sent cards from (name plus encrypted
 * coordinates, like contacts). postcards.origin_id is NULL for cards sent
 * from home, which resolves to HOME_LAT / HOME_LNG / OWNER_LOCATION at read
 * time, so existing rows keep the home origin and home coordinates are
 * never copied into the database.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('origins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('name', 150);
            $table->text('lat'); // encrypted
            $table->text('lng'); // encrypted
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });

        Schema::table('postcards', function (Blueprint $table) {
            $table->foreignId('origin_id')->nullable()->after('country_id')
                ->constrained('origins')->nullOnDelete();
        });
    }

    /**
     * Rolling back drops every saved origin; cards fall back to home.
     */
    public function down(): void
    {
        Schema::table('postcards', function (Blueprint $table) {
            $table->dropConstrainedForeignId('origin_id');
        });

        Schema::dropIfExists('origins');
    }
};
