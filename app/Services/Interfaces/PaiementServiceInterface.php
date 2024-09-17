<?php

namespace App\Services\Interfaces;

interface PaiementServiceInterface
{

    public function getAllPaiements(array $filters = []);

    public function getPaiementById($id);

    public function createPaiement(array $data);

    public function updatePaiement($id, array $data);

    public function deletePaiement($id);
}