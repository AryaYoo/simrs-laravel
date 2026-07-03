<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ModalLookupDokter extends Component
{
    public $eventTarget = 'selectDokter';
    public $search = '';
    public $isOpen = false;
    public $modalId = 'modal-dokter-usg';

    #[On('open-modal')]
    public function handleOpenModal($data = [])
    {
        $id = $data['id'] ?? '';
        if ($id === $this->modalId) {
            $this->isOpen = true;
            $this->search = '';
        }
    }

    #[On('close-modal')]
    public function handleCloseModal()
    {
        $this->isOpen = false;
    }

    public function select($kd_dokter, $nm_dokter)
    {
        $this->dispatch($this->eventTarget, [
            'kd_dokter' => $kd_dokter,
            'nm_dokter' => $nm_dokter
        ]);
        $this->isOpen = false;
    }

    public function render()
    {
        $doctors = [];
        if ($this->isOpen) {
            $doctors = DB::table('dokter')
                ->where('status', '1')
                ->where(function ($query) {
                    $query->where('kd_dokter', 'like', '%' . $this->search . '%')
                        ->orWhere('nm_dokter', 'like', '%' . $this->search . '%');
                })
                ->orderBy('nm_dokter', 'asc')
                ->limit(50)
                ->get();
        }

        return view('livewire.shared.modal-lookup-dokter', [
            'doctors' => $doctors
        ]);
    }
}
