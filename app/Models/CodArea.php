<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodArea extends Model
{
    protected $table = 'cod_areas';

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }
}
