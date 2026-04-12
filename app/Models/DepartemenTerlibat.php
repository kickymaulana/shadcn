<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('departemen_terlibat')]
#[Fillable([
    'formulir_id',
    'sub_departemen_id',
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
     * Casting untuk kolom date dan JSON.
     * Laravel 11/12/13 menggunakan method casts() untuk fleksibilitas lebih tinggi.
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

    /**
     * Relasi ke Formulir induk
     */
    public function formulir(): BelongsTo
    {
        return $this->belongsTo(Formulir::class);
    }

    /**
     * Relasi ke User yang menerima sample di departemen tersebut
     */
    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /**
     * Relasi ke User QC (Gunakan nama method berbeda dari kolom database)
     */
    public function qcUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paraf_qc');
    }

    /**
     * Relasi ke User SPV (Gunakan nama method berbeda dari kolom database)
     */
    public function spvUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paraf_spv');
    }

    /**
     * Relasi ke Master Sub Departemen
     */
    public function sub_departemen(): BelongsTo
    {
        return $this->belongsTo(SubDepartemen::class, 'sub_departemen_id');
    }

    /**
     * Shortcut untuk mengambil data Sampel melalui Formulir
     */
    public function sampel(): HasOneThrough
    {
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
