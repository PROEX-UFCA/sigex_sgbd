<?php

namespace App\Models\Sigex;

use Illuminate\Database\Eloquent\Model;

class Membro extends Model
{
    protected $table = 'sigex_membro';

    protected $fillable = ['id_projeto', 'id_pessoa', 'nome_ascii', 'sexo', 'email', 'tipo_membro', 'categoria_membro', 'status', 'data_inicio', 'data_fim'];
}
