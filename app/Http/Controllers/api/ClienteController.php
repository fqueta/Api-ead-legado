<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClienteController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Cliente::where('excluido', 'n')->where('deletado', 'n');

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Nome', 'like', "%{$search}%")
                  ->orWhere('sobrenome', 'like', "%{$search}%")
                  ->orWhere('Email', 'like', "%{$search}%")
                  ->orWhere('Cpf', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        $clientes = $query->orderBy('Nome')->paginate($request->per_page ?? 15);

        return ClienteResource::collection($clientes);
    }

    public function show($id): ClienteResource
    {
        $cliente = Cliente::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('token', $id)
                  ->orWhere('Cpf', $id)
                  ->orWhere('cpf', $id)
                  ->orWhere('Email', $id)
                  ->orWhere('email', $id);
            })
            ->firstOrFail();

        return new ClienteResource($cliente);
    }

    public function store(Request $request): ClienteResource
    {
        $data = $request->validate([
            'Nome' => 'required|string|max:255',
            'sobrenome' => 'nullable|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Cpf' => 'nullable|string|max:20',
            'Celular' => 'nullable|string|max:20',
            'Endereco' => 'nullable|string|max:255',
            'Numero' => 'nullable|string|max:20',
            'Bairro' => 'nullable|string|max:100',
            'Cidade' => 'nullable|string|max:100',
            'Uf' => 'nullable|string|max:2',
            'Cep' => 'nullable|string|max:10',
            'DtNasc2' => 'nullable|date',
            'estado_civil' => 'nullable|string|max:50',
            'profissao' => 'nullable|string|max:100',
            'id_asaas' => 'nullable|string|max:100',
            'canac' => 'nullable|string|max:50',
            'nacionalidade' => 'nullable|string|max:100',
            'config' => 'nullable|array',
        ]);

        $data['token'] = uniqid();

        $cliente = Cliente::create($data);

        return new ClienteResource($cliente);
    }

    public function update(Request $request, $id): ClienteResource
    {
        $cliente = Cliente::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('token', $id)
                  ->orWhere('Cpf', $id)
                  ->orWhere('cpf', $id)
                  ->orWhere('Email', $id)
                  ->orWhere('email', $id);
            })
            ->firstOrFail();

        $data = $request->validate([
            'Nome' => 'sometimes|required|string|max:255',
            'sobrenome' => 'nullable|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Cpf' => 'nullable|string|max:20',
            'Celular' => 'nullable|string|max:20',
            'Endereco' => 'nullable|string|max:255',
            'Numero' => 'nullable|string|max:20',
            'Bairro' => 'nullable|string|max:100',
            'Cidade' => 'nullable|string|max:100',
            'Uf' => 'nullable|string|max:2',
            'Cep' => 'nullable|string|max:10',
            'DtNasc2' => 'nullable|date',
            'estado_civil' => 'nullable|string|max:50',
            'profissao' => 'nullable|string|max:100',
            'id_asaas' => 'nullable|string|max:100',
            'canac' => 'nullable|string|max:50',
            'nacionalidade' => 'nullable|string|max:100',
            'config' => 'nullable|array',
        ]);

        $cliente->update($data);

        return new ClienteResource($cliente);
    }

    public function export(Request $request)
    {
        $query = Cliente::where('excluido', 'n')->where('deletado', 'n');

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Nome', 'like', "%{$search}%")
                  ->orWhere('sobrenome', 'like', "%{$search}%")
                  ->orWhere('Email', 'like', "%{$search}%")
                  ->orWhere('Cpf', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%");
            });
        }

        $clientes = $query->orderBy('Nome')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="clientes.csv"',
        ];

        $callback = function () use ($clientes) {
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, [
                'ID',
                'Nome',
                'Sobrenome',
                'E-mail',
                'CPF',
                'Celular',
                'Telefone',
                'Endereço',
                'Número',
                'Bairro',
                'Cidade',
                'UF',
                'CEP',
                'Data Nascimento',
                'Estado Civil',
                'Profissão',
                'Ativo',
                'Criado em',
            ]);

            foreach ($clientes as $cliente) {
                fputcsv($output, [
                    $cliente->id,
                    $cliente->Nome,
                    $cliente->sobrenome,
                    $cliente->Email ?? $cliente->email,
                    $cliente->Cpf ?? $cliente->cpf,
                    $cliente->Celular,
                    $cliente->Tel ?? $cliente->Telefone,
                    $cliente->Endereco,
                    $cliente->Numero,
                    $cliente->Bairro,
                    $cliente->Cidade,
                    $cliente->Uf,
                    $cliente->Cep,
                    $cliente->DtNasc2 ? date('d/m/Y', strtotime($cliente->DtNasc2)) : '',
                    $cliente->estado_civil,
                    $cliente->profissao,
                    $cliente->ativo === 's' ? 'Sim' : 'Não',
                    $cliente->data ? date('d/m/Y H:i', strtotime($cliente->data)) : '',
                ]);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $cliente = Cliente::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)
                  ->orWhere('token', $id)
                  ->orWhere('Cpf', $id)
                  ->orWhere('cpf', $id);
            })
            ->firstOrFail();

        $cliente->update([
            'excluido' => 's',
            'reg_excluido' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Cliente excluído com sucesso']);
    }
}
