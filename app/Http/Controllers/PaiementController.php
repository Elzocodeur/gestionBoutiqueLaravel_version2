<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Http\Resources\PaiementResource;
use App\Http\Requests\StorePaiementRequest;
use App\Http\Requests\UpdatePaiementRequest;
use App\Services\Interfaces\PaiementServiceInterface;

class PaiementController extends Controller
{
    protected $paiementService;

    public function __construct(PaiementServiceInterface $paiementService)
    {
        $this->paiementService = $paiementService;
    }

    public function index()
    {
        $this->authorize('viewAny', Paiement::class);

        $filters = request()->only(['client_id', 'dette_id', 'start_date', 'end_date', 'montant_min', 'montant_max']);
        $paiements = $this->paiementService->getAllPaiements($filters);
        return PaiementResource::collection($paiements);
    }

    public function show($id)
    {
        $paiement = $this->paiementService->getPaiementById($id, request()->only(["include"]));

        if ($paiement) {
            $this->authorize('view', $paiement);
            return new PaiementResource($paiement);
        }

        return ['message' => 'Paiement not found', 'status' => 404, 'data' => null];
    }

    public function store(StorePaiementRequest $request)
    {
        $this->authorize('create', Paiement::class);
        $paiement = $this->paiementService->createPaiement($request->validated());

        return new PaiementResource($paiement);
    }

    public function update(UpdatePaiementRequest $request, $id)
    {
        $paiement = $this->paiementService->getPaiementById($id);

        if ($paiement) {
            $this->authorize('update', $paiement);
            $paiement = $this->paiementService->updatePaiement($id, $request->validated());
            return new PaiementResource($paiement);
        }
        return ['message' => 'Paiement not found', 'status' => 404, 'data' => null];
    }

    public function destroy($id)
    {
        $paiement = $this->paiementService->getPaiementById($id);

        if ($paiement) {
            $this->authorize('delete', $paiement);
            $deleted = $this->paiementService->deletePaiement($id);

            if ($deleted) {
                return response()->json(['message' => 'Paiement deleted successfully'], 200);
            }
        }
        return ['message' => 'Paiement not found', 'status' => 404, 'data' => null];
    }
}
