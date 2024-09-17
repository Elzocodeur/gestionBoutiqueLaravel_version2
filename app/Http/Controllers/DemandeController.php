<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Enums\DemandeEnum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DemandeResource;
use App\Http\Resources\DemandeCollection;
use App\Http\Requests\StoreDemandeRequest;
use App\Http\Requests\ChangeEtatDemandeRequest;
use App\Services\Interfaces\DemandeServiceInterface;
use Exception;

class DemandeController extends Controller
{
    protected $demandeService;

    public function __construct(DemandeServiceInterface $demandeService)
    {
        $this->demandeService = $demandeService;
    }

    /**
     * Afficher les demandes du client connecté
     */
    public function connected(Request $request)
    {
        // Autoriser seulement les clients
        $this->authorize('viewOwnDemandes', Demande::class);

        $clientId = Auth::id();
        $demandes = $this->demandeService->getDemandeByClientId($clientId, $request->input("etat"));
        return new DemandeCollection($demandes);
    }

    /**
     * Créer une nouvelle demande
     */
    public function store(StoreDemandeRequest $request)
    {
        $this->authorize('create', Demande::class);

        $validated = $request->validated();
        $demandeData = [
            'montant' => $validated['montant'],
            'client_id' => Auth::user()->client->id,
            'date' => now(),
        ];
        $articles = $validated['articles'] ?? [];
        $demande = $this->demandeService->createDemande($demandeData, $articles);
        if ($demande instanceof Demande)
            return new DemandeResource($demande);
        return ["data" => $demande, "status" => 404];
    }

    /**
     * Afficher une demande par son ID
     */
    public function show($id)
    {
        $demande = $this->demandeService->getDemandeById($id);
        $this->authorize('view', $demande);  // Seul un boutiquier peut voir une demande spécifique
        if ($demande)
            return new DemandeResource($demande);
        return ["message" => "Demande not found", "status" => 404];
    }

    /**
     * Afficher toutes les demandes avec des filtres optionnels
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Demande::class);
        return new DemandeCollection(
            $this->demandeService->getDemandeByEtat($request->input("etat"))
        );
    }

    /**
     * Afficher les quantités de produits disponibles pour satisfaire la demande
     */
    public function disponbile($id)
    {
        $this->authorize('disponible', Demande::class);
        $result = $this->demandeService->demandeNonSatisfait($id);
        return [
            "data" => [
                "demande" => new DemandeResource($result["demande"]),
                "article_disponible" => $result["articles"]
            ]
        ];
    }

    /**
     * Modifier l'état d'une demande
     */
    public function changeEtat(ChangeEtatDemandeRequest $request, $id)
    {
        // Autorisation d'effectuer cette action
        // $this->authorize('changeEtat', Demande::class);

        // Récupérer les valeurs validées depuis la requête
        $newEtat = $request->post('etat') ?? DemandeEnum::EN_COURS->value;
        $motif = $request->post('motif'); // Le motif sera présent si l'état est 'annuler'

        // Changer l'état de la demande en utilisant le service
        $this->demandeService->changeDemandeEtat($id, $newEtat, $motif);

        // Retourner un message de succès
        return ['message' => 'Demande state updated successfully'];
    }


    public function relance($id)
    {
        $demande = $this->demandeService->getDemandeById($id);

        $this->authorize("relancer", $demande);

        $nouvelleDemande = $this->demandeService->relanceDemande($id, Auth::user()->client->id);
        if ($nouvelleDemande instanceof Demande)
            return [
                "message" => 'Demande relancée avec succès',
                "data" => new DemandeResource($nouvelleDemande)
            ];
        return ["data" => $demande, "status" => 404];
    }


}
