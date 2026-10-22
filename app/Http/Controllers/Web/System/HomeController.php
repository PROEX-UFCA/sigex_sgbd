<?php

namespace App\Http\Controllers\Web\System;

use App\Http\Controllers\Controller;
use App\Models\Sincronizacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    private $data = [];

    public function index()
    {

        $this->data['sincronizacoes'] = Sincronizacao::latest('id')->limit(20)->get();

        return view('pages.home.index', $this->data);
    }
}
