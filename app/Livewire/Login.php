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

    public function mount()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
    }

    protected $rules = [
        'username' => 'required',
        'password' => 'required',
    ];

    public function authenticate()
    {
        $this->validate();

        if (! $this->recaptchaToken) {
            $this->error = 'Please complete the reCAPTCHA verification.';

            return;
        }

        // Authenticate user
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password])) {
            session()->regenerate();

            return redirect()->route('dashboard'); // Redirect to dashboard logic (index)
        }

        $this->error = 'Access denied. Please check your credentials.';
    }

    public function render()
    {
        return view('livewire.login')
            ->layout('components.layouts.app', ['title' => 'Manager Access - Private']);
    }
}
