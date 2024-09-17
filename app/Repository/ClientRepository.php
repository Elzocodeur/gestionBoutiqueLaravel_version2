<?php

namespace App\Repository;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Exceptions\RepositoryException;
use App\Models\User;
use App\Repository\Interfaces\ClientRepositoryInterface;

use function Laravel\Prompts\error;

class ClientRepository implements ClientRepositoryInterface
{
    protected $model;
    protected $userModel;

    public function __construct(Client $model, User $userModel)
    {
        $this->model = $model;
        $this->userModel = $userModel;
    }

    public function getAll(array $filter = [])
    {
        try {
            $query = $this->model->query();
            if (isset($filter['active']) && !is_null($filter['active'])) {
                $query->active($filter['active']);
            }
            if (isset($filter['comptes']) && !is_null($filter['comptes'])) {
                $query->hasAccount($filter['comptes']);
            }
            $validIncludes = ['user', 'dettes', 'paiements'];
            if (isset($filter['include']) && in_array($filter['include'], $validIncludes)) {
                $query->with($filter['include']);
            }
            return $query->get();
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to retrieve clients: ' . $e->getMessage());
        }
    }


    public function findById(int $id)
    {
        try {
            $client = $this->model->find($id);
            if (!$client) {
                throw new RepositoryException('Client not found.');
            }
            return $client;
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to find client.');
        }
    }

    public function create(array $data)
    {
        try {
            return $this->model->create($data);
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to create client.');
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $client = $this->findById($id);
            $client->update($data);
            return $client;
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to update client.');
        }
    }

    public function delete(int $id)
    {
        try {
            $client = $this->findById($id);
            return $client->delete();
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to delete client.');
        }
    }

    public function searchByTelephone(string $telephone)
    {
        try {
            return $this->model->where('telephone', $telephone)->first();
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to search client by telephone.');
        }
    }

    public function findByUserId(int $userId)
    {
        try {
            return $this->model->where('user_id', $userId)->first();
        } catch (\Exception $e) {
            throw new RepositoryException('Unable to find client by user ID.');
        }
    }

    public function createClient($clientData, $userData = null)
    {
        try {
            DB::beginTransaction();
            $client = $this->model->create($clientData);
            if ($userData) {
                if (!isset($userData["role_id"]))
                    throw new RepositoryException("Missing role_id");
                $user = $this->userModel->create($userData);
                $user->client()->save($client);
            }
            DB::commit();
            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new RepositoryException("An error occurred while creating client: " . $e->getMessage());
        }
    }

    public function getHaveDette()
    {
        try {
            return $this->model->with("dettes")->whereHas("dettes", function ($query) {
                $query->whereRaw('montant > (SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id)');
            })->get();
        } catch (\Exception $e) {
            Log:
            error("Erreur lors de la récupération des clients avec des dettes non solder", [$e->getMessage()]);
            throw new RepositoryException("An error occurred while load client: with dette");
        }
    }
}
