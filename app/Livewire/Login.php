<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class Login extends Component
{
    public $username;
    public $password;
    public $recaptchaToken;
    public $error = '';

    protected $rules = [
        'username' => 'required',
        'password' => 'required',
    ];

    public function authenticate()
    {
        $this->validate();

        // 1. Verify ReCaptcha
        $secret = config('app.recaptcha_secret_key');
        if(!$secret) {
             $this->error = "Configuration Error: ReCaptcha Secret missing.";
             return;
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $this->recaptchaToken,
        ]);

        if (!$response->json('success')) {
            $this->error = "Verifikasi Captcha Gagal! Mohon centang kotak saya bukan robot.";
            return;
        }

        // 2. Auth Attempt
        // Authenticate user
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
             session()->regenerate();
             return redirect()->route('dashboard'); // Redirect to dashboard logic (index)
        }

        // 3. Fail
        $this->error = "Akses ditolak. Silakan periksa kembali kredensial Anda.";
        // Rate limiting handled by system
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.app', ['title' => 'Manager Access - Private']);
    }
}
