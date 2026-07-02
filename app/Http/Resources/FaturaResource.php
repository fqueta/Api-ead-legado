<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaturaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $regAsaas = is_array($this->reg_asaas) ? $this->reg_asaas : [];

        return [
            'id' => $this->id,
            'id_cliente' => $this->id_cliente,
            'ref_compra' => $this->ref_compra,
            'categoria' => $this->categoria,
            'local' => $this->local,
            'vencimento' => $this->formatDate($this->vencimento),
            'valor' => $this->valor ? (string) $this->valor : '0.00',
            'pago' => $this->pago === 's',
            'data_pagamento' => $this->formatDate($this->data_pagamento, 'Y-m-d H:i:s'),
            'descricao' => $this->descricao ?? '',
            'conta' => $this->conta,
            'tipo' => $this->tipo,
            'bank_slip_url' => $regAsaas['bankSlipUrl'] ?? null,
            'invoice_url' => $regAsaas['invoiceUrl'] ?? null,
            'payment_id' => $regAsaas['id'] ?? null,
            'billing_type' => $regAsaas['billingType'] ?? null,

            'matricula' => $this->whenLoaded('matricula', fn () => [
                'id' => $this->matricula->id,
                'token' => $this->matricula->token,
                'id_curso' => $this->matricula->id_curso,
                'status' => $this->matricula->status,
            ]),
            'cliente' => $this->whenLoaded('cliente', fn () => [
                'id' => $this->cliente->id,
                'nome' => $this->cliente->Nome,
                'email' => $this->cliente->Email ?? $this->cliente->email,
                'cpf' => $this->cliente->Cpf ?? $this->cliente->cpf,
            ]),
        ];
    }

    private function formatDate($value, string $format = 'Y-m-d'): ?string
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return null;
        }
        return date($format, strtotime($value)) ?: null;
    }
}
