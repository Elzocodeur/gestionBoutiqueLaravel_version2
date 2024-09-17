<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Rules\TelephoneRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientDetteResource;
use App\Facades\ClientFacade;
use App\Http\Resources\ClientCollection;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if the user has permission to view any client
        $this->authorize('viewAny', Client::class);

        if ($request->has("user_id")) {
            return ClientFacade::findByUserId($request->query("user_id"));
        }

        return new ClientCollection(ClientFacade::getAllCLient($request->only(["comptes", "active", "include"])));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request)
    {
        // Check if the user has permission to create a client
        $this->authorize('create', Client::class);

        return new ClientResource(ClientFacade::create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $client = ClientFacade::find($id);

        // Check if the user has permission to view the client
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, int $id)
    {
        $client = ClientFacade::find($id);

        // Check if the user has permission to update the client
        $this->authorize('update', $client);

        return new ClientResource(ClientFacade::update($id, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $client = ClientFacade::find($id);

        // Check if the user has permission to delete the client
        $this->authorize('delete', $client);

        // Implement delete logic if needed
        ClientFacade::delete($id);

        return response()->json(['message' => 'Client deleted successfully']);
    }

    /**
     * Search client by telephone.
     */
    public function telephone(Request $request)
    {
        $request->validate(['telephone' => ['required', 'string', new TelephoneRule()]]);

        // Check if the user has permission to view any client
        $this->authorize('viewAny', Client::class);

        return new ClientResource(ClientFacade::searchByTelephone($request->post('telephone')));
    }

    /**
     * Get client with associated user.
     */
    public function withUser(int $id)
    {
        // Check if the user has permission to view the client
        $client = ClientFacade::getClientWithUser($id);
        $this->authorize('view', $client);

        return new ClientResource($client);
    }

    /**
     * Get client with associated debt.
     */
    public function withDette(int $id)
    {
        // Check if the user has permission to view the client
        $client = ClientFacade::getClientWithDette($id);
        $this->authorize('view', $client);

        return new ClientDetteResource($client);
    }
}
