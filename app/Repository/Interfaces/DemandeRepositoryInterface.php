<?php

namespace App\Repository\Interfaces;

use App\Models\Dette;
use App\Models\Demande;

interface DemandeRepositoryInterface
{

    public function all();

    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function findByEtat(string $etat);

    public function findByDateRange($startDate, $endDate);

    public function findByMinMontant(int $minMontant);

    public function findByClientId(int $clientId, ?string $filter = null);

    public function attachArticles(Demande $demande, array $articles);

    public function transformDemandeToDette(Demande $demande): Dette;
}
