<?php

namespace App\Services\Interfaces;

use Carbon\Carbon;

interface DetteServiceInterface
{
    public function getAllDettes(array $withRelations = []);

    public function getDetteById($id, array $withRelations = []);

    public function createDette(array $detteData, array $articles = [], $paiement = null);

    public function updateDette(int $id, array $detteData, array $articles = []);

    public function deleteDette($id);

    public function getDetteWithRelation(int $id, string $relation);

    public function articlesDisponible(array $articles): array;

    public function autorize($clientId, $montant = 0);

    public function getNonSoldesEcheanceDepassee(?Carbon $date = null);
}
