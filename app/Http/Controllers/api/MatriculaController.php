<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MatriculaResource;
use App\Models\Matricula;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MatriculaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Matricula::where('excluido', 'n')->where('deletado', 'n');

        if ($request->filled('id_cliente')) {
            $query->where('id_cliente', $request->id_cliente);
        }

        if ($request->filled('id_curso')) {
            $query->where('id_curso', $request->id_curso);
        }

        if ($request->filled('id_turma')) {
            $query->where('id_turma', $request->id_turma);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $matriculas = $query->with(['cliente', 'curso', 'turma'])
            ->orderBy('id', 'desc')
            ->paginate($request->per_page ?? 15);

        return MatriculaResource::collection($matriculas);
    }

    public function show($id): MatriculaResource
    {
        $matricula = Matricula::where('excluido', 'n')->where('deletado', 'n')
            ->with(['cliente', 'curso', 'turma'])
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('token', $id);
            })
            ->firstOrFail();

        return new MatriculaResource($matricula);
    }

    public function store(Request $request): MatriculaResource
    {
        $data = $request->validate([
            'id_cliente' => 'required|exists:clientes,id',
            'id_curso' => 'required|exists:cursos,id',
            'id_turma' => 'nullable|exists:turmas,id',
            'status' => 'nullable|string|max:50',
            'validade' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'contrato' => 'nullable|string',
            'pagamento_asaas' => 'nullable|string',
            'ativo' => 'nullable|in:s,n',
            'config' => 'nullable|array',
            'tipo_curso' => 'nullable|string|max:50',
        ]);

        $data['token'] = uniqid();

        $matricula = Matricula::create($data);

        return new MatriculaResource($matricula->load(['cliente', 'curso', 'turma']));
    }

    public function update(Request $request, $id): MatriculaResource
    {
        $matricula = Matricula::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('token', $id);
            })
            ->firstOrFail();

        $data = $request->validate([
            'id_cliente' => 'sometimes|required|exists:clientes,id',
            'id_curso' => 'sometimes|required|exists:cursos,id',
            'id_turma' => 'nullable|exists:turmas,id',
            'status' => 'nullable|string|max:50',
            'validade' => 'nullable|date',
            'data_inicio' => 'nullable|date',
            'contrato' => 'nullable|string',
            'pagamento_asaas' => 'nullable|string',
            'ativo' => 'nullable|in:s,n',
            'config' => 'nullable|array',
            'tipo_curso' => 'nullable|string|max:50',
        ]);

        $matricula->update($data);

        return new MatriculaResource($matricula->load(['cliente', 'curso', 'turma']));
    }

    public function export(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Matricula::where('excluido', 'n')->where('deletado', 'n')
            ->with(['cliente', 'curso', 'turma']);

        if ($request->filled('id_cliente')) {
            $query->where('id_cliente', $request->id_cliente);
        }

        if ($request->filled('id_curso')) {
            $query->where('id_curso', $request->id_curso);
        }

        if ($request->filled('id_turma')) {
            $query->where('id_turma', $request->id_turma);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        $matriculas = $query->orderBy('id', 'desc')->get();

        $data = $matriculas->map(function ($matricula) use ($request) {
            return (new MatriculaResource($matricula))->toArray($request);
        });

        return response()->json([
            'success' => true,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $matricula = Matricula::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('token', $id);
            })
            ->firstOrFail();

        $matricula->update([
            'excluido' => 's',
            'reg_excluido' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Matrícula excluída com sucesso']);
    }
}
