<?php

namespace App\Http\Controllers\Web\Sgbd;

use App\Http\Controllers\Controller;
use App\Jobs\Sgbd\ExecutarSincronizacaoJob;
use App\Models\Sigex\Acao;
use App\Models\Sigex\Localidade;
use App\Models\Sigex\Membro;
use App\Models\Sincronizacao;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    private $api;

    public function __construct(ApiService $api)
    {
        $this->api = $api;
    }

    public function iniciarSigex(){
        try {
            
            $this->api->autenticar();
    
            $acoes = Acao::get();
            $locais = Localidade::get();
            $membros = Membro::get();
    
            $resultadoAcoes = $this->api->postAcoes($acoes);
            $resultadoLocais = $this->api->postLocalidades($locais);
            $resultadoMembros = $this->api->postMembros($membros);
    
            $this->api->logout();
    
            return redirect()->back()->with('success', "Sincronização concluída");

        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }
}
