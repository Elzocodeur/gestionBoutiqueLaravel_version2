<?php

namespace App\Repository;

use App\Exceptions\RepositoryException;
use App\Models\Paiement;
use App\Repository\Interfaces\PaiementRepositoryInterface;

class PaiementRepository implements PaiementRepositoryInterface
{
    protected $model;

    public function __construct(Paiement $paiement)
    {
        $this->model = $paiement;
    }

    public function getAll($filter = [])
    {
        return $this->filter($filter);
    }

    public function getById($id, $filter = [])
    {
        try {
            $query = $this->model->query();
            $validIncludes = ['dette', 'client'];
            if (isset($filter['include']) && in_array($filter['include'], $validIncludes)) {
                $query->with($filter['include']);
            }
            return $query->findOrFail($id);
        } catch (RepositoryException $e) {
            throw new RepositoryException("Impossible de recupérer le paiement avec l'id: $id.");
        }
    }

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (RepositoryException $e) {
            throw new RepositoryException("Une erreur est survenue lors de la l'ajout de paiement.");
        }
    }

    public function update($id, array $data)
    {
        try {
            $paiement = $this->model->findOrFail($id);
            if ($paiement) {
                $paiement->update($data);
                return $paiement;
            }
            return null;
        } catch (RepositoryException $e) {
            throw new RepositoryException("Impossible de mettre à jour le paiement avec l'id: $id.");
        }
    }

    public function delete($id)
    {
        try {
            $paiement = $this->model->findOrFail($id);
            if ($paiement) {
                $paiement->delete();
                return $paiement;
            }
            return false;
        } catch (RepositoryException $e) {
            throw new RepositoryException("Impossible de supprimer le paiement avec l'id: $id.");
        }
    }

    public function findByDette($detteId)
    {
        try {
            return $this->model->where('dette_id', $detteId)->with('client')->get();
        } catch (RepositoryException $e) {
            throw new RepositoryException("Impossible de supprimer le paiement avec l'id: $detteId.");
        }
    }

    public function findByClient($clientId)
    {
        try {
            return $this->model->where('client_id', $clientId)->with('dette')->get();
        } catch (RepositoryException $e) {
            throw new RepositoryException("Impossible de supprimer le paiement avec l'id: $clientId.");
        }
    }

    public function filter(array $filters)
    {
        try {
            $query = $this->model->query();
            if (isset($filters['client_id'])) {
                $query->where('client_id', $filters['client_id']);
            }
            if (isset($filters['dette_id'])) {
                $query->where('dette_id', $filters['dette_id']);
            }
            if (isset($filters['start_date']) && isset($filters['end_date'])) {
                $query->whereBetween('date', [$filters['start_date'], $filters['end_date']]);
            }
            if (isset($filters['montant_min'])) {
                $query->where('montant', '>=', $filters['montant_min']);
            }
            if (isset($filters['montant_max'])) {
                $query->where('montant', '<=', $filters['montant_max']);
            }
            $validIncludes = ['dette', 'client'];
            if (isset($filter['include']) && in_array($filter['include'], $validIncludes)) {
                $query->with($filter['include']);
            }
            return $query->get();
        } catch (RepositoryException $e) {
            throw new RepositoryException("Impossible c'est produit lors de la recherche le paiement");
        }
    }
}
