<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ServiceException;
use App\Exceptions\RepositoryException;
use App\Services\Interfaces\DetteServiceInterface;
use App\Repository\Interfaces\DetteRepositoryInterface;
use App\Repository\Interfaces\ClientRepositoryInterface;
use App\Repository\Interfaces\ArticleRepositoryInterface;

class DetteService implements DetteServiceInterface
{
    public function __construct(
        protected DetteRepositoryInterface $detteRepository,
        protected ArticleRepositoryInterface $articleRepository,
        protected ClientRepositoryInterface $clientRepository,
    ) {}

    public function getAllDettes(array $withRelations = [])
    {
        try {
            return $this->detteRepository->getAll($withRelations);
        } catch (\Exception $e) {
            throw new ServiceException('Impossible de trouver la dette.');
        }
    }

    public function getDetteById($id, array $withRelations = [])
    {
        try {
            return $this->detteRepository->findById($id, $withRelations);
        } catch (\Exception $e) {
            throw new ServiceException('Impossible de trouver la dette.');
        }
    }

    public function createDette(array $detteData, array $articles = [], $paiement = null)
    {
        try {
            $result = $this->articlesDisponible($articles);
            $disponbile = $result["oui"];
            $notDisponible = $result["non"];
            $autorize = $this->autorize($detteData["client_id"], $detteData["montant"]);
            if (!$autorize[0])
                throw new ServiceException($autorize[1]);

            if ($paiement && $paiement["montant"] > $detteData["montant"])
                return ["message" => "Le montant du paiement doit être inférieur ou égal au montant de la dette"];

            if (count($articles) === count($disponbile)) {
                $dette = $this->detteRepository->create($detteData);
                return $this->detteRepository->loadMontantVerserAndRestant($dette);
            }
            return [
                "disponible" => $disponbile,
                "non disponible" => $notDisponible
            ];
        } catch (RepositoryException $re) {
            throw new ServiceException($re->getMessage());
        } catch (ServiceException $se) {
            throw new ServiceException($se->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException('Impossible de créer la dette.');
        }
    }

    public function updateDette(int $id, array $detteData, array $articles = [])
    {
        try {
            $dette = $this->detteRepository->findById($id);

            if (!$dette) {
                throw new \Exception('Dette non trouvée.');
            }
            $dette->fill($detteData);
            $dette->save();
            $dette = $this->detteRepository->loadMontantVerserAndRestant($dette);
            return $dette;
        } catch (\Exception $e) {
            throw new ServiceException('Impossible de mettre à jour la dette.');
        }
    }

    public function deleteDette($id)
    {
        try {
            return $this->detteRepository->delete($id);
        } catch (\Exception $e) {
            throw new ServiceException('Impossible de supprimer la dette.');
        }
    }



    public function createDetteWithPaiements(array $detteData, array $articles = [], array $paiement = null)
    {
        DB::beginTransaction();

        try {
            $dette = $this->detteRepository->create($detteData);

            if (!empty($articles)) {
                $this->detteRepository->attachArticles($dette, $articles);
            }

            if ($paiement && isset($paiement['montant'])) {
                $this->detteRepository->createPaiement($dette, [
                    'montant' => $paiement['montant'],
                    'date' => now(),
                ]);
            }
            DB::commit();
            return $dette;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServiceException('Impossible de créer la dette avec les paiements.');
        }
    }


    public function getDetteWithRelation(int $id, string $relation)
    {
        try {
            return $this->detteRepository->findByIdWithRelation($id, $relation);
        } catch (\Exception $e) {
            throw new ServiceException('Impossible de trouver les dettes.');
        }
    }

    public function autorize($clientId, $montant = 0)
    {
        $client = $this->clientRepository->findById($clientId);
        if (!$client) {
            return [false, "Client non trouvé"];
        }
        if ($client->isBronze()) {
            if ($client->hasDebt()) {
                return [false, "Le client Bronze ne peut pas faire de demande avec des dettes impayées"];
            }
            return [true, "Le client Bronze est autorisé à faire une demande"];
        }
        if ($client->isSilver()) {
            if ($client->currentDebt() < $client->max_montant && $montant <= $client->max_montant) {
                return [true, "Le client Silver est autorisé à faire une demande"];
            } else {
                $message = ($montant > $client->max_montant)
                    ? "Le montant de la dette dépasse le montant plafond de cette client silver"
                    : "Le client Silver a dépassé le plafond de dette maximum";
                return [false, $message];
            }
        }
        if ($client->isGold()) {
            return [true, "Le client Gold est autorisé à faire un nombre illimité de demandes"];
        }
        // Si la catégorie du client n'est ni Bronze, Silver, ni Gold
        return [false, "Catégorie du client non reconnue"];
    }


    public function articlesDisponible(array $articles): array
    {
        $disponbile = [
            "oui" => array(),
            "non" => array(),
        ];
        foreach ($articles as $article) {
            if ($this->articleRepository->avalable($article["articleId"] ?? $article["id"], $article["quantity"])) {
                $disponbile["oui"][] = $article;
            } else {
                $disponbile["non"][] = $article;
            }
        }
        return $disponbile;
    }

    public function getNonSoldesEcheanceDepassee(?Carbon $date=null)
    {
        if(empty($date))
            $date = Carbon::now();
        return $this->detteRepository->getNonSoldesEcheanceDepassee($date);
    }
}
