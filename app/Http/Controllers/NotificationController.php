<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\NotificationCollection;
use App\Services\Interfaces\NotificationServiceInterface;

class NotificationController extends Controller
{
    public function __construct(protected NotificationServiceInterface $notificationService) {}

    public function relanceNotification(int $clientId)
    {
        $this->authorized('relanceNotification');

        $notification = $this->notificationService->relanceNotification($clientId);
        return new NotificationResource($notification);
    }

    public function relanceNotificationAll()
    {
        $this->authorized('relanceNotificationAll');

        $result = $this->notificationService->relanceNotificationAll();
        if ($result) {
            return ["message" => "Les notifications ont été envoyées avec succès"];
        }
        throw new Exception("Les notifications n'ont pas été envoyées");
    }

    public function sendMessageNotification(int $clientId, Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $this->authorized('sendMessageNotification');

        $notification = $this->notificationService->sendMessageNotification($clientId, $request->input('message'));
        return new NotificationResource($notification);
    }

    public function sendMessageNotificationAll(Request $request)
    {
        $request->validate(['message' => 'required|string']);

        $this->authorized('sendMessageNotificationAll');

        $result = $this->notificationService->sendMessageNotificationAll($request->input('message'));
        if ($result) {
            return ["message" => "Les notifications de message ont été envoyées avec succès"];
        }
        throw new Exception("Les notifications de message n'ont pas été envoyées");
    }

    public function getNotificationsNonLue()
    {
        $this->authorized('getNotificationsNonLue');

        $notifications = $this->notificationService->getNotificationsNonLue(Auth::id());
        return new NotificationCollection($notifications);
    }

    public function getNotificationsLue()
    {
        $this->authorized('getNotificationsLue');

        $notifications = $this->notificationService->getNotificationsLue(Auth::id());
        return new NotificationCollection($notifications);
    }

    public function marquerNotificationLue(string $id)
    {
        $this->authorized('marquerNotificationLue');

        $notification = $this->notificationService->marquerNotificationLue(Auth::id(), $id);
        return new NotificationResource($notification);
    }

    public function notificationResponseDemande()
    {
        $this->authorized('notificationResponseDemande');

        return new NotificationCollection(
            $this->notificationService->getNotificationResponseDemande(Auth::id())
        );
    }

    public function notificationResponseDemandeClient($id)
    {
        $this->authorized('notificationResponseDemandeClient');

        return new NotificationCollection(
            $this->notificationService->getNotificationResponseDemande($id)
        );
    }

    public function notificationDemande()
    {
        $this->authorized('notificationDemande');

        return new NotificationCollection(
            $this->notificationService->notificationDemandeSoumis(Auth::id())
        );
    }

    private function authorized(string $action)
    {
        $user = Auth::user();
        if ($user->role->libelle === "admin" || $user->role->libelle === "boutiquier") {
            $canPerform = in_array($action, [
                'relanceNotification',
                'relanceNotificationAll',
                'sendMessageNotification',
                'notificationDemande',
                'sendMessageNotificationAll'
            ]);
        } elseif ($user->role->libelle === "client") {
            $canPerform = in_array($action, [
                'getNotificationsNonLue',
                'getNotificationsLue',
                'marquerNotificationLue',
                'notificationResponseDemande',
                'notificationResponseDemandeClient',
            ]);
        } else {
            $canPerform = false;
        }
        if (!$canPerform) {
            Log::info("User ID {$user->id} role {$user->role->libelle} tried to perform action '{$action}' and was denied.");
        }
        if (!$canPerform)
            throw new Exception("This action is unautorized");
    }
}
