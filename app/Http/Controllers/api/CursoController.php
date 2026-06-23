<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CursoResource;
use App\Models\Curso;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CursoController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Curso::where('excluido', 'n')->where('deletado', 'n');

        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo);
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('destaque')) {
            $query->where('destaque', $request->destaque);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('titulo', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
            });
        }

        $cursos = $query->orderBy('ordenar')->orderBy('nome')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'total' => $cursos->total(),
            'data' => CursoResource::collection($cursos)->toArray($request),
        ]);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $curso = Curso::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('token', $id);
            })
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'total' => 1,
            'data' => [new CursoResource($curso)],
        ]);
    }

    public function store(Request $request): CursoResource
    {
        $data = $request->validate([
            'nome' => 'required|string|max:255',
            'titulo' => 'required|string|max:255',
            'url' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'tipo' => 'nullable',
            'descricao' => 'nullable|string',
            'descricao_site' => 'nullable|string',
            'meta_descricao' => 'nullable|string',
            'meta_titulo' => 'nullable|string|max:255',
            'valor' => 'nullable|numeric',
            'inscricao' => 'nullable|numeric',
            'parcelas' => 'nullable|integer',
            'valor_parcela' => 'nullable|numeric',
            'duracao' => 'nullable|integer',
            'unidade_duracao' => 'nullable|string|max:20',
            'ativo' => 'nullable|in:s,n',
            'destaque' => 'nullable|in:s,n',
            'professor' => 'nullable|integer',
            'ordenar' => 'nullable|integer',
            'config' => 'nullable|array',
            'conteudo' => 'nullable|array',
        ]);

        $data['token'] = uniqid();
        $data['autor'] = auth()->id();

        $curso = Curso::create($data);

        return new CursoResource($curso);
    }

    public function update(Request $request, $id): CursoResource
    {
        $curso = Curso::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('token', $id);
            })
            ->firstOrFail();

        $data = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'titulo' => 'sometimes|required|string|max:255',
            'url' => 'nullable|string|max:255',
            'categoria' => 'nullable|string|max:100',
            'tipo' => 'nullable',
            'descricao' => 'nullable|string',
            'descricao_site' => 'nullable|string',
            'meta_descricao' => 'nullable|string',
            'meta_titulo' => 'nullable|string|max:255',
            'valor' => 'nullable|numeric',
            'inscricao' => 'nullable|numeric',
            'parcelas' => 'nullable|integer',
            'valor_parcela' => 'nullable|numeric',
            'duracao' => 'nullable|integer',
            'unidade_duracao' => 'nullable|string|max:20',
            'ativo' => 'nullable|in:s,n',
            'destaque' => 'nullable|in:s,n',
            'professor' => 'nullable|integer',
            'ordenar' => 'nullable|integer',
            'config' => 'nullable|array',
            'conteudo' => 'nullable|array',
        ]);

        $curso->update($data);

        return new CursoResource($curso);
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $curso = Curso::where('excluido', 'n')->where('deletado', 'n')
            ->where(function ($q) use ($id) {
                $q->where('id', $id)->orWhere('token', $id);
            })
            ->firstOrFail();

        $curso->update([
            'excluido' => 's',
            'reg_excluido' => now()->toDateTimeString(),
        ]);

        return response()->json(['message' => 'Curso excluído com sucesso']);
    }
}
