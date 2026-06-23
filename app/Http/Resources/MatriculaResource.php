<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatriculaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_cliente' => $this->id_cliente,
            'id_curso' => $this->id_curso,
            'id_turma' => $this->id_turma,
            'cliente' => new ClienteResource($this->whenLoaded('cliente')),
            'curso' => new CursoResource($this->whenLoaded('curso')),
            'turma' => new TurmaResource($this->whenLoaded('turma')),
            'token' => $this->token,
            'status' => $this->status,
            'validade' => $this->validade,
            'data_inicio' => $this->data_inicio,
            'contrato' => $this->contrato,
            'pagamento_asaas' => $this->pagamento_asaas,
            'ativo' => $this->ativo,
            'numero_aluno' => $this->numero_aluno,
            'config' => $this->config,
            'tipo_curso' => $this->tipo_curso,
            'orc' => $this->orc,
            'created_at' => $this->data,
            'updated_at' => $this->atualizado,
        ];
    }
}
