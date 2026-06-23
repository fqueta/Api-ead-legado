<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class Cliente extends Model
{
    use HasApiTokens;

    protected $table = 'clientes';

    const CREATED_AT = 'data';
    const UPDATED_AT = 'atualizado';

    protected $fillable = [
        'Nome', 'sobrenome', 'Email', 'email', 'Cpf', 'cpf',
        'token', 'Celular', 'Telefone', 'Tel', 'telefonezap',
        'Endereco', 'Numero', 'Bairro', 'Cidade', 'Uf', 'Cep', 'Compl',
        'Ident', 'DtNasc2', 'estado_civil', 'profissao',
        'id_asaas', 'config', 'senha', 'canac',
        'nacionalidade', 'permissao', 'origem', 'ativo',
    ];

    protected $casts = [
        'config' => 'array',
        'DtNasc2' => 'date',
    ];

    protected $hidden = [
        'senha', 'excluido', 'deletado', 'reg_excluido', 'reg_deletado',
    ];

    public function matriculas(): HasMany
    {
        return $this->hasMany(Matricula::class, 'id_cliente');
    }
}
