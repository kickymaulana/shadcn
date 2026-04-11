<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('samples')]
#[Fillable([
    'kode_sample',
    'diajukan_oleh',
    'kepada',
    'customer',
    'model',
    'spesifikasi'
])]
class Sample extends Model
{
    public function formulir()
    {
        return $this->hasMany(Formulir::class, 'sampel_id', 'id');
    }

}
