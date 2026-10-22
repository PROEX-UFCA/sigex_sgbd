<?php

namespace App\Models\Sigex;

use Illuminate\Database\Eloquent\Model;

class Localidade extends Model
{
    protected $table = 'sigex_localidade';

    protected $fillable = ['id_projeto', 'cidade', 'estado'];
}
