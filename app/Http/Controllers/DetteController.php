<?php

namespace App\Http\Controllers;

use App\Facades\ArchiveDatabaseFacade;
use App\Facades\ArchiveDetteFacade;
use App\Models\Dette;
use App\Http\Resources\DetteResource;
use App\Http\Resources\DetteCollection;
use App\Http\Requests\StoreDetteRequest;
use App\Http\Requests\UpdateDetteRequest;
use App\Facades\DetteFacade;
use App\Facades\PaiementFacade;
use App\Http\Resources\PaiementResource;
use Illuminate\Http\Request;

class DetteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Dette::class);

        $includeRelations = request()->query('include', '');
        $withRelations = !empty($includeRelations) ? explode(',', $includeRelations) : [];
        $statut = request()->query('statut', null);

        return new DetteCollection(DetteFacade::getAllDettes([
            'include' => $withRelations,
            'statut' => $statut,
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDetteRequest $request)
    {
        $this->authorize('create', Dette::class);

        $validated = $request->validated();
        $detteData = [
            'montant' => $validated['montant'],
            'client_id' => $validated['clientId'],
            'echeance' => $validated['clientId'],
            'date' => now(),
        ];
        $articles = $validated['articles'] ?? [];
        $paiement = $validated["paiement"] ?? null;
        $dette = DetteFacade::createDette($detteData, $articles, $paiement);
        if ($dette instanceof Dette)
            return new DetteResource($dette);
        return ["data" => $dette, "status" => 404];
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $includeRelations = request()->query('include', '');
        $withRelations = !empty($includeRelations) ? explode(',', $includeRelations) : [];
        $dette = DetteFacade::getDetteById($id, ["include" => $withRelations]);
        $this->authorize('view', $dette);
        return new DetteResource($dette);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDetteRequest $request, int $id)
    {
        $dette = DetteFacade::getDetteById($id);

        $this->authorize('update', $dette);

        $validated = $request->validated();
        $detteData = [
            'montant' => $validated['montant'],
            'client_id' => $validated['client_id'],
            'date' => now(),
        ];
        $articles = $validated['articles'] ?? [];
        $paiement = $validated['paiement'] ?? null;
        return new DetteResource(DetteFacade::updateDette($id, $id, $detteData, $articles, $paiement));
    }


    public function withArticles(int $id)
    {
        $dette = DetteFacade::getDetteWithRelation($id, "articles");
        $this->authorize('view', $dette);
        return new DetteResource($dette);
    }

    public function withPaiements(int $id)
    {
        $dette = DetteFacade::getDetteWithRelation($id, "paiements");
        $this->authorize('view', $dette);
        return new DetteResource($dette);
    }

    public function addPaiement(Request $request, int $id)
    {
        $dette = DetteFacade::getDetteById($id);
        $this->authorize('update', $dette);
        $request->validate(["montant" => 'required|numeric|min:1']);
        return new PaiementResource(PaiementFacade::createPaiement([
            "montant" => $request->post("montant"),
            "dette_id" => $id
        ]));
    }


    public function getArchive(Request $request)
    {
        $clientId = $request->query('client');
        $date = $request->query('date') ? new \DateTime($request->query('date')) : null;
        $detteArchiving = [];

        if ($date == null && $clientId == null) {
            $detteArchiving = ArchiveDetteFacade::getAll();
        } else {
            $detteArchiving = ArchiveDatabaseFacade::getAll($clientId, $date);
        }
        return new DetteCollection($detteArchiving);
    }
}
