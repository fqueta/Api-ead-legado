<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turma extends Model
{
    protected $table = 'turmas';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'id_curso', 'nome', 'inicio', 'fim',
        'max_alunos', 'ativo', 'data_inicio', 'ordenar',
    ];

    protected $casts = [
        'inicio' => 'datetime',
        'fim' => 'datetime',
        'data_inicio' => 'datetime',
    ];

    protected $hidden = [
        'excluido', 'deletado', 'reg_excluido', 'reg_deletado',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'id_turma');
    }
}
