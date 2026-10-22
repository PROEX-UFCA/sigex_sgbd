<?php

namespace App\Http\Controllers\Web\Sgbd\Sigex;

use App\Http\Controllers\Controller;
use App\Models\Sigex\Localidade;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LocalidadeController extends Controller
{
    public function sincronizar()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $caminhoArquivo = database_path('sql/localidade.sql');

            if (!File::exists($caminhoArquivo)) {
                throw new \RuntimeException('O arquivo de query não foi encontrado em database/sql/localidade.sql');
            }

            $sqlQuery = File::get($caminhoArquivo);

            $registrosExternos = DB::connection('external_db')->cursor($sqlQuery);
            $totalProcessados = 0;
            
            foreach ($registrosExternos as $reg) {
                
                Localidade::updateOrCreate(
                    [
                        'id_projeto' => $reg->id_projeto ?? null, 
                        'cidade' => $reg->nome ?? null
                    ],
                    [
                        'estado' => $reg->estado ?? null,
                        'sim' => $reg->ano ?? null,
                        'area_tematica' => $reg->ano ?? null,
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
