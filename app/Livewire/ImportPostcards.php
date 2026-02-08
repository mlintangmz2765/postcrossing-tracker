<?php

namespace App\Livewire;

use App\Models\Postcard;
use App\Services\CurrencyService;
use App\Services\GeocodingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        // Skip header
        fgetcsv($handle, 1000, ';');

        $count = 0;
        $errors = 0;

        DB::beginTransaction();
        try {
            while (($data = fgetcsv($handle, 0, ';', '"')) !== false) {
                if (empty($data) || ! isset($data[0]) || empty(trim($data[0]))) {
                    continue;
                }

                $type = strtolower(trim($data[0]));
                $pc_id = trim($data[1] ?? '');

                // Date conversion DD/MM/YYYY -> YYYY-MM-DD
                $tgl_k_raw = str_replace('/', '-', trim($data[2] ?? ''));
                $tgl_k = ! empty($tgl_k_raw) ? date('Y-m-d', strtotime($tgl_k_raw)) : null;

                $tgl_t_raw = str_replace('/', '-', trim($data[3] ?? ''));
                $tgl_t = ! empty($tgl_t_raw) ? date('Y-m-d', strtotime($tgl_t_raw)) : null;

                $desc = trim($data[4] ?? '');
                $nama = trim($data[5] ?? '');
                $almt = trim(str_replace(["\r", "\n"], ' ', $data[6] ?? ''));

                $num_cols = count($data);
                if ($num_cols >= 11) {
                    // SENT structure (with phone)
                    $neg = trim($data[7] ?? '');
                    $telp = trim($data[8] ?? '');
                    $raw_cost = $data[9] ?? 0;
                    $curr_code = ! empty($data[10]) ? strtoupper(trim($data[10])) : 'IDR';
                } else {
                    // RECEIVED structure (no phone)
                    $neg = trim($data[7] ?? '');
                    $telp = '';
                    $raw_cost = $data[8] ?? 0;
                    $curr_code = ! empty($data[9]) ? strtoupper(trim($data[9])) : 'IDR';
                }

                $clean_cost = preg_replace('/[^0-9.]/', '', str_replace(',', '.', $raw_cost));
                $cost = (float) $clean_cost;

                // Historical rate
                $kurs = 1;
                if ($type === 'received' && $curr_code !== 'IDR' && $tgl_k) {
                    $kurs = $currencyService->getHistoricalRate($curr_code, $tgl_k);
                }

                $biaya_idr = $cost * $kurs;
                $coords = $geoService->getCoordinates($almt, $neg);

                // Fetch Country ID
                $country = \App\Models\Country::where('nama_indonesia', $neg)->first();
                $country_id = $country?->id;

                // Sync with Contacts master data (to get contact_id and save pii)
                $contact = null;
                if ($nama && $nama !== '-') {
                    $contact = \App\Models\Contact::updateOrCreate(
                        ['user_id' => 1, 'nama_kontak' => $nama],
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
                    'user_id' => 1,
                    'uid' => uniqid('pc_'),
                    'postcard_id' => $pc_id,
                    'type' => $type,
                    'contact_id' => $contact?->id,
                    'country_id' => $country_id,
                    'tanggal_kirim' => $tgl_k,
                    'tanggal_terima' => $tgl_t,
                    'biaya_prangko' => round($biaya_idr),
                    'nilai_asal' => $cost,
                    'mata_uang' => $curr_code,
                    'kurs_idr' => $kurs,
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
        }

        fclose($handle);
        $this->file_csv = null;
    }

    public function render()
    {
        return view('livewire.import-postcards')->layout('components.layouts.app', ['title' => 'Mass Data Import - Migration']);
    }
}
