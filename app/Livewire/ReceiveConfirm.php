<?php

namespace App\Livewire;

use App\Models\Postcard;
use App\Traits\CalculatesDistance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;

class ReceiveConfirm extends Component
{
    use CalculatesDistance;

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

    // Map markers. This page is public, so the sender's marker uses rounded
    // coordinates (about 11 km); the exact origin never reaches the browser.
    public $myLat;

    public $myLng;

    public $originName;

    public $targetLat = 0;

    public $targetLng = 0;

    public function mount($uid)
    {
        $this->uid = $uid;
        $this->postcard = Postcard::with(['contact', 'country', 'origin'])->where('uid', $uid)->where('type', 'sent')->firstOrFail();

        $origin = $this->postcard->ownerPlace();
        ['lat' => $this->myLat, 'lng' => $this->myLng] = $origin->coarse();
        // Home is shown as OWNER_LOCATION (hidden when unset); a travel origin by its name
        $this->originName = $origin->isHome ? config('app.owner_location') : $origin->name;

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

        // Distance uses the exact origin, server-side
        $this->distance = $this->postcard->distanceKm() ?? 0;
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

    // calculateDistance() provided by CalculatesDistance trait

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

        $this->notifyOwner();
    }

    /**
     * Email the owner that a card arrived. Recipient and sender come only
     * from OWNER_EMAIL and MAIL_FROM_*; when either is missing the email is
     * skipped with a warning instead of going to a hardcoded address.
     */
    protected function notifyOwner(): void
    {
        $ownerEmail = config('app.owner_email');
        $context = ['postcard_id' => $this->postcard->id];

        if (blank($ownerEmail)) {
            Log::warning('Arrival email not sent: OWNER_EMAIL is not set.', $context);

            return;
        }

        if (blank(config('mail.from.address'))) {
            Log::warning('Arrival email not sent: MAIL_FROM_ADDRESS is not set.', $context);

            return;
        }

        $subject = '📬 Postcard Arrived! ['.($this->postcard->country?->display_name ?? 'Unknown').']';

        try {
            // The sender is the mailer's global "from" (MAIL_FROM_ADDRESS / MAIL_FROM_NAME).
            Mail::send('emails.postcard-arrived', [
                'ownerName' => config('app.owner_name'),
                'postcard' => $this->postcard,
                'senderMessage' => $this->message,
            ], function ($message) use ($ownerEmail, $subject) {
                $message->to($ownerEmail)->subject($subject);
            });
        } catch (\Throwable $e) {
            // The recipient's confirmation is already saved; don't fail their request.
            Log::error('Arrival email failed: '.$e->getMessage(), $context);
        }
    }

    public function render()
    {
        return view('livewire.receive-confirm')
            ->layout('components.layouts.app', ['title' => 'Postcard Arrival Registry']);
    }
}
