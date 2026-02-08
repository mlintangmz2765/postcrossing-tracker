<?php

namespace App\Livewire;

use App\Models\Postcard;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class ReceiveConfirm extends Component
{
    public $uid;

    public $postcard;

    public $message = '';

    public $step = 1;

    // Derived state
    public $alreadyConfirmed = false;

    public $justConfirmed = false;

    public $isSwap = false;

    public $isChina = false;

    public $distance = 0;

    // Map markers
    public $myLat;

    public $myLng;

    public $targetLat = 0;

    public $targetLng = 0;

    public function mount($uid)
    {
        $this->myLat = (float) config('app.home_lat');
        $this->myLng = (float) config('app.home_lng');
        $this->uid = $uid;
        $this->postcard = Postcard::with(['contact', 'country'])->where('uid', $uid)->where('type', 'sent')->firstOrFail();

        $this->alreadyConfirmed = ! empty($this->postcard->tanggal_terima) && $this->postcard->tanggal_terima != '0000-00-00';
        $this->isSwap = ! Str::contains($this->postcard->postcard_id, '-');

        // Detect China Viewer
        $this->detectChinaViewer();

        // China keyword check for destination (AMap is better for China addresses)
        $isToChina = false;
        $chinaKeywords = ['china', 'tiongkok', 'prc', 'people\'s republic of china'];
        $countryName = $this->postcard->country?->nama_indonesia ?? '';
        foreach ($chinaKeywords as $kw) {
            if (stripos($countryName, $kw) !== false) {
                $isToChina = true;
                break;
            }
        }

        if ($isToChina) {
            $this->isChina = true;
        }

        // Coordinates
        $this->targetLat = (float) ($this->postcard->contact?->lat ?? 0);
        $this->targetLng = (float) ($this->postcard->contact?->lng ?? 0);

        if ($this->targetLat && $this->targetLng) {
            $this->distance = $this->calculateDistance($this->myLat, $this->myLng, $this->targetLat, $this->targetLng);
        }
    }

    public function detectChinaViewer()
    {
        // URL Override
        if (request()->has('china')) {
            $this->isChina = (request()->query('china') == '1');

            return;
        }

        // Cloudflare IP Detection
        $cfCountry = request()->header('CF-IPCountry');
        if ($cfCountry === 'CN') {
            $this->isChina = true;

            return;
        }

        $this->isChina = false;
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;

        return round($miles * 1.609344);
    }

    public function confirm()
    {
        if ($this->alreadyConfirmed) {
            return;
        }

        $this->postcard->update([
            'tanggal_terima' => now()->toDateString(),
            'notif_read' => 0,
            'pesan_penerima' => $this->message,
        ]);

        $this->alreadyConfirmed = true;
        $this->justConfirmed = true;

        // Send Email - Using beautiful HTML template
        try {
            $ownerName = config('app.owner_name', 'Owner');
            $ownerEmail = config('app.owner_email', 'lintangmaulanazulfan@gmail.com');
            $fromAddress = config('mail.from.address', 'noreply@postcrossing.mlintangmz.my.id');
            $fromName = config('mail.from.name', 'Postcard Tracker');

            $subject = '📬 Postcard Arrived! ['.($this->postcard->country?->nama_indonesia ?? 'Unknown').']';
            $senderMessage = $this->message;

            Mail::send('emails.postcard-arrived', [
                'ownerName' => $ownerName,
                'postcard' => $this->postcard,
                'senderMessage' => $senderMessage
            ], function ($message) use ($ownerEmail, $fromAddress, $fromName, $subject) {
                $message->to($ownerEmail)
                    ->from($fromAddress, $fromName)
                    ->subject($subject);
            });
        } catch (\Exception $e) {
            // Silently fail or handle error without telemetry
        }
    }

    public function render()
    {
        return view('livewire.receive-confirm')
            ->layout('components.layouts.app', ['title' => 'Postcard Arrival Registry']);
    }
}
