<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;

#[Table('departemen')]
#[Fillable(['nama'])]
class Departemen extends Model
{
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subDepartemens(): HasMany
    {
        return $this->hasMany(SubDepartemen::class, 'departemen_id', 'id');
    }
}
