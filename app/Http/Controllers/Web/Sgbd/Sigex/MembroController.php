<?php

namespace App\Http\Controllers\Web\Sgbd\Sigex;

use App\Http\Controllers\Controller;
use App\Models\Sigex\Membro;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MembroController extends Controller
{
    public function sincronizar()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $caminhoArquivo = database_path('sql/membro.sql');

            if (!File::exists($caminhoArquivo)) {
                throw new \RuntimeException('O arquivo de query não foi encontrado em database/sql/membro.sql');
            }

            $sqlQuery = File::get($caminhoArquivo);

            $registrosExternos = DB::connection('external_db')->cursor($sqlQuery);
            $totalProcessados = 0;

            foreach ($registrosExternos as $reg) {
                
                Membro::updateOrCreate(
                    [
                        'id_projeto' => $reg->id_projeto, 
                        'id_pessoa' => $reg->id_pessoa, 
                    ],
                    [
                        'nome_ascii' => $reg->nome_ascii, 
                        'sexo' => $reg->sexo, 
                        'email' => $reg->email, 
                        'tipo_membro' => $reg->tipo_membro, 
                        'categoria_membro' => $reg->categoria_membro, 
                        'status' => $reg->status, 
                        'data_inicio' => $reg->data_inicio, 
                        'data_fim' => $reg->data_fim
                    ]
                );

                $totalProcessados++;
            }

            return $totalProcessados;

        } catch (Exception $e) {
            throw new \RuntimeException('Erro ao sincronizar: '. $e->getMessage());
        }
    }
}
