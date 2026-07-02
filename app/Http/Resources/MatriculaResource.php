<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatriculaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Extract financial data from orc → reg_pagamento/reg_inscricao → curso
        $orc = is_array($this->orc) ? $this->orc : [];
        $regPagamento = is_array($this->reg_pagamento) ? $this->reg_pagamento : [];
        $regInscricao = is_array($this->reg_inscricao) ? $this->reg_inscricao : [];
        $curso = $this->curso;

        $desconto = isset($orc['desconto']) ? (string) $orc['desconto']
            : (isset($regInscricao['desconto']) ? (string) $regInscricao['desconto'] : '0.00');

        $inscricao = isset($orc['inscricao']) ? (string) $orc['inscricao']
            : (isset($regInscricao['valor']) ? (string) $regInscricao['valor']
            : ($curso ? (string) $curso->inscricao : '0.00'));

        $subtotal = isset($orc['subtotal']) ? (string) $orc['subtotal']
            : (isset($regPagamento['valor']) ? (string) $regPagamento['valor']
            : ($curso ? (string) $curso->valor : '0.00'));

        $total = isset($orc['total']) ? (string) $orc['total']
            : (isset($regPagamento['valor']) ? (string) $regPagamento['valor']
            : ($curso ? (string) $curso->valor : '0.00'));
        $gera_valor = isset($orc['meta']['gera_valor']) ?? '';
        $parcelada = isset($orc['meta']['parcelada']) ? (bool) $orc['meta']['parcelada'] : false;
        $parcelas = isset($orc['meta']['parcelas']) ? (string) $orc['meta']['parcelas'] : '12';
        $texto_desconto = isset($orc['meta']['texto_desconto']) ?? '';
        $validade_meta = isset($orc['meta']['validade']) ? (string) $orc['meta']['validade'] : '';
        
        // Extract additional data from config array
        $config = is_array($this->config) ? $this->config : [];
        $id_consultor = isset($config['id_consultor']) ? (string) $config['id_consultor'] : '';
        $id_responsavel = isset($config['id_responsavel']) ? (string) $config['id_responsavel'] : '';
        $situacao_id = isset($config['situacao_id']) ? (string) $config['situacao_id'] : '';

        return [
            'ativo' => $this->ativo,
            'desconto' => $desconto,
            'id' => $this->id,
            'id_cliente' => $this->id_cliente,
            'id_consultor' => $id_consultor,
            'id_curso' => $this->id_curso,
            'id_responsavel' => $id_responsavel,
            'id_turma' => $this->id_turma ?: '0', // Ensure we return '0' for null/empty
            'inscricao' => $inscricao,
            'meta' => [
                'gera_valor' => $gera_valor,
                'parcelada' => $parcelada,
                'parcelas' => $parcelas,
                'texto_desconto' => $texto_desconto,
                'validade' => $validade_meta,
            ],
            'obs' => $this->memo ?? '',
            'orc' => $this->orc,
            'situacao_id' => $situacao_id,
            'subtotal' => $subtotal,
            'total' => $total,
        ];
    }
}
