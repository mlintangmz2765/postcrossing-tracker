<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Make postcard costs independent of a hardcoded IDR base currency.
 *
 * - kurs_idr -> exchange_rate: the rate is now "to the base currency", and
 *   2 decimals cannot hold rates into strong currencies (JPY->USD is
 *   0.0067222), so the column is widened to 10 decimals.
 * - base_currency: the currency biaya_prangko is recorded in, stored per row
 *   so changing APP_BASE_CURRENCY never silently relabels existing amounts.
 *   Rows that already exist were converted by code that only supported IDR,
 *   so they are backfilled as IDR.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('postcards', function (Blueprint $table) {
            $table->renameColumn('kurs_idr', 'exchange_rate');
        });

        Schema::table('postcards', function (Blueprint $table) {
            $table->decimal('exchange_rate', 20, 10)->nullable()->default(1)->change();
            $table->string('base_currency', 3)->nullable()->after('mata_uang');
        });

        DB::table('postcards')->whereNull('base_currency')->update(['base_currency' => 'IDR']);
    }

    public function down(): void
    {
        Schema::table('postcards', function (Blueprint $table) {
            $table->dropColumn('base_currency');
        });

        Schema::table('postcards', function (Blueprint $table) {
            $table->decimal('exchange_rate', 15, 2)->nullable()->default(1)->change();
        });

        Schema::table('postcards', function (Blueprint $table) {
            $table->renameColumn('exchange_rate', 'kurs_idr');
        });
    }
};
