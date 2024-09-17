<?php

namespace App\Services\Interfaces;

use App\Exceptions\ServiceException;


interface NotificationServiceInterface
{
    /**
     * Relance une notification de dette pour un client spécifique.
     *
     * @param int $id
     * @return \Illuminate\Notifications\DatabaseNotification
     * @throws ServiceException
     */
    public function relanceNotification(int $id);

    /**
     * Relance les notifications de dette pour tous les clients.
     *
     * @return bool
     * @throws ServiceException
     */
    public function relanceNotificationAll(): bool;

    /**
     * Envoie une notification de message à un client spécifique.
     *
     * @param int $clientId
     * @param string $message
     * @return \Illuminate\Notifications\DatabaseNotification
     * @throws ServiceException
     */
    public function sendMessageNotification(int $clientId, string $message);

    /**
     * Envoie une notification de message à tous les clients avec une dette.
     *
     * @param string $message
     * @return bool
     * @throws ServiceException
     */
    public function sendMessageNotificationAll(string $message): bool;

    /**
     * Récupère les notifications non lues d'un client spécifique.
     *
     * @param int $clientId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws ServiceException
     */
    public function getNotificationsNonLue(int $clientId);

    /**
     * Récupère les notifications lues d'un client spécifique.
     *
     * @param int $clientId
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws ServiceException
     */
    public function getNotificationsLue(int $clientId);

    /**
     * Récupère une notification spécifique et la marque comme lue.
     *
     * @param int $clientId
     * @param int $notificationId
     * @return \Illuminate\Notifications\DatabaseNotification
     * @throws ServiceException
     */
    public function marquerNotificationLue(int $clientId, string $notificationId);

    public function getNotificationResponseDemande(int $clientId);

    public function notificationDemandeSoumis(int $id);
}
