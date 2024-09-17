<?php

namespace App\Observers;

use App\Models\Client;
use App\Facades\RoleFacade;
use App\Facades\UserFacade;
use App\Models\User;
use App\Repository\Interfaces\UserRepositoryInterface;

class ClientObserver
{

    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        $data = request()->all();
        if (isset($data["user"])) {
            $userData = $data["user"];
            $userData["role"] = "client";
            $photo = $userData["photo"];
            unset($userData["photo"]);
            $user = UserFacade::create($userData, $photo);
            $user->client()->save($client);
        }
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
