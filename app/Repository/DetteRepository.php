<?php

namespace App\Repository;

use Carbon\Carbon;
use App\Models\Dette;
use App\Models\Article;
use App\Exceptions\RepositoryException;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\Interfaces\DetteRepositoryInterface;

class DetteRepository implements DetteRepositoryInterface
{
    protected $model;
    public function __construct(Dette $model)
    {
        $this->model = $model;
    }

    public function getAll(?array $filters = [])
    {
        try {
            $query = $this->model->newQuery();

            if (isset($filters['statut']) && in_array(strtolower($filters['statut']), ["solder", "nonsolder"])) {
                $filters["include"][] = "client";
            }
            if (isset($filters['include']) && is_array($filters['include'])) {
                $validRelations = ['client', 'articles', 'paiements'];
                foreach ($filters['include'] as $include) {
                    if (in_array($include, $validRelations)) {
                        $query->with($include);
                    }
                }
            }


            $dettes = $this->loadMontantVerserAndRestants($query->get());

            if (isset($filters['statut'])) {
                $dettes = $this->filterByStatut($dettes, $filters['statut']);
            }

            return $dettes;
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to retrieve debts.');
        }
    }

    public function findById(int $id, array $filters = [])
    {
        try {
            $query = $this->model->newQuery();

            if (isset($filters['include']) && is_array($filters['include'])) {
                $validRelations = ['articles', 'paiements'];
                foreach ($filters['include'] as $include) {
                    if (in_array($include, $validRelations)) {
                        $query->with($include);
                    }
                }
            }
            $query->with("client");
            $record = $query->find($id);
            if (!$record) {
                throw new RepositoryException('Debt not found.');
            }
            return $this->loadMontantVerserAndRestant($record);
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to find debt.');
        }
    }

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to create debt.');
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $dettes = $this->findById($id);
            $dettes->update($data);
            return $dettes;
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to update debt.');
        }
    }

    public function delete(int $id)
    {
        try {
            $dettes = $this->findById($id);
            return $dettes->delete();
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to delete debt.');
        }
    }

    public function findByClientId(int $clientId)
    {
        try {
            $dettes =  $this->model->where('client_id', $clientId)->get();
            return $this->loadMontantVerserAndRestants($dettes);
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to find debts for the client.');
        }
    }

    public function loadMontantVerserAndRestant($dette)
    {
        try {
            $paiements = $dette->paiements();
            $montantVerser = $paiements->exists() ? $paiements->sum('montant') : 0;
            $dette->montant_verser = (float)$montantVerser;
            $dette->montant_restant = (float)$dette->montant - $montantVerser;
            return $dette;
        } catch (\Exception $e) {
            throw new RepositoryException('Impossible de mettre à jour les montants de la dette.');
        }
    }


    public function loadMontantVerserAndRestants($dettes)
    {
        try {
            $detteMapping = new Collection();
            foreach ($dettes as $dette) {
                $updatedDette = $this->loadMontantVerserAndRestant($dette);
                $detteMapping->add($updatedDette);
            }
            return $detteMapping;
        } catch (\Exception $e) {
            throw new RepositoryException('Impossible de mettre à jour les montants des dettes.', 0, $e);
        }
    }

    public function attachArticles(Dette $dette, array $articles, bool $reduitQuantity = true)
    {
        $this->verifyArticle($articles, $reduitQuantity);

        $dette->articles()->attach(
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

    public function attachPaiments(Dette $dette, array $paiements)
    {
        foreach ($paiements as $paiementData) {
            $dette->paiements()->create([
                'montant' => $paiementData['montant'],
                'date' => $paiementData['date'],
                'client_id' => $paiementData['client_id'] ?? $dette->client_id, // Par défaut, on prend le client de la dette
            ]);
        }
    }

    public function createPaiement(Dette $dette, array $paiementData)
    {
        if ($paiementData["montant"] > $dette->montant)
            throw new RepositoryException("Le montant du paiement doit être inférieur ou égal au montant de la dette");
        return $dette->paiements()->create($paiementData);
    }


    public function getDettePayer()
    {
        return $this->getAll(["statut" => "solder"]);
    }

    public function filterByStatut($dettes, ?string $statut)
    {
        $detteFilter = new Collection();

        if (strtolower($statut) === "solder") {
            foreach ($dettes as $dette) {
                if ($dette->montant_restant == 0)
                    $detteFilter->add($dette);
            }
            return $detteFilter;
        } elseif (strtolower($statut) === "nonsolder") {
            foreach ($dettes as $dette) {
                if ($dette->montant_restant != 0)
                    $detteFilter->add($dette);
            }
            return $detteFilter;
        }
        return $detteFilter;
    }



    public function findByIdWithRelation(int $id, string $relation)
    {
        try {
            $dette =  $this->model->findOrFail($id);
            $dette->load($relation);
            return $this->loadMontantVerserAndRestant($dette);
        } catch (\Exception $e) {
            throw new RepositoryException("Impossible de trouver cette dette avec l'id: $id", 404);
        }
    }

    public function getNonSoldesEcheanceDepassee(Carbon $date)
    {
        // Récupérer toutes les dettes dont la date d'échéance est antérieure à la date spécifiée
        return Dette::with('paiements')
            ->where('echeance', '<', $date)
            ->get()
            ->filter(function ($dette) {
                $montantTotal = $dette->montant;
                $montantPaye = $dette->paiements->sum('montant');
                $montantRestant = $montantTotal - $montantPaye;

                // Filtrer pour les dettes non soldées
                return $montantRestant > 0;
            });
    }

    public function verifyArticle(array $articles, bool $reduitQuantity = true)
    {
        foreach ($articles as $article) {
            $id = $article['articleId'] ?? $article["id"] ?? 0;
            $articleModel = Article::find($id);
            if (!$articleModel) {
                throw new RepositoryException("Article ID {$id} not found.");
            }
            if ($articleModel->quantity < $article['quantity']) {
                throw new RepositoryException("Not enough stock for article ID {$article['articleId']}. Available: {$articleModel->quantity}, requested: {$article['quantity']}.");
            }
            if (isset($article["articleId"]) && $reduitQuantity) {
                $articleModel->quantity -= $article['quantity'];
                $articleModel->save();
            }
        }
    }
}
