<?php

namespace App\Services;

use App\Models\User;
use App\Events\UserCreatedEvent;
use Illuminate\Http\UploadedFile;
use App\Facades\LocalStorageFacade;
use App\Exceptions\ServiceException;
use Illuminate\Database\Eloquent\Collection;
use App\Services\Interfaces\RoleServiceInterface;
use App\Services\Interfaces\UserServiceInterface;
use App\Repository\Interfaces\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;
    protected $roleService;
    private const DEFAULT_PHOTO = "/storage/images/avatar.jpg";

    public function __construct(UserRepositoryInterface $userRepository, RoleServiceInterface $roleService) // Utilisation du bon service
    {
        $this->userRepository = $userRepository;
        $this->roleService = $roleService;
    }

    public function getAll(array $filters = []): Collection
    {
        try {
            if (isset($filters["role"]))
                $filters["role"] = $this->roleService->getId($filters["role"]);
            return $this->userRepository->all($filters);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
        }
    }

    public function findById(int $id): ?User
    {
        try {
            return $this->userRepository->find($id);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la récupération de l'utilisateur avec l'ID $id: " . $e->getMessage());
        }
    }

    public function create(array $data, ?UploadedFile $photo = null): User
    {
        try {
            if ($photo) {
                $data["photo_url"] = LocalStorageFacade::uploadFile($photo);
            } else {
                $data["photo_url"] = self::DEFAULT_PHOTO;
            }

            $role_id = $this->roleService->getId($data["role"]);
            if (!$role_id)
                throw new ServiceException("Erreur le role {$data["role"]} n'existe pas dans le système.");

            $data["role_id"] = $role_id;
            $user =  $this->userRepository->create($data);
            return $user;
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data, ?UploadedFile $photo = null): User
    {
        try {
            if ($photo) {
                $data["photo_url"] = LocalStorageFacade::uploadFile($photo);
            } else {
                $data["photo_url"] = self::DEFAULT_PHOTO;
            }
            return $this->userRepository->update($id, $data);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la mise à jour de l'utilisateur avec l'ID $id: " . $e->getMessage());
        }
    }

    public function delete(int $id): bool
    {
        try {
            return $this->userRepository->delete($id);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la suppression de l'utilisateur avec l'ID $id: " . $e->getMessage());
        }
    }

    public function search(array $criteria): Collection
    {
        try {
            return $this->userRepository->search($criteria);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la recherche des utilisateurs: " . $e->getMessage());
        }
    }

    public function createUserForClient(array $data)
    {
        try {
            if ($data["photo"]) {
                $data["photo_url"] = LocalStorageFacade::uploadFile($data["photo"]);
            } else {
                $data["photo_url"] = self::DEFAULT_PHOTO;
            }
            $clientId = $data["client_id"];
            $role_id = $this->roleService->getId("client");
            $data["role_id"] = $role_id;
            
            unset($data["client_id"]);
            unset($data["photo"]);
            $user = $this->userRepository->createUserForClient($data, $clientId);
            event(new UserCreatedEvent($user));
            return $user;
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la création compte d(utilisateur pour un client: " . $e->getMessage());
        }
    }

    public function emailExist(string $email)
    {
        try {
            return $this->userRepository->findByEmail($email);
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la rechercher de l'utilisateur avec ce mail $email: " . $e->getMessage());
        }
    }

    public function findByColumn(string $column, string $value, string $condition = "=", bool $many = true)
    {
        try {
            $result = $this->userRepository->findByColumn($column, $value, $condition, $many);
            return $result;
        } catch (\Exception $e) {
            throw new ServiceException("Erreur lors de la rechercher de l'utilisateur avec cette critère: " . $e->getMessage());
        }
    }
}
