<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ApiService
{
    private string $token;

    public function autenticar(): void
    {
        $response = Http::post(
            config('services.sigex.url') . '/login',
            [
                'email' => config('services.sigex.email'),
                'password' => config('services.sigex.password'),
            ]
        );

        if ($response->unauthorized()) {
            throw new Exception('Credenciais inválidas na API.');
        }

        if (!$response->successful()) {
            throw new Exception(
                'Erro ao autenticar: ' . $response->body()
            );
        }

        $this->token = $response->json('token');
    }

    public function logout(): void
    {
        $response = Http::withToken($this->token)
            ->post(config('services.sigex.url') . '/logout');

        if (!$response->successful()) {
            throw new \Exception(
                'Erro ao realizar logout: ' . $response->body()
            );
        }
    }

    private function request(string $rota)
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->get(config('services.sigex.url') . $rota);

        if (!$response->successful()) {
            throw new Exception(
                "Erro ao consultar {$rota}: " . $response->body()
            );
        }

        return $response->json();
    }

    private function post(string $rota, $dados)
    {
        $response = Http::withToken($this->token)
            ->acceptJson()
            ->post(config('services.sigex.url') . $rota, $dados);

        if (!$response->successful()) {
            throw new Exception(
                "Erro ao consultar {$rota}: " . $response->body()
            );
        }

        return $response->json();
    }

    public function postAcoes($dados)
    {
        return $this->post('/sincronizar/acoes', $dados);
    }

    public function postLocalidades($dados)
    {
        return $this->post('/sincronizar/locais', $dados);
    }

    public function postMembros($dados)
    {
        return $this->post('/sincronizar/membros', $dados);
    }
}