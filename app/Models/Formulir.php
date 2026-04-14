<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('formulirs')]
#[Fillable([
    'sampel_id',
    'size',
    'qty_sampel_kirim',
    'running_ke',
    'tanggal_permintaan',
    'status',
    'diperiksa_oleh',
    'disetujui_oleh'
])]
class Formulir extends Model
{

    public function pemeriksa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diperiksa_oleh');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function sampel()
    {
        return $this->belongsTo(Sample::class, 'sampel_id', 'id');
    }

    public function departemen_terlibat()
    {
        return $this->hasMany(DepartemenTerlibat::class, 'formulir_id', 'id');
    }

}
