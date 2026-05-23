<?php

namespace App\Repositories\RawatInap;

use App\Models\BookingOperasi;
use Illuminate\Support\Facades\DB;

class BookingOperasiRepository
{
    public function getByNoRawat($noRawat)
    {
        return BookingOperasi::with(['dokter', 'paketOperasi', 'ruangOk'])
            ->where('no_rawat', $noRawat)
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->get();
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            return BookingOperasi::create($data);
        });
    }

    public function update(array $keys, array $data)
    {
        return DB::transaction(function () use ($keys, $data) {
            $query = BookingOperasi::query();
            foreach ($keys as $key => $value) {
                $query->where($key, $value);
            }
            
            $record = $query->first();
            if ($record) {
                // If keys themselves are changed, we need to handle it.
                // In this UI, they probably can change the keys. If they do, an update on composite PK is tricky.
                // Standard approach: delete and recreate if keys changed, or just standard update since Eloquent can handle update if keys are not auto-increment.
                $record->update($data);
                return $record;
            }
            return null;
        });
    }

    public function delete(array $keys)
    {
        return DB::transaction(function () use ($keys) {
            $query = BookingOperasi::query();
            foreach ($keys as $key => $value) {
                $query->where($key, $value);
            }
            return $query->delete();
        });
    }

    public function exists(array $keys)
    {
        $query = BookingOperasi::query();
        foreach ($keys as $key => $value) {
            $query->where($key, $value);
        }
        return $query->exists();
    }
}
