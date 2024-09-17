<?php

namespace App\Http\Controllers;

use App\Facades\UserFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User; 
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        // Appliquer les politiques à toutes les actions sauf 'register'
        $this->authorizeResource(User::class, 'user');
    }

    public function index(Request $request)
    {
        // Vérifie si l'utilisateur peut voir tous les utilisateurs
        $this->authorize('viewAny', User::class);

        $users = UserFacade::getAll([
            "role" => $request->query('role'),
            "active" => $request->query('active')
        ]);
        return new UserCollection($users); // Utilise UserCollection pour formater la liste
    }

    public function show(int $id)
    {
        $user = UserFacade::findById($id);

        // Vérifie si l'utilisateur peut voir l'utilisateur spécifique
        $this->authorize('view', $user);

        return new UserResource($user);
    }

    public function store(StoreUserRequest $request)
    {
        // Vérifie si l'utilisateur peut créer des utilisateurs
        $this->authorize('create', User::class);

        $validatedData = $request->validated();
        $photo = $request->file('photo');
        $user = UserFacade::create($validatedData, $photo);
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, int $id)
    {
        $user = UserFacade::findById($id);

        // Vérifie si l'utilisateur peut mettre à jour l'utilisateur spécifique
        $this->authorize('update', $user);

        $validatedData = $request->validated();
        $photo = $request->file('photo');
        $user = UserFacade::update($id, $validatedData, $photo);
        return new UserResource($user);
    }

    public function destroy(int $id)
    {
        $user = UserFacade::findById($id);

        // Vérifie si l'utilisateur peut supprimer l'utilisateur spécifique
        $this->authorize('delete', $user);

        UserFacade::delete($id);
        return ["message" => "L'utilisateur a été supprimé"];
    }

    public function search(Request $request)
    {
        // Optionnel : Ajouter une vérification d'autorisation pour la recherche si nécessaire
        $this->authorize('viewAny', User::class);

        $criteria = $request->only(['email', 'etat']);
        $users = UserFacade::search($criteria);
        return new UserCollection($users);
    }

    public function register(RegisterRequest $request)
    {
        // Optionnel : Ajouter une vérification d'autorisation pour l'enregistrement si nécessaire
        $this->authorize('create', User::class);

        $user = UserFacade::createUserForClient($request->validated());
        return new UserResource($user);
    }
}
