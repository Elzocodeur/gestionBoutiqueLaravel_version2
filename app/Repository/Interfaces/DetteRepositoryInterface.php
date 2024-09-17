<?php

namespace App\Repository\Interfaces;

use Carbon\Carbon;
use App\Models\Dette;

interface DetteRepositoryInterface
{
    public function getAll(?array $filters = []);

    public function findById(int $id, array $withRelations = []);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function findByClientId(int $clientId);

    public function loadMontantVerserAndRestant($dette);

    public function loadMontantVerserAndRestants($dettes);

    public function createPaiement(Dette $dette, array $paiementData);

    public function attachArticles(Dette $dette, array $articles, bool $reduitQuantity = true);

    public function attachPaiments(Dette $dette, array $paiements);

    public function getDettePayer();

    public function filterByStatut($query, ?string $statut);

    public function findByIdWithRelation(int $id, string $relation);

    public function getNonSoldesEcheanceDepassee(Carbon $date);

    public function verifyArticle(array $articles, bool $reduitQuantity = true);
}
