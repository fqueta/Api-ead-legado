<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaturaResource;
use App\Models\Fatura;
use Illuminate\Http\Request;

class FaturaController extends Controller
{
    public function index(Request $request)
    {
        $query = Fatura::with(['matricula', 'cliente']);

        if ($request->filled('id_cliente')) {
            $query->where('id_cliente', $request->id_cliente);
        }

        if ($request->filled('ref_compra')) {
            $query->where('ref_compra', $request->ref_compra);
        }

        if ($request->filled('pago')) {
            $query->where('pago', $request->pago === 'true' || $request->pago === '1' ? 's' : 'n');
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('vencimento_de')) {
            $query->whereDate('vencimento', '>=', $request->vencimento_de);
        }

        if ($request->filled('vencimento_ate')) {
            $query->whereDate('vencimento', '<=', $request->vencimento_ate);
        }

        $faturas = $query->orderBy('vencimento', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        return FaturaResource::collection($faturas);
    }

    public function show($id)
    {
        $fatura = Fatura::with(['matricula', 'cliente'])->findOrFail($id);

        return new FaturaResource($fatura);
    }

    /**
     * Exporta todas as faturas com dados da matrícula e cliente.
     */
    public function export(Request $request)
    {
        $query = Fatura::with(['matricula', 'cliente']);

        if ($request->filled('id_cliente')) {
            $query->where('id_cliente', $request->id_cliente);
        }

        if ($request->filled('ref_compra')) {
            $query->where('ref_compra', $request->ref_compra);
        }

        if ($request->filled('pago')) {
            $query->where('pago', $request->pago === 'true' || $request->pago === '1' ? 's' : 'n');
        }

        if ($request->filled('vencimento_de')) {
            $query->whereDate('vencimento', '>=', $request->vencimento_de);
        }

        if ($request->filled('vencimento_ate')) {
            $query->whereDate('vencimento', '<=', $request->vencimento_ate);
        }

        $faturas = $query->orderBy('vencimento', 'desc')->orderBy('id', 'desc')->get();

        $data = $faturas->map(function ($fatura) {
            return [
                'id' => $fatura->id,
                'id_cliente' => $fatura->id_cliente,
                'cliente_nome' => $fatura->cliente?->Nome ?? '',
                'cliente_email' => $fatura->cliente?->Email ?? $fatura->cliente?->email ?? '',
                'cliente_cpf' => $fatura->cliente?->Cpf ?? $fatura->cliente?->cpf ?? '',
                'ref_compra' => $fatura->ref_compra,
                'matricula_id' => $fatura->matricula?->id ?? '',
                'matricula_status' => $fatura->matricula?->status ?? '',
                'curso_id' => $fatura->matricula?->id_curso ?? '',
                'categoria' => $fatura->categoria,
                'local' => $fatura->local,
                'descricao' => $fatura->descricao ?? '',
                'vencimento' => $this->formatDate($fatura->vencimento),
                'valor' => $fatura->valor ? (string) $fatura->valor : '0.00',
                'pago' => $fatura->pago === 's' ? 'Sim' : 'Não',
                'data_pagamento' => $this->formatDate($fatura->data_pagamento, 'Y-m-d H:i:s'),
                'conta' => $fatura->conta,
                'tipo' => $fatura->tipo,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        abort(501, 'Not implemented');
    }

    public function update(Request $request, $id)
    {
        abort(501, 'Not implemented');
    }

    public function destroy($id)
    {
        abort(501, 'Not implemented');
    }

    private function formatDate($value, string $format = 'Y-m-d'): string
    {
        if (empty($value) || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return '';
        }
        return date($format, strtotime($value)) ?: '';
    }
}
