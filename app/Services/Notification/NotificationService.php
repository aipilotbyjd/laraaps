<?php

namespace App\Services\Notification;

use App\Models\Notification;

class NotificationService
{
    public function getNotifications(string $userId)
    {
        return Notification::where('user_id', $userId)->get();
    }

    public function markAsRead(string $id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->read_at = now();
            $notification->save();
        }

        return $notification;
    }

    public function markAllAsRead(string $userId)
    {
        return Notification::where('user_id', $userId)->update(['read_at' => now()]);
    }

    public function deleteNotification(string $id)
    {
        return Notification::destroy($id);
    }

    // Mocked methods for now

    public function getSettings(string $userId)
    {
        return [];
    }

    public function updateSettings(string $userId, array $settings)
    {
        return ['message' => 'Settings updated.'];
    }

    public function getChannels(string $userId)
    {
        return [];
    }

    public function createChannel(string $userId, array $channel)
    {
        return ['message' => 'Channel created.'];
    }

    public function deleteChannel(string $id)
    {
        return ['message' => 'Channel deleted.'];
    }
}
