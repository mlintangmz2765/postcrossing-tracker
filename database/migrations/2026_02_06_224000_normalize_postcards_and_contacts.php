<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add new foreign key columns
        Schema::table('postcards', function (Blueprint $table) {
            $table->unsignedBigInteger('contact_id')->nullable()->after('user_id');
            $table->unsignedBigInteger('country_id')->nullable()->after('contact_id');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('country_id')->nullable()->after('user_id');
            $table->text('lat')->nullable()->after('nomor_telepon');
            $table->text('lng')->nullable()->after('lat');
        });

        // 2. Data Migration
        // Note: We use DB facade to avoid issues with Model booting/casting during migration

        // Map Contacts to Countries
        $contacts = DB::table('contacts')->get();
        foreach ($contacts as $contact) {
            $country = DB::table('countries')->where('nama_indonesia', $contact->negara)->first();
            if ($country) {
                DB::table('contacts')->where('id', $contact->id)->update(['country_id' => $country->id]);
            }
        }

        // Map Postcards to Countries & Contacts
        $postcards = DB::table('postcards')->get();
        foreach ($postcards as $postcard) {
            $country = DB::table('countries')->where('nama_indonesia', $postcard->negara)->first();
            $contact = DB::table('contacts')
                ->where('user_id', $postcard->user_id)
                ->where('nama_kontak', $postcard->nama_kontak)
                ->first();

            $update = [];
            if ($country) {
                $update['country_id'] = $country->id;
            }
            if ($contact) {
                $update['contact_id'] = $contact->id;
            }

            if (! empty($update)) {
                DB::table('postcards')->where('id', $postcard->id)->update($update);
            }

            // Also migrate coordinates to contact if not already there
            if ($contact && $postcard->lat && $postcard->lng) {
                DB::table('contacts')->where('id', $contact->id)->update([
                    'lat' => $postcard->lat,
                    'lng' => $postcard->lng,
                ]);
            }
        }

        // 3. Drop redundant columns
        Schema::table('postcards', function (Blueprint $table) {
            $table->dropColumn(['negara', 'nama_kontak', 'nomor_telepon', 'alamat', 'lat', 'lng']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('negara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add dropped columns
        Schema::table('postcards', function (Blueprint $table) {
            $table->string('negara')->nullable();
            $table->string('nama_kontak')->nullable();
            $table->text('nomor_telepon')->nullable();
            $table->text('alamat')->nullable();
            $table->text('lat')->nullable();
            $table->text('lng')->nullable();
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->string('negara')->nullable();
            $table->dropColumn(['country_id', 'lat', 'lng']);
        });

        Schema::table('postcards', function (Blueprint $table) {
            $table->dropColumn(['contact_id', 'country_id']);
        });
    }
};
