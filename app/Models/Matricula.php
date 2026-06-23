<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Matricula extends Model
{
    use HasFactory;

    protected $table = 'matriculas';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'id_cliente', 'id_curso', 'id_turma', 'token',
        'status', 'validade', 'data_inicio', 'contrato',
        'pagamento_asaas', 'ativo', 'numero_aluno', 'config',
        'tipo_curso', 'contrato_financiamento_horas',
        'orc', 'memo', 'route', 'icon', 'actived',
    ];

    protected $casts = [
        'orc' => 'array',
        'config' => 'array',
        'data_inicio' => 'datetime',
        'validade' => 'datetime',
    ];

    protected $hidden = [
        'excluido', 'deletado', 'reg_excluido', 'reg_deletado',
    ];

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class, 'id_curso');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function turma(): BelongsTo
    {
        return $this->belongsTo(Turma::class, 'id_turma');
    }
}
