<?php

namespace App\Services;

use App\Exceptions\RepositoryException;
use App\Services\Interfaces\PaiementServiceInterface;
use App\Repository\Interfaces\PaiementRepositoryInterface;
use App\Repository\Interfaces\DetteRepositoryInterface;
use App\Exceptions\ServiceException;
use Exception;

class PaiementService implements PaiementServiceInterface
{
    protected $paiementRepository;
    protected $detteRepository;

    public function __construct(PaiementRepositoryInterface $paiementRepository, DetteRepositoryInterface $detteRepository)
    {
        $this->paiementRepository = $paiementRepository;
        $this->detteRepository = $detteRepository;
    }

    public function getAllPaiements(array $filters = [])
    {
        try {
            return $this->paiementRepository->filter($filters);
        } catch (Exception $e) {
            throw new ServiceException('Impossible de récupérer les paiements.', 0, $e);
        }
    }

    public function getPaiementById($id, $filters = [])
    {
        try {
            $paiement = $this->paiementRepository->getById($id, $filters);
            if (!$paiement) {
                throw new ServiceException("Paiement avec l'ID $id non trouvé.");
            }
            return $paiement;
        } catch (Exception $e) {
            throw new ServiceException("Erreur lors de la récupération du paiement avec l'ID $id.", 0, $e);
        }
    }

    public function createPaiement(array $data)
    {
        try {
            if (!isset($data['dette_id'])) {
                throw new ServiceException('Une dette doit être associée au paiement.');
            }
            $dette = $this->detteRepository->findById($data['dette_id']);
            if (!$dette) {
                throw new ServiceException('Dette non trouvée.');
            }
            $montantRestant = $dette->montant_restant;

            if ($data['montant'] > $montantRestant) {
                throw new ServiceException('Le montant payé dépasse le montant restant de la dette.', 404);
            }

            $data['client_id'] = $dette->client_id;

            $data['date'] = now();

            $paiement = $this->paiementRepository->create($data);

            return $paiement;
        } catch (RepositoryException $e) {
            throw new ServiceException('Impossible de créer le paiement.', 0, $e);
        }
    }

    public function updatePaiement($id, array $data)
    {
        try {
            $paiement = $this->paiementRepository->getById($id);
            if (!$paiement) {
                throw new ServiceException("Paiement avec l'ID $id non trouvé.");
            }

            // Mettre à jour le paiement avec les nouvelles données
            return $this->paiementRepository->update($id, $data);
        } catch (Exception $e) {
            throw new ServiceException("Erreur lors de la mise à jour du paiement avec l'ID $id.", 0, $e);
        }
    }

    public function deletePaiement($id)
    {
        try {
            $paiement = $this->paiementRepository->getById($id);
            if (!$paiement) {
                throw new ServiceException("Paiement avec l'ID $id non trouvé.");
            }

            // Supprimer le paiement
            return $this->paiementRepository->delete($id);
        } catch (Exception $e) {
            throw new ServiceException("Erreur lors de la suppression du paiement avec l'ID $id.", 0, $e);
        }
    }
}
