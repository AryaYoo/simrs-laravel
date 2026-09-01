<?php

namespace App\Livewire\Modul\RawatInap\KelahiranBayi;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'SOP & Panduan Kelahiran Bayi - SIMRS LaraLite'])]
class Sop extends Component
{
    public string $activeTab = 'tentang'; // 'tentang', 'sop_input', 'sop_skl', 'sop_koreksi', 'faq'

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.modul.rawat-inap.kelahiran-bayi.sop');
    }
}
