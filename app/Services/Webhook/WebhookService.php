<?php

namespace App\Services\Webhook;

use App\Models\Webhook;
use Illuminate\Support\Str;

class WebhookService
{
    public function getWebhooksByOrg(string $orgId)
    {
        return Webhook::where('org_id', $orgId)->get();
    }

    public function createWebhook(array $data, string $orgId): Webhook
    {
        $data['id'] = Str::uuid();
        $data['org_id'] = $orgId;

        return Webhook::create($data);
    }

    public function getWebhook(string $id): ?Webhook
    {
        return Webhook::find($id);
    }

    public function updateWebhook(string $id, array $data): ?Webhook
    {
        $webhook = Webhook::find($id);

        if (! $webhook) {
            return null;
        }

        $webhook->update($data);

        return $webhook;
    }

    public function deleteWebhook(string $id): bool
    {
        $webhook = Webhook::find($id);

        if (! $webhook) {
            return false;
        }

        return $webhook->delete();
    }

    public function handleIncomingWebhook(string $workflowId, string $path, array $data)
    {
        // This would trigger a workflow execution
        return ['message' => 'Webhook received.'];
    }

    // Mocked methods for now

    public function test(string $id)
    {
        return ['message' => 'Webhook test initiated.'];
    }

    public function getTestUrl(string $id)
    {
        return ['url' => url("/api/v1/webhook/{$id}/test")];
    }

    public function getLogs(string $id)
    {
        return [];
    }

    public function getStats(string $id)
    {
        return [];
    }

    public function regenerateToken(string $id)
    {
        return ['message' => 'Token regenerated.'];
    }

    public function updateIpWhitelist(string $id, array $ips)
    {
        return ['message' => 'IP whitelist updated.'];
    }
}
