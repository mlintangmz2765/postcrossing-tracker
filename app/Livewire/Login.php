<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

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

        // Verify ReCaptcha
        // Authenticate user
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session()->regenerate();

            return redirect()->route('dashboard'); // Redirect to dashboard logic (index)
        }

        $this->error = 'Akses ditolak. Silakan periksa kembali kredensial Anda.';
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.app', ['title' => 'Manager Access - Private']);
    }
}
