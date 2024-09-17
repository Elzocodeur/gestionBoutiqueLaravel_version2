<?php

namespace App\Services;

use App\Exceptions\RepositoryException;
use App\Exceptions\ServiceException;
use App\Facades\SmsFacade;
use App\Notifications\DebtSummaryNotification;
use App\Notifications\MessageNotification;
use App\Repository\Interfaces\ClientRepositoryInterface;
use App\Repository\Interfaces\UserRepositoryInterface;
use App\Services\Interfaces\NotificationServiceInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationService implements NotificationServiceInterface
{

    public function __construct(protected ClientRepositoryInterface $clientRepository, protected UserRepositoryInterface $userRepository) {}

    public function relanceNotification($id)
    {
        try {
            $client = $this->clientRepository->findById($id);
            if (!$client) {
                throw new ServiceException("Client not found with ID: $id");
            }
            $cumule = $client->currentDebt();
            if ($cumule <= 0) {
                throw new ServiceException("Le client n'a pas de dette à notifier.");
            }

            $formattedAmount = number_format($cumule, 0, ',', ' ');
            $message = "Bonjour, chez Gestion Boutique ODC.\nNous vous informons que vous avez un cumul de dette de $formattedAmount Fcfa à payer.";
            SmsFacade::sendSms($client->telephone, $message);

            $client->notify(new DebtSummaryNotification($cumule));

            return $client->notifications()->latest()->first();
        } catch (ServiceException | RepositoryException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Impossible de faire la relance de notification");
        }
    }

    public function relanceNotificationAll(): bool
    {
        try {
            foreach ($this->clientRepository->getHaveDette() as $client) {
                $totalDue = $client->currentDebt();
                $client->notify(new DebtSummaryNotification($totalDue));
                $message = "Rappel : Vous avez un montant total de $totalDue dû pour cette semaine.";
                SmsFacade::sendSms($client->telephone, $message);
            }
            return true;
        } catch (ServiceException | RepositoryException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e);
            throw new ServiceException("Impossible de faire la relance de notification");
        }
    }

    public function sendMessageNotification(int $clientId, string $message)
    {
        try {
            $client = $this->clientRepository->findById($clientId);
            if (!$client) {
                throw new ServiceException("Client not found with ID: $clientId");
            }
            $client->notify(new MessageNotification($message, $client->telephone));
            return $client->notifications()->latest()->first();
        } catch (ServiceException | RepositoryException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Impossible d'envoyer la notification : " . $e->getMessage());
        }
    }

    public function sendMessageNotificationAll(string $message): bool
    {
        try {
            $clients = $this->clientRepository->getHaveDette();
            foreach ($clients as $client) {
                $this->sendMessageNotification($client->id, $message);
            }
            return true;
        } catch (ServiceException | RepositoryException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Impossible d'envoyer la notification : " . $e->getMessage());
        }
    }

    public function getNotificationsNonLue(int $clientId)
    {
        try {
            $client = $this->clientRepository->findById($clientId);
            if (!$client) {
                throw new ServiceException("Client not found with ID: $clientId");
            }

            return $client->unreadNotifications;
        } catch (RepositoryException | ServiceException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Impossible de récupérer les notifications non lues : " . $e->getMessage());
        }
    }

    public function getNotificationsLue(int $clientId)
    {
        try {
            $client = $this->clientRepository->findById($clientId);
            if (!$client) {
                throw new ServiceException("Client not found with ID: $clientId");
            }

            return $client->notifications()->whereNotNull('read_at')->get();
        } catch (RepositoryException | ServiceException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Impossible de récupérer les notifications lues : " . $e->getMessage());
        }
    }

    public function marquerNotificationLue(int $clientId, string $notificationId)
    {
        try {
            $user = $this->userRepository->find($clientId);
            if (!$user)
                throw new ServiceException("User not found with ID: $clientId");
            if (!$user->isClient())
                throw new ServiceException("Vous n'est pas un client");
            $client = $user->client;

            $notification = $client->notifications->where('id', $notificationId)->first();
            if (!$notification) {
                throw new ServiceException("Notification not found with ID: $notificationId");
            }

            if ($notification->read_at === null) {
                $notification->markAsRead();
            }
            return $notification;
        } catch (RepositoryException | ServiceException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            throw new ServiceException("Impossible de marquer la notification comme lue : " . $e->getMessage());
        }
    }

    public function getNotificationResponseDemande(int $clientId)
    {
        try {
            $client = $this->clientRepository->findById($clientId);
            if (!$client) {
                throw new ServiceException("Client not found with ID: $clientId");
            }
            $demandes = $client->demandes;
            if ($demandes->isEmpty()) {
                throw new ServiceException("Aucune demande trouvée pour ce client.");
            }
            $notifications = [];
            foreach ($demandes as $demande) {
                $responseNotifications = $demande->notifications()->whereNull('read_at')->get();
                if ($responseNotifications->isNotEmpty()) {
                    $notification[] = $responseNotifications;
                    // $notifications = array_merge($notifications, $responseNotifications->toArray());
                }
            }
            if (empty($notifications)) {
                throw new ServiceException("Aucune notification de réponse trouvée pour les demandes de ce client.");
            }
            return $notifications;
        } catch (RepositoryException | ServiceException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Impossible de récupérer les notifications de réponse aux demandes : ");
        }
    }

    public function notificationDemandeSoumis(int $id)
    {
        try {
            $user = $this->userRepository->find($id);
            if (!$user) {
                throw new ServiceException("L'utilisateur non trouvé avec l'ID : $id");
            }
            if (!$user->isBoutiquier())
                throw new ServiceException("Cet utilisateur n'est pas un boutiquier.");

            $notifications = $user->notifications()->get();
            if (!$notifications) {
                throw new ServiceException("Aucune demande de dette soumise par ce boutiquier.");
            }
            return $notifications;
        } catch (RepositoryException | ServiceException $e) {
            throw new ServiceException($e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            throw new ServiceException("Impossible de récupérer les notifications de réponse aux demandes.");
        }
    }
}
