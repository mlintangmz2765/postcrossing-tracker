<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\RegisterPostcard;
use App\Livewire\Home;
use App\Livewire\PublicGallery;
use Illuminate\Support\Facades\Auth;

Route::get('/', Home::class)->name('home');
Route::get('/gallery', PublicGallery::class)->name('gallery');
Route::get('/new', RegisterPostcard::class)->name('postcard.register')->middleware('auth');
Route::get('/receive/{uid}', App\Livewire\ReceiveConfirm::class)->name('receive.confirm');
Route::get('/login', App\Livewire\Login::class)->name('login');
Route::get('/dashboard', App\Livewire\Dashboard::class)->name('dashboard')->middleware('auth');
Route::get('/import', App\Livewire\ImportPostcards::class)->name('postcard.import')->middleware('auth');
Route::get('/postcard-gallery', App\Livewire\PostcardGallery::class)->name('postcard.gallery')->middleware('auth');
Route::get('/stamps', App\Livewire\StampGallery::class)->name('stamps')->middleware('auth');
Route::get('/stats', App\Livewire\Statistics::class)->name('stats')->middleware('auth');
Route::get('/view/{id}', App\Livewire\PostcardView::class)->name('view')->middleware('auth');
Route::get('/edit/{id}', App\Livewire\EditPostcard::class)->name('edit')->middleware('auth');

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');
