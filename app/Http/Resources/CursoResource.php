<?php

namespace App\Http\Resources;

use App\Models\ConteudoEad;
use App\Models\Modulo;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CursoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $modulos = $this->getModulos();

        return [
            'ID_antigo' => (string) $this->id,
            'ativo' => $this->ativo,
            'titulo' => $this->titulo,
            'nome' => $this->nome,
            'slug' => $this->url,
            'descricao_curso' => $this->descricao ?? '',
            'duracao' => (string) $this->duracao,
            'unidade_duracao' => $this->unidade_duracao === 'h' ? 'hrs' : ($this->unidade_duracao ?? ''),
            'valor' => (string) $this->valor,
            'inscricao' => (string) $this->inscricao,
            'parcelas' => (string) $this->parcelas,
            'valor_parcela' => (string) $this->valor_parcela,
            'tipo' => (string) $this->tipo,
            'publicar' => $this->ativo,
            'instrutor' => (string) $this->professor,
            'observacoes' => $this->obs ?? '',
            'perguntas' => [],
            'modulos' => $modulos,
            'config' => $this->config,
        ];
    }

    private function getModulos(): array
    {
        $modulosData = $this->conteudo;

        if (!is_array($modulosData) || empty($modulosData)) {
            $modulosData = [];
        }

        $moduleIds = collect($modulosData)->pluck('idItem')->filter()->values()->toArray();

        if (empty($moduleIds)) {
            return [];
        }

        $modulos = Modulo::whereIn('id', $moduleIds)
            ->where('excluido', 'n')
            ->where('deletado', 'n')
            ->get()
            ->keyBy('id');

        $modulesOrdered = collect($moduleIds)
            ->map(fn($id) => $modulos->get($id))
            ->filter()
            ->values();

        return $modulesOrdered->map(fn($modulo) => $this->formatModulo($modulo))->toArray();
    }

    private function formatModulo(Modulo $modulo): array
    {
        $conteudoMod = $modulo->conteudo;

        $activityIds = [];
        if (is_array($conteudoMod)) {
            $activityIds = collect($conteudoMod)->pluck('idItem')->filter()->values()->toArray();
        }

        $atividades = [];

        if (!empty($activityIds)) {
            $atividadesModel = ConteudoEad::whereIn('id', $activityIds)
                ->where('excluido', 'n')
                ->where('deletado', 'n')
                ->get()
                ->keyBy('id');

            $atividades = collect($activityIds)
                ->map(fn($id) => $atividadesModel->get($id))
                ->filter()
                ->map(fn($atv) => $this->formatAtividade($atv))
                ->values()
                ->toArray();
        }

        return [
            'active' => $modulo->ativo ?? 's',
            'module_id' => (string) $modulo->id,
            'name' => $modulo->nome_exibicao ?? $modulo->nome,
            'title' => $modulo->nome_exibicao ?? $modulo->nome,
            'description' => $modulo->descricao ?? '',
            'duration' => (string) ($modulo->config['duration'] ?? '0'),
            'type_duration' => $modulo->config['type_duration'] ?? 'seg',
            'atividades' => $atividades,
        ];
    }

    private function formatAtividade(ConteudoEad $atividade): array
    {
        $videoUrl = '';

        if ($atividade->video) {
            if (str_starts_with($atividade->video, 'http')) {
                $videoUrl = $atividade->video;
            } elseif ($atividade->tipo_link_video === 'y' || $atividade->tipo_link_video === 'youtube') {
                $videoUrl = "https://www.youtube.com/watch?v={$atividade->video}";
            } elseif ($atividade->tipo_link_video === 'v' || strtolower($atividade->tipo ?? '') === 'video') {
                $videoUrl = "https://player.vimeo.com/video/{$atividade->video}";
            }
        }

        return [
            'active' => $atividade->ativo ?? 's',
            'name' => $atividade->nome_exibicao ?? $atividade->nome,
            'title' => $atividade->nome_exibicao ?? $atividade->nome,
            'type_activities' => strtolower($atividade->tipo ?? ''),
            'content' => $videoUrl,
            'id_antigo' => (string) $atividade->id,
            'description' => $atividade->descricao ?? '',
            'duration' => (string) ($atividade->duracao ?? '0'),
            'type_duration' => $atividade->unidade_duracao ?? 'seg',
        ];
    }
}
