<?php

namespace App\Livewire\Modul\RegistrasiPasien;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Tambah Pasien Baru'])]
class NewPatient extends Component
{
    public function render()
    {
        return view('livewire.modul.registrasi-pasien.new');
    }
}
