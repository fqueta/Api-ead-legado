<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curso extends Model
{
    protected $table = 'cursos';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'nome', 'titulo', 'url', 'categoria', 'tipo',
        'descricao', 'descricao_site', 'meta_descricao', 'meta_titulo', 'obs',
        'valor', 'inscricao', 'parcelas', 'valor_parcela',
        'duracao', 'unidade_duracao',
        'ativo', 'destaque', 'token', 'conteudo', 'config',
        'professor', 'autor', 'ordenar',
    ];

    protected $casts = [
        'conteudo' => 'array',
        'config' => 'array',
        'valor' => 'float',
        'inscricao' => 'float',
        'valor_parcela' => 'float',
    ];

    protected $hidden = [
        'excluido', 'deletado', 'reg_excluido', 'reg_deletado',
    ];

    public function turmas(): HasMany
    {
        return $this->hasMany(Turma::class, 'id_curso');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'id_curso');
    }
}
