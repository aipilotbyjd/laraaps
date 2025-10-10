<?php

namespace App\Services\Credential;

use App\Models\Credential;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class CredentialService
{
    public function getCredentialsByOrg(string $orgId)
    {
        return Credential::where('org_id', $orgId)->get();
    }

    public function createCredential(array $data, string $orgId, string $userId): Credential
    {
        $data['id'] = Str::uuid();
        $data['org_id'] = $orgId;
        $data['user_id'] = $userId;
        $data['encrypted_data'] = Crypt::encryptString(json_encode($data['data']));
        unset($data['data']);

        return Credential::create($data);
    }

    public function getCredential(string $id): ?Credential
    {
        $credential = Credential::find($id);

        if ($credential) {
            $credential->data = json_decode(Crypt::decryptString($credential->encrypted_data), true);
        }

        return $credential;
    }

    public function updateCredential(string $id, array $data): ?Credential
    {
        $credential = Credential::find($id);

        if (! $credential) {
            return null;
        }

        if (isset($data['data'])) {
            $data['encrypted_data'] = Crypt::encryptString(json_encode($data['data']));
            unset($data['data']);
        }

        $credential->update($data);

        return $credential;
    }

    public function deleteCredential(string $id): bool
    {
        $credential = Credential::find($id);

        if (! $credential) {
            return false;
        }

        return $credential->delete();
    }

    // Mocked methods for now

    public function getTypes()
    {
        return [];
    }

    public function getTypeSchema(string $type)
    {
        return [];
    }

    public function test(string $id)
    {
        return ['message' => 'Credential test initiated.'];
    }

    public function getTestStatus(string $id)
    {
        return ['status' => 'success'];
    }

    public function oauthAuthorize(string $id)
    {
        return ['message' => 'OAuth authorization initiated.'];
    }

    public function oauthCallback(string $id, array $data)
    {
        return ['message' => 'OAuth callback handled.'];
    }

    public function oauthRefresh(string $id)
    {
        return ['message' => 'OAuth token refreshed.'];
    }

    public function getShares(string $id)
    {
        return [];
    }

    public function createShare(string $id, string $userId)
    {
        return ['message' => 'Credential shared.'];
    }

    public function deleteShare(string $id, string $userId)
    {
        return ['message' => 'Credential unshared.'];
    }

    public function getUsage(string $id)
    {
        return [];
    }

    public function getWorkflows(string $id)
    {
        return [];
    }
}
