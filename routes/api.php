<?php

use App\Http\Controllers\Api\Sigex\DadosController;
use App\Http\Controllers\Api\System\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/acoes', [DadosController::class, 'acoes']);
    Route::get('/localidades', [DadosController::class, 'localidades']);
    Route::get('/membros', [DadosController::class, 'membros']);
});
