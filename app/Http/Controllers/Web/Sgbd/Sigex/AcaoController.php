<?php

namespace App\Http\Controllers\Web\Sgbd\Sigex;

use App\Http\Controllers\Controller;
use App\Models\Sigex\Acao;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;

class AcaoController extends Controller
{
    public function sincronizar()
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        try {
            $caminhoArquivo = database_path('sql/acao.sql');

            if (!File::exists($caminhoArquivo)) {
                throw new \RuntimeException('Arquivo database/sql/acao.sql não encontrado.');
            }

            $sqlQuery = File::get($caminhoArquivo);

            $registrosExternos = DB::connection('external_db')->cursor($sqlQuery);
            $totalProcessados = 0;

            foreach ($registrosExternos as $reg) {
                Acao::updateOrCreate(
                    ['id_projeto' => $reg->id_projeto],
                    [
                        'ano' => $reg->ano ?? null,
                        'titulo' => $reg->titulo ?? null,
                        'modalidade_edital' => $reg->edital ?? null,
                        'bolsas_solicitadas' => $reg->bolsas_solicitadas ?? 0,
                        'bolsas_concedidas' => $reg->bolsas_concedidas ?? 0,
                        'financiamento_interno' => $reg->financiamento_interno ?? null,
                        'financiamento_externo' => $reg->financiamento_externo ?? null,
                        'situacao' => $reg->situacao ?? null,
                        'data_cadastro' => $reg->data_cadastro ?? null,
                        'data_inicio' => $reg->data_inicio ?? null,
                        'data_fim' => $reg->data_fim ?? null,
                        'data_atualizacao' => $reg->data_atualizacao ?? null,
                        'centro_departamento_sigla' => $reg->sigla ?? null,
                        'tipo_acao' => $reg->tipo_acao ?? null,
                        'area_tematica' => $reg->area_tematica ?? null,
                        'resumo' => $reg->resumo ?? null,
                        'palavras_chave' => $reg->palavras_chave ?? null,
                        'ods' => $reg->ods ?? null,
                        'contexto' => $reg->contexto ?? null,
                    ]
                );

                $totalProcessados++;
            }

            return $totalProcessados;

        } catch (Exception $e) {
            throw new \RuntimeException('Erro ao sincronizar: '. $e->getMessage());
        }
    }

    public function testarConexaoExterna()
    {
        set_time_limit(0);

        try {
            // 1. Testa se consegue abrir a conexão PDO com o banco externo
            DB::connection('external_db')->getPdo();
            $statusConexao = "Conexão com o banco externo estabelecida com sucesso!";
        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'erro' => 'Falha ao conectar no banco externo: ' . $e->getMessage()
            ], 500);
        }

        try {
            // 2. Verifica se o arquivo SQL existe
            $caminhoArquivo = database_path('sql/acao.sql');

            if (!File::exists($caminhoArquivo)) {
                return response()->json([
                    'sucesso' => false,
                    'erro' => 'O arquivo de query não foi encontrado em database/sql/acao.sql'
                ], 404);
            }

            // 3. Lê e executa a query no banco externo
            $sqlQuery = File::get($caminhoArquivo);
            $registros = DB::connection('external_db')->select($sqlQuery);

            $totalEncontrado = count($registros);
            
            // Pega o primeiro registro como amostra para você inspecionar as colunas
            $amostra = $totalEncontrado > 0 ? $registros[0] : null;

            return response()->json([
                'sucesso' => true,
                'mensagem' => $statusConexao,
                'total_registros_retornados' => $totalEncontrado,
                'amostra_primeiro_registro' => $amostra
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => $statusConexao,
                'erro_na_query' => $e->getMessage()
            ], 500);
        }
    }
}