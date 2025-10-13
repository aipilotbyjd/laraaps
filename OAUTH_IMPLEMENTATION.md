# OAuth 2.0 Implementation Guide

This document describes the complete OAuth 2.0 implementation for credential management.

## Components

### 1. CredentialService OAuth Methods

#### `oauthAuthorize(string $credentialId)`
**Purpose:** Generate OAuth authorization URL

**Flow:**
1. Validates credential exists and is OAuth2 type
2. Retrieves client_id, authorization_url from credential data
3. Generates random 40-character state token
4. Caches state token for 10 minutes (CSRF protection)
5. Builds authorization URL with query parameters:
   - `client_id`
   - `redirect_uri` (points to callback endpoint)
   - `response_type=code`
   - `scope` (from credential data)
   - `state` (CSRF token)
6. Returns authorization URL for user redirect

**Usage:**
```php
$result = $credentialService->oauthAuthorize($credentialId);
// Returns: ['status' => 'success', 'authorization_url' => '...', 'state' => '...']
```

#### `oauthCallback(string $credentialId, array $data)`
**Purpose:** Handle OAuth callback and exchange code for tokens

**Flow:**
1. Validates credential exists
2. Extracts `code` and `state` from callback data
3. Verifies state matches cached value (CSRF protection)
4. Deletes cached state (one-time use)
5. Makes POST request to token endpoint with:
   - `grant_type=authorization_code`
   - `code` (from OAuth provider)
   - `redirect_uri`
   - `client_id`
   - `client_secret`
6. Extracts tokens from response:
   - `access_token`
   - `refresh_token`
   - `expires_in` (converted to timestamp)
7. Updates credential with new tokens
8. Returns success with expiry time

**Usage:**
```php
$result = $credentialService->oauthCallback($credentialId, [
    'code' => $_GET['code'],
    'state' => $_GET['state']
]);
```

#### `oauthRefresh(string $credentialId)`
**Purpose:** Refresh expired OAuth access token

**Flow:**
1. Validates credential exists and is OAuth2 type
2. Retrieves refresh_token, token_url, client credentials
3. Makes POST request to token endpoint with:
   - `grant_type=refresh_token`
   - `refresh_token`
   - `client_id`
   - `client_secret`
4. Extracts new tokens:
   - Updates `access_token`
   - Updates `refresh_token` (if provided)
   - Updates `expires_at` timestamp
5. Saves updated credential
6. Returns success with new expiry time

**Usage:**
```php
$result = $credentialService->oauthRefresh($credentialId);
// Returns: ['status' => 'success', 'expires_at' => '2024-...']
```

### 2. RefreshOAuthTokenJob

**Purpose:** Background job for automatic token refresh

**Logic:**
1. Finds credential by ID
2. Validates credential is OAuth2 type
3. Decrypts credential data to check expiry
4. If token expires in less than 10 minutes:
   - Calls `oauthRefresh()`
   - Logs success/failure
5. Otherwise skips refresh

**Dispatch:**
```php
RefreshOAuthTokenJob::dispatch($credentialId);
```

### 3. RefreshExpiredOAuthTokens Command

**Purpose:** Scheduled command to refresh tokens expiring soon

**Flow:**
1. Queries all OAuth2 credentials
2. For each credential:
   - Decrypts credential data
   - Checks if token expires within 1 hour
   - If yes, dispatches `RefreshOAuthTokenJob`
3. Reports count of queued refreshes

**Schedule (add to Kernel):**
```php
$schedule->command('oauth:refresh-tokens')->hourly();
```

**Manual execution:**
```bash
php artisan oauth:refresh-tokens
```

## Setup Guide

### 1. Credential Data Structure

OAuth2 credentials require these fields:

```json
{
  "type": "oauth2",
  "client_id": "your-client-id",
  "client_secret": "your-client-secret",
  "authorization_url": "https://provider.com/oauth/authorize",
  "token_url": "https://provider.com/oauth/token",
  "scope": "read write",
  "access_token": null,
  "refresh_token": null,
  "expires_at": null
}
```

### 2. Routes

Add to `routes/api.php`:

```php
// OAuth routes already defined in CredentialController:
Route::get('credentials/{id}/oauth/authorize', 'oauthAuthorize');
Route::get('credentials/{id}/oauth/callback', 'oauthCallback');
Route::post('credentials/{id}/oauth/refresh', 'oauthRefresh');
```

### 3. Scheduler Setup

Add to `routes/console.php` or `app/Console/Kernel.php`:

```php
$schedule->command('oauth:refresh-tokens')->hourly();
```

### 4. Queue Configuration

Ensure queue worker is running:

```bash
php artisan queue:work
```

## OAuth Flow Examples

### Example 1: GitHub OAuth

```php
// 1. Create credential
$credential = $credentialService->createCredential([
    'name' => 'GitHub OAuth',
    'type' => 'oauth2',
    'data' => [
        'client_id' => 'github_client_id',
        'client_secret' => 'github_client_secret',
        'authorization_url' => 'https://github.com/login/oauth/authorize',
        'token_url' => 'https://github.com/login/oauth/access_token',
        'scope' => 'repo user',
    ]
], $orgId, $userId);

// 2. Get authorization URL
$auth = $credentialService->oauthAuthorize($credential->id);
// Redirect user to: $auth['authorization_url']

// 3. Handle callback (automatic via controller)
// GET /api/v1/credentials/{id}/oauth/callback?code=...&state=...

// 4. Use credential in nodes
// Access token automatically retrieved from credential data
```

### Example 2: Google OAuth

```php
$credential = $credentialService->createCredential([
    'name' => 'Google OAuth',
    'type' => 'oauth2',
    'data' => [
        'client_id' => 'google_client_id',
        'client_secret' => 'google_client_secret',
        'authorization_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
        'token_url' => 'https://oauth2.googleapis.com/token',
        'scope' => 'https://www.googleapis.com/auth/drive.readonly',
    ]
], $orgId, $userId);
```

## Security Features

### CSRF Protection
- State parameter generated with `Str::random(40)`
- Cached for 10 minutes only
- Validated on callback
- Deleted after one use

### Token Security
- All tokens encrypted in database
- Only decrypted when needed
- Never logged or exposed in responses (except during initial setup)

### Automatic Refresh
- Tokens refreshed before expiry
- Prevents API call failures
- Runs via background jobs (non-blocking)

## Testing

### Test OAuth Authorization
```bash
curl http://localhost/api/v1/credentials/{id}/oauth/authorize
```

### Test Token Refresh
```bash
curl -X POST http://localhost/api/v1/credentials/{id}/oauth/refresh
```

### Test Command
```bash
php artisan oauth:refresh-tokens
```

## Troubleshooting

### Token Refresh Fails
1. Check `token_url` is correct in credential data
2. Verify `client_secret` is valid
3. Check OAuth provider allows refresh_token grant type
4. Ensure refresh_token hasn't expired (some providers expire them)

### Authorization Fails
1. Verify `authorization_url` is correct
2. Check `client_id` is valid
3. Ensure redirect URI matches exactly in OAuth provider settings
4. Check state parameter is being cached properly

### Queue Not Processing
```bash
# Check queue connection
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

## Supported OAuth Providers

The implementation is provider-agnostic and works with:
- GitHub
- Google
- Microsoft/Azure AD
- GitLab
- Bitbucket
- Salesforce
- Any OAuth 2.0 compliant provider

Provider-specific requirements (like PKCE) may need additional implementation.
