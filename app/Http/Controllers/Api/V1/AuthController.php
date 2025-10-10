<?php

namespace App\Http\Controllers\Api\V1;

use Aacotroneo\Saml2\Facades\Saml2Auth;
use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class AuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->created([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'User registered successfully.');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        if (! Auth::attempt($request->only('email', 'password'))) {
            return $this->unauthorized('Invalid credentials');
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'User logged in successfully.');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'User logged out successfully.');
    }

    public function refresh(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed successfully.');
    }

    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return $this->unauthorized('Invalid verification link');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success([], 'Email already verified.');
        }

        if ($user->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($user));
        }

        return $this->success([], 'Email verified successfully.');
    }

    public function resendVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->notFound('User not found');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success([], 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        return $this->success([], 'Verification link sent successfully.');
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return $this->notFound('User not found');
        }

        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // For simplicity, we are returning the token in the response.
        // In a real application, you would send an email to the user with the reset link.
        return $this->success(['reset_token' => $token], 'Password reset token generated successfully.');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $passwordReset = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (! $passwordReset || ! Hash::check($request->token, $passwordReset->token)) {
            return $this->unauthorized('Invalid token');
        }

        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return $this->success([], 'Password reset successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        if (! Hash::check($request->current_password, $user->password)) {
            return $this->unauthorized('Invalid current password');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return $this->success([], 'Password changed successfully.');
    }

    public function oauthRedirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function oauthCallback($provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return $this->unauthorized('Failed to authenticate with '.$provider);
        }

        $user = User::where('email', $socialiteUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'User authenticated successfully.');
    }

    public function samlLogin(Request $request)
    {
        return Saml2Auth::redirect('/');
    }

    public function samlAcs(Request $request)
    {
        $saml2User = Saml2Auth::getSaml2User();
        $attributes = $saml2User->getAttributes();

        $email = $attributes['urn:oid:0.9.2342.19200300.100.1.3'][0];
        $name = $attributes['urn:oid:2.5.4.42'][0].' '.$attributes['urn:oid:2.5.4.4'][0];

        $user = User::where('email', $email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make(Str::random(24)),
                'email_verified_at' => now(),
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 'User authenticated successfully.');
    }

    public function mfaEnable(Request $request)
    {
        $user = $request->user();

        $secret = Google2FA::generateSecretKey();

        $user->two_factor_secret = $secret;
        $user->save();

        $qrCodeUrl = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $secret
        );

        return $this->success([
            'qr_code_url' => $qrCodeUrl,
            'secret' => $secret,
        ], 'MFA enabled successfully. Scan the QR code with your authenticator app.');
    }

    public function mfaVerify(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'one_time_password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $isValid = Google2FA::verifyKey($user->two_factor_secret, $request->one_time_password);

        if (! $isValid) {
            return $this->unauthorized('Invalid one time password');
        }

        $user->two_factor_enabled = true;
        $user->save();

        return $this->success([], 'MFA verified successfully.');
    }

    public function mfaDisable(Request $request)
    {
        $user = $request->user();

        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();

        return $this->success([], 'MFA disabled successfully.');
    }

    public function me(Request $request)
    {
        return $this->success($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$user->id,
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $user->fill($request->only(['name', 'email']));
        $user->save();

        return $this->success($user, 'Profile updated successfully.');
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Revoke all tokens
        $user->tokens()->delete();

        // Delete the user
        $user->delete();

        return $this->success([], 'Account deleted successfully.');
    }

    public function getSessions(Request $request)
    {
        $sessions = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->get();

        return $this->success($sessions);
    }

    public function deleteSession(Request $request, $id)
    {
        $user = $request->user();

        $session = DB::table('sessions')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (! $session) {
            return $this->notFound('Session not found');
        }

        DB::table('sessions')->where('id', $id)->delete();

        return $this->success([], 'Session deleted successfully.');
    }

    public function getApiKeys(Request $request)
    {
        return $this->success($request->user()->apiKeys);
    }

    public function createApiKey(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->unprocessable($validator->errors());
        }

        $apiKey = new ApiKey([
            'name' => $request->name,
            'key' => Str::random(40),
        ]);

        $user->apiKeys()->save($apiKey);

        return $this->created($apiKey, 'API key created successfully.');
    }

    public function deleteApiKey(Request $request, $id)
    {
        $user = $request->user();

        $apiKey = $user->apiKeys()->find($id);

        if (! $apiKey) {
            return $this->notFound('API key not found');
        }

        $apiKey->delete();

        return $this->success([], 'API key deleted successfully.');
    }
}
