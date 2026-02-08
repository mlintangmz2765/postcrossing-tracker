<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EncryptExistingPii extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pii:encrypt {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt existing plain-text PII (alamat, nomor_telepon, lat, lng) in contacts and postcards tables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will encrypt all alamat and nomor_telepon data. Have you backed up your database?')) {
                $this->warn('Aborted. Please backup first!');

                return 1;
            }
        }

        $this->info('Encrypting Contacts table...');
        $this->encryptTable('contacts');

        $this->info('Encrypting Postcards table...');
        $this->encryptTable('postcards');

        $this->newLine();
        $this->info('✅ Encryption complete! All PII data is now secured.');
        $this->warn('IMPORTANT: Keep your APP_KEY safe. Losing it = losing access to encrypted data forever.');

        return 0;
    }

    protected function encryptTable(string $table)
    {
        // Get actual columns in the database for this table
        $existingColumns = Schema::getColumnListing($table);
        $allPossible = ['id', 'alamat', 'nomor_telepon', 'lat', 'lng'];

        // Filter out columns that don't exist in the actual table
        $columns = array_intersect($allPossible, $existingColumns);

        if (count($columns) <= 1) {
            $this->line(" No PII columns found in $table, skipping.");

            return;
        }

        $records = DB::table($table)->select($columns)->get();
        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $record) {
            $updates = [];

            // Only encrypt if not already encrypted (checking for Laravel encrypted format)
            if ($record->alamat && ! $this->isAlreadyEncrypted($record->alamat)) {
                $updates['alamat'] = Crypt::encryptString($record->alamat);
            }

            if ($record->nomor_telepon && ! $this->isAlreadyEncrypted($record->nomor_telepon)) {
                $updates['nomor_telepon'] = Crypt::encryptString($record->nomor_telepon);
            }

            // Encrypt coordinates if present in the current row
            if (isset($record->lat) && $record->lat && ! $this->isAlreadyEncrypted($record->lat)) {
                $updates['lat'] = Crypt::encryptString((string) $record->lat);
            }

            if (isset($record->lng) && $record->lng && ! $this->isAlreadyEncrypted($record->lng)) {
                $updates['lng'] = Crypt::encryptString((string) $record->lng);
            }

            if (! empty($updates)) {
                DB::table($table)->where('id', $record->id)->update($updates);
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
    }

    protected function isAlreadyEncrypted(string $value): bool
    {
        // Laravel encrypted strings start with 'eyJ' (base64 encoded JSON with iv, value, mac)
        return str_starts_with($value, 'eyJ');
    }
}
