<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Base schema as it existed before the 2026_02_06 migrations.
 *
 * Earlier installs created these tables from an SQL dump that was never
 * committed, so a fresh `php artisan migrate` failed on the first ALTER.
 * Every table is guarded with hasTable(), so running this on an existing
 * install is a no-op.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username', 50)->unique();
                $table->string('password');
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('nama_indonesia', 100);
                $table->string('nama_inggris', 100)->nullable();
                $table->char('kode_iso', 2)->unique();
                $table->string('benua', 50)->nullable();
                $table->string('subbenua', 50)->nullable();
            });
        }

        if (! Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('nama_kontak', 100)->nullable();
                $table->text('alamat')->nullable();
                $table->string('nomor_telepon', 20)->nullable();
                $table->string('negara')->nullable();
                $table->timestamp('updated_at')->nullable()->useCurrent();
            });
        }

        if (! Schema::hasTable('postcards')) {
            Schema::create('postcards', function (Blueprint $table) {
                $table->id();
                $table->string('uid', 50)->unique();
                $table->unsignedBigInteger('user_id')->index();
                $table->enum('type', ['sent', 'received']);
                $table->string('postcard_id', 50)->nullable();
                $table->string('negara')->nullable();
                $table->string('nama_kontak')->nullable();
                $table->string('nomor_telepon', 20)->nullable();
                $table->text('alamat')->nullable();
                $table->string('lat', 50)->nullable();
                $table->string('lng', 50)->nullable();
                $table->date('tanggal_kirim')->nullable();
                $table->date('tanggal_terima')->nullable();
                $table->text('deskripsi_gambar')->nullable();
                $table->text('pesan_penerima')->nullable();
                $table->decimal('biaya_prangko', 15, 2)->nullable()->default(0);
                $table->decimal('nilai_asal', 15, 2)->nullable()->default(0);
                $table->string('mata_uang', 5)->nullable();
                $table->decimal('kurs_idr', 15, 2)->nullable()->default(1);
                $table->string('foto_depan')->nullable();
                $table->string('foto_belakang')->nullable();
                $table->boolean('notif_read')->default(true);
            });
        }

        if (! Schema::hasTable('postcard_stamps')) {
            Schema::create('postcard_stamps', function (Blueprint $table) {
                $table->id();
                $table->foreignId('postcard_id')->constrained('postcards')->cascadeOnDelete();
                $table->string('foto_prangko');
            });
        }
    }

    /**
     * Intentionally a no-op: on installs that pre-date migrations these tables
     * hold real data this migration did not create, so a rollback must never
     * drop them.
     */
    public function down(): void
    {
        //
    }
};
