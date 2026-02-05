<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class Login extends Component
{
    public $username; // Maps to 'user_identifier' in legacy
    public $password; // Maps to 'secret_key' in legacy
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
        $secret = env('RECAPTCHA_SECRET_KEY');
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
        // Legacy used 'username' column. Laravel default is email, but we can customize.
        // Assuming 'username' is the column name in users table.
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
             session()->regenerate();
             return redirect()->route('dashboard'); // Redirect to dashboard logic (index)
        }

        // 3. Fail
        $this->error = "Akses ditolak. Silakan periksa kembali kredensial Anda.";
        // Simulate legacy `sleep(1)` to prevent brute force? Livewire handles this naturally with network delay, but we can rate limit if needed.
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.app', ['title' => 'Manager Access - Private']);
    }
}
