<?php

namespace App\Services;

use App\Models\Dette;
use App\Models\Demande;
use App\Enums\DemandeEnum;
use App\Facades\DetteFacade;
use App\Jobs\SendNotificationsJob;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ServiceException;
use App\Exceptions\RepositoryException;
use App\Facades\ArticleFacade;
use App\Jobs\SendNotificationProcessingJob;
use Illuminate\Database\Eloquent\Collection;
use Google\Cloud\Core\Exception\NotFoundException;
use App\Notifications\StockInsuffisantNotification;
use App\Services\Interfaces\DemandeServiceInterface;
use App\Repository\Interfaces\DemandeRepositoryInterface;

class DemandeService implements DemandeServiceInterface
{
    protected $repository;

    public function __construct(DemandeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllDemande(): Collection
    {
        return $this->repository->all();
    }

    public function getDemandeById(int $id): ?Demande
    {
        $demande = $this->repository->findById($id);
        if (!$demande) {
            throw new NotFoundException("Demande not found with ID: $id");
        }
        return $demande;
    }

    public function createDemande(array $data, array $articles = []): Demande
    {
        try {
            $autorize = DetteFacade::autorize($data["client_id"], $data["montant"]);
            if (!$autorize[0])
                throw new ServiceException($autorize[1]);
            DB::beginTransaction();
            $demande = $this->repository->create($data);
            $this->repository->attachArticles($demande, $articles);
            DB::commit();
            SendNotificationsJob::dispatch($demande->client);
            return $demande;
        } catch (RepositoryException | ServiceException $e) {
            DB::rollBack();
            throw new ServiceException($e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new ServiceException("Failed to create demande", 0, $e);
        }
    }

    public function updateDemande(int $id, array $data): bool
    {
        try {
            return $this->repository->update($id, $data);
        } catch (\Exception $e) {
            throw new ServiceException("Failed to update demande with ID: $id", 0, $e);
        }
    }

    public function deleteDemande(int $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            throw new ServiceException("Failed to delete demande with ID: $id", 0, $e);
        }
    }

    public function getDemandeByEtat(?string $etat = null): Collection
    {
        $validEtats = array_map(fn($case) => $case->value, DemandeEnum::cases());
        if (!in_array($etat, $validEtats, true)) {
            return $this->repository->all();
        }
        return $this->repository->findByEtat($etat);
    }

    public function getDemandeByDateRange($startDate, $endDate): Collection
    {
        return $this->repository->findByDateRange($startDate, $endDate);
    }

    public function getDemandeByMinMontant(int $minMontant): Collection
    {
        return $this->repository->findByMinMontant($minMontant);
    }

    public function getDemandeByClientId(int $clientId, ?string $filter = null): Collection
    {
        return $this->repository->findByClientId($clientId, $filter);
    }

    public function changeDemandeEtat(int $id, string $newEtat, ?string $motif = ""): bool
    {
        try {
            $demande = $this->repository->findById($id);

            if (!$demande) {
                throw new NotFoundException("Demande not found with ID: $id");
            }
            if ($demande->etat === DemandeEnum::ANNULER->value) {
                throw new ServiceException("La demande a déja été annuler");
            }
            if ($newEtat === DemandeEnum::EN_COURS->value) {
                throw new ServiceException("L'état de la demande ne peut pas être change en cours");
            }
            $result = $this->repository->update($id, array('etat' => $newEtat, 'motif' => $motif));
            if ($result)
                SendNotificationProcessingJob::dispatch($demande, $newEtat, $motif);
            return $result;
        } catch (RepositoryException $re) {
            throw new ServiceException($re->getMessage());
        } catch (\InvalidArgumentException $iae) {
            throw new ServiceException("Invalid state value: $newEtat", 0, $iae);
        } catch (\Exception $e) {
            throw new ServiceException("Failed to change state for demande with ID: $id", 0, $e);
        }
    }


    public function relanceDemande(int $id, int $clientId): Demande
    {
        try {
            $demandeExistante = $this->getDemandeById($id);
            if (!$demandeExistante)
                throw new ServiceException("Demande not found with ID: $id");
            if ($demandeExistante->client_id !== $clientId)
                throw new ServiceException("Unauthorized: You do not own this demande.");
            $nouvelleDemandeData = $demandeExistante->toArray();
            $articles = $this->transformArticle($demandeExistante);
            unset($nouvelleDemandeData['id']);
            $nouvelleDemandeData['etat'] = DemandeEnum::EN_COURS->value;
            $nouvelleDemandeData['date'] = now();
            return $this->createDemande($nouvelleDemandeData, $articles);
        } catch (ServiceException $se) {
            throw new ServiceException($se->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Failed to relance demande", 0, $e);
        }
    }

    public function demandeEstDisponible(int $id)
    {
        $demande = $this->repository->findById($id);
        if ($demande) {
            return DetteFacade::articlesDisponible(
                $this->transformArticle($demande)
            );
        }
    }


    public function transformArticle(Demande $demande): array
    {
        return  $demande->articles->mapWithKeys(function ($article) {
            return [
                $article->id => [
                    'id' => $article->id,
                    'quantity' => $article->pivot->quantity,
                    'price' => $article->pivot->price
                ]
            ];
        })->toArray();
    }

    public function transformDemandeToDette(Demande $demande): Dette
    {
        try {
            $dette = $this->repository->transformDemandeToDette($demande);
            if (!$dette)
                throw new ServiceException("La transformation de demande en dette ne s'est pas effectuer");
            $demande->delete();
            return $dette;
        } catch (ServiceException | RepositoryException $se) {
            throw new ServiceException($se->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Échec de la tranformation de demande en article", 0, $e);
        }
    }

    public function demandeNonSatisfait($id)
    {
        $demande = $this->getDemandeById($id);
        $result = $this->demandeEstDisponible($id);
        if (count($result["non"])) {
            $this->sendStockNotification($demande->client, $result["non"]);
        }
        return [
            "demande" => $demande,
            "articles" => $result
        ];
    }

    protected function sendStockNotification($client, $notifications)
    {
        $message = 'Votre demande ne peut être entièrement satisfaite en raison de stock insuffisant pour les articles suivants : ';
        foreach ($notifications as $notification) {
            $article = ArticleFacade::getArticleById($notification["id"]);
            $quantity = $article->quantity - $article->seuil;
            $message .= 'Article ID ' . $notification['id'] . ': disponible ' . $quantity . ', demandé ' . $notification['quantity'] . '; ';
        }
        $message .= 'Vous pouvez ajuster votre demande en conséquence.';

        $client->notify(new StockInsuffisantNotification($message));
    }
}
