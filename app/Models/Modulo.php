<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    protected $table = 'modulos_ead';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'nome', 'nome_exibicao', 'descricao', 'url',
        'professor', 'token', 'conteudo', 'config',
        'token_curso', 'ativo', 'ordenar', 'autor',
    ];

    protected $casts = [
        'conteudo' => 'array',
        'config' => 'array',
    ];

    protected $hidden = [
        'excluido', 'deletado', 'reg_excluido', 'reg_deletado',
    ];
}
