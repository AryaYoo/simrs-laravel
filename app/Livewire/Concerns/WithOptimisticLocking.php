<?php

namespace App\Livewire\Concerns;

use Illuminate\Database\Eloquent\Model;

trait WithOptimisticLocking
{
    /**
     * Stores the unique signature of the record state when it was first loaded.
     */
    public string $initialRecordHash = '';

    /**
     * Initializes the lock by hashing the current model's attributes.
     * Call this in your mount() method after fetching the model.
     */
    public function initializeLock(Model $model): void
    {
        $this->initialRecordHash = $this->generateModelHash($model);
    }

    /**
     * Validates that the record hasn't changed in the database since it was loaded.
     * Call this inside your save() method before committing any updates.
     * 
     * @throws \Exception If a data conflict is detected.
     */
    public function validateLock(Model $model): void
    {
        // We expect the caller to pass a "fresh" instance from the database.
        // The trait will then compare its current state in DB with the state when it was loaded.
        
        if (!$model->exists) {
            throw new \Exception("Daftar data tidak ditemukan atau sudah dihapus oleh pengguna lain.");
        }

        $currentHash = $this->generateModelHash($model);

        if ($currentHash !== $this->initialRecordHash) {
            $this->dispatch('swal', [
                'title' => 'Konflik Data!',
                'text'  => 'Data ini telah diubah oleh orang lain saat Anda sedang mengedit. Silakan muat ulang halaman untuk mendapatkan data terbaru.',
                'icon'  => 'warning',
                'confirmButtonText' => 'Muat Ulang Sekarang',
                'showCancelButton' => true,
            ]);

            throw new \Exception("CONCURRENCY_ERROR: Data has been modified by another user.");
        }
    }

    /**
     * Generates a unique MD5 hash of the model's attributes.
     */
    protected function generateModelHash(Model $model): string
    {
        // We only hash the attributes to ignore loaded relations or dynamic properties
        return md5(json_encode($model->getAttributes()));
    }
}
