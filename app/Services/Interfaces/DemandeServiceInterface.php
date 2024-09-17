<?php

namespace App\Services\Interfaces;

use App\Models\Dette;
use App\Models\Demande;
use Illuminate\Database\Eloquent\Collection;

interface DemandeServiceInterface
{

    public function getAllDemande(): Collection;

    public function getDemandeById(int $id): ?Demande;

    public function createDemande(array $data): Demande;

    public function updateDemande(int $id, array $data): bool;

    public function deleteDemande(int $id): bool;

    public function getDemandeByEtat(?string $etat = null): Collection;

    public function getDemandeByDateRange($startDate, $endDate): Collection;

    public function getDemandeByMinMontant(int $minMontant): Collection;

    public function getDemandeByClientId(int $clientId, ?string $filter = null): Collection;

    public function changeDemandeEtat(int $id, string $newEtat): bool;

    public function relanceDemande(int $id, int $clientId): Demande;

    public function transformArticle(Demande $demande): array;

    public function demandeEstDisponible(int $id);

    public function demandeNonSatisfait($id);

    public function transformDemandeToDette(Demande $demande): Dette;
}
