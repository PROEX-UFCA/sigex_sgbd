<?php

namespace App\Http\Controllers\Api\Sigex;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DadosController extends Controller
{
    public function acoes()
    {
        return response()->json(
            DB::table('sigex_acao')->get()
        );
    }

    public function localidades()
    {
        return response()->json(
            DB::table('sigex_localidade')->get()
        );
    }

    public function membros()
    {
        return response()->json(
            DB::table('sigex_membro')->get()
        );
    }
}