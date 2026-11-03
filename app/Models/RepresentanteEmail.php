<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepresentanteEmail extends Model
{
    protected $table = 'representante_emails';
    protected $fillable = ['representante_id','email','principal'];

    public function representante()
    {
        return $this->belongsTo(Representante::class);
    }
}
