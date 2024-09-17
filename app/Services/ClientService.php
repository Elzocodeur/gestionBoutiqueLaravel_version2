<?php

namespace App\Services;

use App\Facades\QrCodeFacade;
use Illuminate\Support\Facades\Log;
use App\Exceptions\ServiceException;
use App\Facades\CategorieFacade;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\ClientServiceInterface;
use App\Repository\Interfaces\ClientRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;
    protected $roleServie;

    public function __construct(ClientRepositoryInterface $clientRepository, RoleServiceInterface $roleServie)
    {
        $this->clientRepository = $clientRepository;
        $this->roleServie = $roleServie;
    }

    public function create(array $data)
    {
        try {
            $caterogie = $data["categorie"] ?? "bronze";
            $clientData = [
                'surname' => $data["surname"],
                'adresse' => $data["adresse"],
                'telephone' => $data["telephone"],
                'max_montant' => $data["max_montant"] ?? 0,
                'categorie_id' => CategorieFacade::getId($caterogie),
                'qrcode' => QrCodeFacade::generateQrCode($data["telephone"]),
            ];
            return $this->clientRepository->create($clientData);
        } catch (\Exception $e) {
            Log::error('Error creating client: ' . $e->getMessage());
            throw new ServiceException('Unable to create client.');
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $client = $this->clientRepository->findById($id);
            if (!$client) {
                throw new ModelNotFoundException('Client not found.');
            }

            if(isset($data["categorie"])){
                $data['categorie_id'] = CategorieFacade::getId($data["categorie"]);
                unset($data["categorie"]);
            }

            return $this->clientRepository->update($id, $data);
        } catch (ModelNotFoundException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error updating client: ' . $e->getMessage());
            throw new ServiceException('Unable to upEate client.');
        }
    }

    public function delete(int $id)
    {
        try {
            $client = $this->clientRepository->findById($id);
            if (!$client) {
                throw new ModelNotFoundException('Client not found.');
            }

            return $this->clientRepository->delete($id);
        } catch (ModelNotFoundException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error deleting client: ' . $e->getMessage());
            throw new ServiceException('Unable to delete client.');
        }
    }

    public function find(int $id)
    {
        try {
            return $this->clientRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('Error finding client: ' . $e->getMessage());
            throw new ServiceException('Unable to find client.');
        }
    }

    public function getAllCLient($filters = [])
    {
        try {
            return $this->clientRepository->getAll($filters);
        } catch (\Exception $e) {
            Log::error('Error retrieving clients: ' . $e->getMessage());
            throw new ServiceException('Unable to retrieve clients.');
        }
    }

    public function searchByTelephone(string $telephone)
    {
        try {
            return $this->clientRepository->searchByTelephone($telephone);
        } catch (\Exception $e) {
            Log::error('Error searching client by telephone: ' . $e->getMessage());
            throw new ServiceException('Unable to search client by telephone.');
        }
    }

    public function findByUserId(int $userId)
    {
        try {
            return $this->clientRepository->findByUserId($userId);
        } catch (\Exception $e) {
            Log::error('Error finding client by user ID: ' . $e->getMessage());
            throw new ServiceException('Unable to find client by user ID.');
        }
    }

    public function getClientWithUser(int $id)
    {
        $client = $this->find($id);
        $client->load("user");
        return $client;
    }

    public function getClientWithDette(int $id)
    {
        $client = $this->find($id);
        $client->load("dettes");
        return $client;
    }
}
