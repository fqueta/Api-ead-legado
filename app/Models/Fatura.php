<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fatura extends Model
{
    protected $table = 'lcf_entradas';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'id_cliente', 'ref_compra', 'categoria', 'local',
        'vencimento', 'valor', 'pago', 'data_pagamento',
        'descricao', 'conta', 'reg_asaas', 'tipo',
    ];

    protected $casts = [
        'valor' => 'float',
        'reg_asaas' => 'array',
    ];

    public function matricula(): BelongsTo
    {
        return $this->belongsTo(Matricula::class, 'ref_compra', 'token');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}
