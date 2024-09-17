<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Exceptions\RepositoryException;
use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use App\Repository\Interfaces\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository implements UserRepositoryInterface
{

    protected $model;
    protected $clientModel;

    public function __construct(User $model, Client $clientModel)
    {
        $this->model = $model;
        $this->clientModel = $clientModel;
    }


    public function all(array $filters = []): Collection
    {
        try {
            $query = $this->model->query();
            if (isset($filters['role'])) {
                $query->filterByRole($filters['role']);
            }
            if (isset($filters['etat'])) {
                $query->filterByStatus($filters['etat']);
            }
            return $query->get();
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
        }
    }


    public function find(int $id): ?User
    {
        try {
            return $this->model->find($id);
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la recherche de l\'utilisateur: ' . $e->getMessage());
        }
    }


    public function create(array $attributes): User
    {
        try {
            unset($attributes["role"]);
            if (!isset($attributes["is_blocked"]))
                $attributes["is_blocked"] = false;
            if (!isset($attributes["photo"]))
                $attributes["photo"] = "storage/images/avatar.jpg";
            return $this->model->create($attributes);
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la création de l\'utilisateur: ' . $e->getMessage());
        }
    }


    public function update(int $id, array $attributes): User
    {
        try {
            $user = $this->model->findOrFail($id);
            $user->update($attributes);
            return $user;
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la mise à jour de l\'utilisateur: ' . $e->getMessage());
        }
    }


    public function delete(int $id): bool
    {
        try {
            $user = $this->model->findOrFail($id);
            return $user->delete();
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la suppression de l\'utilisateur: ' . $e->getMessage());
        }
    }


    public function search(array $criteria): Collection
    {
        try {
            $query = $this->model->query();
            if (isset($criteria['prenom'])) {
                $query->where('prenom', 'like', '%' . $criteria['prenom'] . '%');
            }
            if (isset($criteria['nom'])) {
                $query->where('nom', 'like', '%' . $criteria['nom'] . '%');
            }
            if (isset($criteria['email'])) {
                $query->where('email', $criteria['email']);
            }
            if (isset($criteria['is_bocked'])) {
                $query->filterByStatus($criteria['is_bocked']);
            }
            return $query->get();
        } catch (\Exception $e) {
            throw new RepositoryException('Erreur lors de la recherche des utilisateurs: ' . $e->getMessage());
        }
    }

    public function createUserForClient(array $userData, int $clientId)
    {
        try {
            DB::beginTransaction();
            $client = $this->clientModel->findOrFail($clientId);

            $user = $this->model->create([
                'prenom' => $userData['prenom'],
                'nom' => $userData['nom'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'role_id' => $userData['role_id'],
                'photo_url' => $userData['photo_url'],
            ]);
            $client->user()->associate($user);
            $client->save();
            DB::commit();
            return $user;
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            throw new ModelNotFoundException("Client with ID $clientId not found.");
        } catch (\Exception $e) {
            DB::rollBack();
            throw new RepositoryException("An error occurred while creating the user: " . $e->getMessage());
        }
    }

    public function findByEmail(string $email)
    {
        try {
            return $this->model->where("email", $email)->firstOrFail();
        } catch (\Exception $e) {
            throw new RepositoryException("An error occurred while creating the user: " . $e->getMessage());
        }
    }

    public function findByColumn(string $column, string $value, string $condition = "=", bool $many = true)
    {
        try {
            $query = $this->model->newQuery(); // Crée une nouvelle instance de requête
            $result = null;
            if ($many) {
                $result =  $query->findByColumn($column, $value, $condition)->get();
            }
            $result = $query->findByColumn($column, $value, $condition)->first();
            // dd($result);
            return $result;
        } catch (\Exception $e) {
            throw new RepositoryException("An error occurred while finding the user with criteria: " . $e->getMessage());
        }
    }
}
