<?php

namespace App\Livewire;

use App\Models\Country;
use App\Models\Postcard;
use App\Services\CurrencyService;
use App\Services\GeocodingService;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportPostcards extends Component
{
    use WithFileUploads;

    public $file_csv;

    public $message = '';

    public $messageType = 'success';

    public function import(CurrencyService $currencyService, GeocodingService $geoService)
    {
        $this->validate([
            'file_csv' => 'required|mimes:csv,txt|max:10240', // 10MB max
        ]);

        $path = $this->file_csv->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->message = 'Import failed: unable to read uploaded file.';
            $this->messageType = 'error';

            return;
        }

        // Skip header
        fgetcsv($handle, 1000, ';');

        $count = 0;
        $line = 1;
        $base = Money::base();

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 0, ';', '"')) !== false) {
                $line++;
                if (empty($data) || ! isset($data[0]) || empty(trim($data[0]))) {
                    continue;
                }

                $type = strtolower(trim($data[0]));
                $pc_id = trim($data[1] ?? '');

                // Dates: DD/MM/YYYY or YYYY-MM-DD -> YYYY-MM-DD
                $tgl_k = $this->parseDate($data[2] ?? '', $line);
                $tgl_t = $this->parseDate($data[3] ?? '', $line);

                $desc = trim($data[4] ?? '');
                $nama = trim($data[5] ?? '');
                $almt = trim(str_replace(["\r", "\n"], ' ', $data[6] ?? ''));

                $num_cols = count($data);
                if ($num_cols >= 11) {
                    // SENT structure (with phone)
                    $neg = trim($data[7] ?? '');
                    $telp = trim($data[8] ?? '');
                    $raw_cost = $data[9] ?? 0;
                    $curr_code = ! empty($data[10]) ? strtoupper(trim($data[10])) : $base;
                } else {
                    // RECEIVED structure (no phone)
                    $neg = trim($data[7] ?? '');
                    $telp = '';
                    $raw_cost = $data[8] ?? 0;
                    $curr_code = ! empty($data[9]) ? strtoupper(trim($data[9])) : $base;
                }

                $clean_cost = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $raw_cost));
                $cost = (float) $clean_cost;

                // Convert to the base currency at the rate for the send date
                $rate = 1.0;
                if ($curr_code !== $base) {
                    $rate = $currencyService->getHistoricalRate($curr_code, $tgl_k ?: date('Y-m-d'), $base);
                    if ($rate === null) {
                        throw new \RuntimeException("CSV row $line: no exchange rate available for $curr_code to $base.");
                    }
                }

                $biaya = Money::round($cost * $rate, $base);
                $coords = $geoService->getCoordinates($almt, $neg);

                // Fetch Country ID (English name, Indonesian name, or ISO code)
                $country = Country::findByName($neg);
                $country_id = $country?->id;

                // Sync with Contacts master data (to get contact_id and save pii)
                $contact = null;
                if ($nama && $nama !== '-') {
                    $contact = \App\Models\Contact::updateOrCreate(
                        ['user_id' => auth()->id(), 'nama_kontak' => $nama],
                        [
                            'alamat' => $almt,
                            'country_id' => $country_id,
                            'nomor_telepon' => $telp ?: '-',
                            'lat' => $coords['lat'],
                            'lng' => $coords['lng'],
                        ]
                    );
                }

                $newPostcard = Postcard::create([
                    'user_id' => auth()->id(),
                    'uid' => Str::ulid(),
                    'postcard_id' => $pc_id,
                    'type' => $type,
                    'contact_id' => $contact?->id,
                    'country_id' => $country_id,
                    'tanggal_kirim' => $tgl_k,
                    'tanggal_terima' => $tgl_t,
                    'biaya_prangko' => $biaya,
                    'nilai_asal' => $cost,
                    'mata_uang' => $curr_code,
                    'base_currency' => $base,
                    'exchange_rate' => $rate,
                    'deskripsi_gambar' => $desc,
                    'notif_read' => 1,
                ]);

                // Contact sync moved before Postcard creation

                $count++;
            }
            DB::commit();
            $this->message = "Successfully imported $count records!";
            $this->messageType = 'success';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import failed: '.$e->getMessage());
            $this->message = 'Import failed: '.$e->getMessage();
            $this->messageType = 'error';
        } finally {
            fclose($handle);
            $this->file_csv = null;
        }
    }

    /**
     * Parse a CSV date as DD/MM/YYYY or YYYY-MM-DD.
     *
     * createFromFormat() silently rolls invalid dates over (a US-style
     * "03/15/2024" read as day 3 of month 15 becomes 2025-03-03), so any
     * parse warning is treated as an error rather than stored.
     */
    protected function parseDate(string $raw, int $line): ?string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        foreach (['!d/m/Y', '!Y-m-d'] as $format) {
            $parsed = \DateTime::createFromFormat($format, $raw);
            $errors = \DateTime::getLastErrors();
            if ($parsed && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $parsed->format('Y-m-d');
            }
        }

        throw new \RuntimeException("CSV row $line: invalid date \"$raw\" (use DD/MM/YYYY or YYYY-MM-DD).");
    }

    public function render()
    {
        return view('livewire.import-postcards')->layout('components.layouts.app', ['title' => 'Mass Data Import - Migration']);
    }
}
