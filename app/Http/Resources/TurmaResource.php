<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TurmaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'id_curso' => $this->id_curso,
            'curso' => new CursoResource($this->whenLoaded('curso')),
            'nome' => $this->nome,
            'inicio' => $this->inicio,
            'fim' => $this->fim,
            'data_inicio' => $this->data_inicio,
            'max_alunos' => (int) $this->max_alunos,
            'ativo' => $this->ativo,
            'created_at' => $this->data,
            'updated_at' => $this->atualizado,
        ];
    }
}
