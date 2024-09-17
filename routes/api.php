<?php

use Carbon\Carbon;
use App\Facades\DetteFacade;
use App\Facades\SmsFacade;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\NotificationController;
use App\Http\Requests\ChangeEtatDemandeRequest;
use App\Http\Resources\DetteCollection;
use App\Repository\Interfaces\ArchiveDetteRepositoryInterface;
use Illuminate\Http\Request;

Route::prefix("v1")->group(function () {

    // Les routes pour l'authentification
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');


    Route::middleware("auth:api")->group(function () {

        // Les routes pour la resource users
        Route::apiResource("users", UserController::class)->except("destroy");
        Route::post("register", [UserController::class, "register"]);

        // Les routes pour la resource paiements
        Route::apiResource("paiements", PaiementController::class);

        // Les routes pour la resource articles
        Route::get('articles', [ArticleController::class, "index"]);
        Route::get('articles/{id}', [ArticleController::class, "show"]);
        Route::put('articles/{id}', [ArticleController::class, "update"]);
        Route::post('articles', [ArticleController::class, "store"]);
        Route::post('articles/libelle', [ArticleController::class, "libelle"]);
        Route::post('articles/stock', [ArticleController::class, 'stock']);
        Route::patch('articles/{id}', [ArticleController::class, 'incrementQuantity']);
        // Route::delete('articles/{id}', [ArticleController::class, 'destroy']);


        // Les routes pour la resource clients
        Route::get("clients", [ClientController::class, "index"]);
        Route::get("clients/{id}", [ClientController::class, "show"]);
        Route::post("clients", [ClientController::class, "store"]);
        Route::post("clients/telephone", [ClientController::class, "telephone"]);
        Route::post("clients/{id}/user", [ClientController::class, "withUser"]);
        Route::post("clients/{id}/dettes", [ClientController::class, "withDette"]);


        // Les routes pour la resource dettes
        Route::apiResource("dettes", DetteController::class)->except("destroy");
        Route::post("dettes/{id}/articles", [DetteController::class, "withArticles"]);
        Route::post("dettes/{id}/paiements", [DetteController::class, "withPaiements"]);
        Route::post("dettes/{id}/addPaiement", [DetteController::class, "addPaiement"]);


        // Les routes pour la resource dettes archivé
        Route::get("archives/dettes", [ArchiveController::class, "getArchive"]);
        Route::get("archives/client/{id}", [ArchiveController::class, "getWithClient"]);
        Route::get("archives/dettes/{id}", [ArchiveController::class, "getById"]);


        // Les routes pour la resource dettes à restaurer
        Route::post("restaure/{id}", [ArchiveController::class, "restaureByDate"]);
        Route::post("restaure/dettes/{id}", [ArchiveController::class, "restaureById"]);
        Route::post("restaure/client/{id}", [ArchiveController::class, "restaureByClient"]);


        // Les routes pour la resource demandes
        Route::get("demandes", [DemandeController::class, "connected"]); // les demandes du client connecter
        Route::post("demandes", [DemandeController::class, "store"]); // enregistrer un demande
        Route::get("demandes/show/{id}", [DemandeController::class, "show"]); // afficher une demande par son id
        Route::get("demandes/all", [DemandeController::class, "index"]); // afficher tous les demandes avec filtre
        Route::get("demandes/{id}/disponible", [DemandeController::class, "disponbile"]); //
        Route::post("demandes/{id}", [DemandeController::class, "changeEtat"]); // modifier l'état de la demande
        Route::post("demandes/{id}/relance", [DemandeController::class, "relance"]); // relance une demande


        // Les routes pour la resource notification
        Route::get("notification/client/relance/{id}", [NotificationController::class, "relanceNotification"]);
        Route::post("notification/client/all", [NotificationController::class, "relanceNotificationAll"]);
        Route::post("notification/client/message", [NotificationController::class, "sendMessageNotificationAll"]);
        Route::post("notification/client/{id}/message", [NotificationController::class, "sendMessageNotification"]);

        // Les routes pour la resource notication du client connecter connecter
        Route::get("notification/client/nonlue", [NotificationController::class, "getNotificationsNonLue"]);
        Route::get("notification/client/lue", [NotificationController::class, "getNotificationsLue"]);
        Route::patch("notification/client/lire/{id}", [NotificationController::class, "marquerNotificationLue"]);

        // Les routes pour la resource notification de demande
        Route::get("demandes/notifications/client", [NotificationController::class, "notificationResponseDemande"]); // Voir les notifications de reponse suite a une demande de dette
        Route::get("demandes/notifications/client/{id}", [NotificationController::class, "notificationResponseDemandeClient"]); // Voir les notifications de reponse suite a une demande de dette d'un client spécifique
        Route::get("demandes/notifications", [NotificationController::class, "notificationDemande"]); // Voir les notification des demandes de dettes soummises
    });
});


Route::post("/test", function(Request $request) {
    $request->validate([
        "etat"=>"required",
    ]);
    dd($request->all());
});


