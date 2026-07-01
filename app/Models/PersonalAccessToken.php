<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Modelo customizado de Personal Access Token para multi-tenancy.
 *
 * O Sanctum por padrão usa a connection padrão do banco (central).
 * Como os tokens são criados e armazenados no banco do tenant,
 * precisamos forçar a connection 'tenant' para que o Sanctum
 * consiga encontrar e validar os tokens corretamente.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'tenant';
}
