<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TurmaResource;
use App\Models\Turma;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TurmaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Turma::where('excluido', 'n')->where('deletado', 'n');

        if ($request->filled('id_curso')) {
            $query->where('id_curso', $request->id_curso);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $turmas = $query->with('curso')->orderBy('id')->paginate($request->per_page ?? 15);

        return TurmaResource::collection($turmas);
    }

    public function show($id): TurmaResource
    {
        $turma = Turma::where('excluido', 'n')->where('deletado', 'n')
            ->with('curso')
            ->findOrFail($id);

        return new TurmaResource($turma);
    }

    public function store(Request $request): TurmaResource
    {
        $data = $request->validate([
            'id_curso' => 'required|exists:cursos,id',
            'nome' => 'required|string|max:255',
            'inicio' => 'nullable|date',
            'fim' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'max_alunos' => 'nullable|integer',
            'ativo' => 'nullable|in:s,n',
        ]);

        $turma = Turma::create($data);

        return new TurmaResource($turma->load('curso'));
    }

    public function update(Request $request, $id): TurmaResource
    {
        $turma = Turma::where('excluido', 'n')->where('deletado', 'n')
            ->findOrFail($id);

        $data = $request->validate([
            'id_curso' => 'sometimes|required|exists:cursos,id',
            'nome' => 'sometimes|required|string|max:255',
            'inicio' => 'nullable|date',
            'fim' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'max_alunos' => 'nullable|integer',
            'ativo' => 'nullable|in:s,n',
        ]);

        $turma->update($data);

        return new TurmaResource($turma->load('curso'));
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $turma = Turma::where('excluido', 'n')->where('deletado', 'n')
            ->findOrFail($id);

        $turma->update([
            'excluido' => 's',
            'reg_excluido' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Turma excluída com sucesso']);
    }

    public function export(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Turma::where('excluido', 'n')->where('deletado', 'n')
            ->with(['curso', 'matriculas']);

        if ($request->filled('id_curso')) {
            $query->where('id_curso', $request->id_curso);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_inicio', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_inicio', '<=', $request->data_fim);
        }

        $turmas = $query->orderBy('nome')->get();

        $data = $turmas->map(function ($turma) {
            return [
                'id' => $turma->id,
                'curso' => $turma->curso->nome ?? '',
                'id_curso' => $turma->id_curso,
                'nome' => $turma->nome,
                'inicio' => $turma->inicio?->format('d/m/Y') ?? '',
                'fim' => $turma->fim?->format('d/m/Y') ?? '',
                'data_inicio' => $turma->data_inicio?->format('d/m/Y') ?? '',
                'max_alunos' => $turma->max_alunos ?? '',
                'matriculados' => $turma->matriculas->count(),
                'ativo' => $turma->ativo === 's',
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }
}
