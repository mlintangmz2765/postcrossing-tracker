<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Postcard;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $this->myLat = (float) env('HOME_LAT', 0);
        $this->myLng = (float) env('HOME_LNG', 0);
        $this->uid = $uid;
        $this->postcard = Postcard::where('uid', $uid)->where('type', 'sent')->firstOrFail();
        
        $this->alreadyConfirmed = !empty($this->postcard->tanggal_terima) && $this->postcard->tanggal_terima != '0000-00-00';
        $this->isSwap = !Str::contains($this->postcard->postcard_id, '-');

        // Detect China Viewer
        $this->detectChinaViewer();

        // China keyword check for destination (useful for map style)
        $isToChina = false;
        $chinaKeywords = ['china', 'tiongkok', 'prc', 'people\'s republic of china'];
        foreach ($chinaKeywords as $kw) {
            if (stripos($this->postcard->negara, $kw) !== false || stripos($this->postcard->alamat, $kw) !== false) {
                $isToChina = true;
                break;
            }
        }
        
        // If it's TO China, we use AMap even if the viewer isn't in China (for better local detail)
        if ($isToChina) {
            $this->isChina = true;
        }

        // Coordinates
        $this->targetLat = (float) $this->postcard->lat;
        $this->targetLng = (float) $this->postcard->lng;
        
        if ($this->targetLat && $this->targetLng) {
            $this->distance = $this->calculateDistance($this->myLat, $this->myLng, $this->targetLat, $this->targetLng);
        }
    }

    public function detectChinaViewer()
    {
        if (request()->has('china')) {
            $val = request()->query('china') == '1';
            $this->isChina = $val;
            session()->put('is_cn_viewer', $val);
            return;
        }

        if (session()->has('is_cn_viewer')) {
            $this->isChina = session('is_cn_viewer');
            return;
        }

        $cfCountry = request()->header('CF-IPCountry');
        if ($cfCountry === 'CN') {
            $this->isChina = true;
            session()->put('is_cn_viewer', true);
            return;
        }

        $this->isChina = false;
        session()->put('is_cn_viewer', false);
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return round($miles * 1.609344);
    }

    public function confirm()
    {
        if ($this->alreadyConfirmed) return;

        $this->postcard->update([
            'tanggal_terima' => now()->toDateString(),
            'notif_read' => 0,
            'pesan_penerima' => $this->message,
        ]);

        $this->alreadyConfirmed = true;
        $this->justConfirmed = true;
        
        // Send Email
        try {
            // Simplified mail for now, can use Mailable class later
            $ownerName = config('app.owner_name', 'Owner');
            $ownerEmail = config('app.owner_email', config('mail.from.address'));
            
            Mail::raw("Hello {$ownerName},\n\nYour postcard has arrived safely!\n\nID: {$this->postcard->postcard_id}\nRecipient: {$this->postcard->nama_kontak}\n\nMessage: \"{$this->message}\"", function ($message) use ($ownerEmail) {
                $message->to($ownerEmail)
                        ->subject("📬 Postcard Arrived! [{$this->postcard->negara}]");
                        // from() uses MAIL_FROM_ADDRESS and MAIL_FROM_NAME from .env automatically
            });
        } catch (\Exception $e) {
            // Log error but don't fail user request
            \Log::error('Mail error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.receive-confirm')
            ->layout('components.layouts.app', ['title' => 'Postcard Arrival Registry']);
    }
}
