<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('postcards', function (Blueprint $table) {
            $table->text('nomor_telepon')->nullable()->change();
            $table->text('lat')->nullable()->change();
            $table->text('lng')->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->text('nomor_telepon')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postcards', function (Blueprint $table) {
            $table->string('nomor_telepon', 20)->nullable()->change();
            $table->string('lat', 50)->nullable()->change();
            $table->string('lng', 50)->nullable()->change();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('nomor_telepon', 20)->nullable()->change();
        });
    }
};
