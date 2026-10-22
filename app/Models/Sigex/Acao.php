<?php

namespace App\Models\Sigex;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acao extends Model
{
    protected $table = 'sigex_acao';

    protected $fillable = ['id_projeto', 'ano', 'titulo', 'modalidade_edital', 'bolsas_solicitadas', 'bolsas_concedidas', 'financiamento_interno', 'financiamento_externo', 'situacao', 'data_cadastro', 'data_inicio', 'data_fim', 'data_atualizacao', 'centro_departamento_sigla', 'tipo_acao', 'area_tematica', 'resumo', 'palavras_chave', 'ods', 'contexto'];
}
