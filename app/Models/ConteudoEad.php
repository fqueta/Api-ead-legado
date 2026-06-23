<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConteudoEad extends Model
{
    protected $table = 'conteudo_ead';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'nome', 'nome_exibicao', 'tipo', 'descricao',
        'duracao', 'unidade_duracao', 'url', 'video',
        'tipo_link_video', 'config', 'gratis', 'ativo',
        'id_curso', 'token_modulo', 'token_curso',
        'token_prova', 'token', 'ordenar', 'autor',
        'start', 'end',
    ];

    protected $casts = [
        'config' => 'array',
    ];

    protected $hidden = [
        'excluido', 'deletado', 'reg_excluido', 'reg_deletado',
    ];
}
