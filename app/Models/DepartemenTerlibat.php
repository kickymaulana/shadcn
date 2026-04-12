<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

#[Table('departemen_terlibat')]
#[Fillable([
    'formulir_id',
    'departemen_id',
    'tanggal_diterima',
    'diterima_oleh',
    'tanggal_selesai',
    'qty',
    'paraf_qc',
    'paraf_spv',
    'data_tambahan',
    'item_pemeriksaan'
])]
class DepartemenTerlibat extends Model
{
    /**
     * Casting untuk kolom JSON agar otomatis menjadi array/object PHP
     */
    protected function casts(): array
    {
        return [
            'data_tambahan' => 'array',
            'item_pemeriksaan' => 'array',
            'tanggal_diterima' => 'datetime',
            'tanggal_selesai' => 'datetime',
        ];
    }

    public function formulir(): BelongsTo
    {
        return $this->belongsTo(Formulir::class);
    }

    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function paraf_qc()
    {
        return $this->belongsTo(User::class, 'paraf_qc');
    }

    public function parafQcUser()
    {
        return $this->belongsTo(User::class, 'paraf_qc');
    }
    public function paraf_spv()
    {
        return $this->belongsTo(User::class, 'paraf_spv');
    }
    public function parafSpvUser()
    {
        return $this->belongsTo(User::class, 'paraf_spv');
    }


    public function sampel()
    {
        // Jika di tabel departemen_terlibat tidak ada sampel_id,
        // kita gunakan relasi melalui Formulir
        return $this->hasOneThrough(
            Sampel::class,
            Formulir::class,
            'id',           // Foreign key di tabel formulir (id formulir)
            'id',           // Foreign key di tabel sampel (id sampel)
            'formulir_id',  // Local key di tabel departemen_terlibat
            'sampel_id'     // Local key di tabel formulir
        );
    }

}
