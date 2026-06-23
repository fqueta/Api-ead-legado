<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->Nome,
            'sobrenome' => $this->sobrenome,
            'email' => $this->Email ?? $this->email,
            'cpf' => $this->Cpf ?? $this->cpf,
            'token' => $this->token,
            'celular' => $this->Celular,
            'telefone' => $this->Tel ?? $this->Telefone,
            'endereco' => $this->Endereco,
            'numero' => $this->Numero,
            'bairro' => $this->Bairro,
            'cidade' => $this->Cidade,
            'uf' => $this->Uf,
            'cep' => $this->Cep,
            'complemento' => $this->Compl,
            'identidade' => $this->Ident,
            'data_nascimento' => $this->DtNasc2,
            'estado_civil' => $this->estado_civil,
            'profissao' => $this->profissao,
            'id_asaas' => $this->id_asaas,
            'canac' => $this->canac,
            'nacionalidade' => $this->nacionalidade,
            'config' => $this->config,
            'ativo' => $this->ativo,
            'created_at' => $this->data,
            'updated_at' => $this->atualizado,
        ];
    }
}
