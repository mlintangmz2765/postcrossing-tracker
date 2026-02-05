<?php
 
 namespace App\Livewire;
 
 use Livewire\Component;
 
 class Home extends Component
 {
     public $isChina = false;
 
     public function mount()
     {
         $this->detectChinaViewer();
     }
 
     public function detectChinaViewer()
     {
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
 
     public function render()
     {
         return view('livewire.home', [
             'isChina' => $this->isChina
         ])->layout('components.layouts.app', ['title' => 'Postcard Journey - Home']);
     }
 }
