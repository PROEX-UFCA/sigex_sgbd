<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sigex_membro', function (Blueprint $table) {
            $table->id();
            $table->string('id_projeto')->nullable();
            $table->string('id_pessoa')->nullable();
            $table->string('nome_ascii')->nullable();
            $table->string('sexo')->nullable();
            $table->string('email')->nullable();
            $table->string('tipo_membro')->nullable();
            $table->string('categoria_membro')->nullable();
            $table->string('status')->nullable();
            $table->date('data_inicio')->nullable();
            $table->date('data_fim')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sigex_membro');
    }
};
