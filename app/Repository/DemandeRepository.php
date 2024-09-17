<?php

namespace App\Repository;

use App\Models\Dette;
use App\Models\Article;
use App\Models\Demande;
use Illuminate\Support\Facades\DB;
use App\Facades\DetteRepositoryFacade;
use App\Exceptions\RepositoryException;
use App\Repository\Interfaces\DemandeRepositoryInterface;
use Illuminate\Support\Facades\Log;

class DemandeRepository implements DemandeRepositoryInterface
{
    protected $model;

    public function __construct(Demande $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function findById(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $demande = $this->findById($id);
        if (!$demande) {
            return false;
        }

        return $demande->update($data);
    }

    public function delete(int $id)
    {
        $demande = $this->findById($id);
        if (!$demande) {
            return false;
        }

        return $demande->delete();
    }

    public function findByEtat(string $etat)
    {
        return $this->model->byEtat($etat)->get();
    }

    public function findByDateRange($startDate, $endDate)
    {
        return $this->model->byDateRange($startDate, $endDate)->get();
    }

    public function findByMinMontant(int $minMontant)
    {
        return $this->model->byMinMontant($minMontant)->get();
    }

    public function findByClientId(int $clientId, ?string $etat = null)
    {
        if ($etat)
            return $this->model->byEtat($etat)->byClientId($clientId)->get();

        return $this->model->byClientId($clientId)->get();
    }

    public function attachArticles(Demande $demande, array $articles)
    {
        foreach ($articles as $article) {
            $id = $article['articleId'] ?? $article['id'] ?? 0;
            $articleModel = Article::find($id);
            if (!$articleModel) {
                throw new RepositoryException("Article ID {$id} not found.");
            }
        }
        // Attachement des articles à la demande avec gestion du prix
        $demande->articles()->attach(
            collect($articles)->mapWithKeys(function ($item) {
                $id = $item['articleId'] ?? $item['id'];
                $articleModel = Article::find($id);
                $price = isset($item['price']) ? (float) $item['price'] : (float) $articleModel->price;
                return [
                    $id => [
                        'quantity' => (int) $item['quantity'],
                        'price' => $price,
                    ]
                ];
            })->toArray()
        );
    }

    public function transformDemandeToDette(Demande $demande): Dette
    {
        try {
            DB::beginTransaction();
            $dette = DetteRepositoryFacade::create([
                'client_id' => $demande->client_id,
                'montant' => $demande->montant,
                'date' => now(),
                'echeance' => now()->addMonth(),
            ]);
            $articles = $demande->articles->map(function ($article) {
                return [
                    'articleId' => $article->id,
                    'price' => $article->pivot->price,
                    'quantity' => $article->pivot->quantity,
                ];
            })->toArray();
            DetteRepositoryFacade::attachArticles($dette, $articles);
            DB::commit();
            return $dette;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la transformation de demande en dette: " . $e->getMessage());
            throw new RepositoryException("Échec de la transformation de demande en dette");
        }
    }
}
