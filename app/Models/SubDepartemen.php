<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Table('sub_departemen')]
#[Fillable(['departemen_id', 'nama', 'urutan'])]
class SubDepartemen extends Model
{
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class, 'departemen_id');
    }
}
