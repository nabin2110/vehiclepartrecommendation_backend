<?php

namespace App\Http\Repositories;

use App\Http\Services\ChallengeService;
use App\Http\Services\CookieService;
use App\Http\Services\TokenService;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthRepository extends BaseRepository
{
    public function __construct(
        protected User $userModel,
        protected ChallengeService $challengeService,
        protected TokenService $tokenService,
        protected CookieService $cookieService,
    ) {
        parent::__construct($userModel);
    }

    public function initiateLogin(array $data)
    {
        $challengeData = [
            'email' => $data['email'],
            'device_id' => $data['device_id'],
            'ip_address' => request()->ip(),
            'user_agent' => $request->user_agent ?? request()->userAgent(),
        ];

        $challengeResponse = $this->challengeService->createChallenge(
            $challengeData
        );

        return [
            'challenge_id' => $challengeResponse->challenge_id,
            'nonce'        => $challengeResponse->nonce,
            'expires_at'   => $challengeResponse->expires_at->toIso8601String(),
            'algorithm'    => 'SHA-256',
        ];
    }

    public function login(array $data)
    {
        $challenge = $this->challengeService->validateChallenge(
            challengeId: $data['challenge_id'],
            challengeResponse: $data['challenge_response'],
            email: $data['email'],
            deviceId: $data['device_id'],
            ipAddress: request()->ip(),
        );

        if (!$challenge) return ['challenge' => false];

        $user = $this->model->where('email', strtolower($data['email']))->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            $this->challengeService->consumeChallenge($challenge);
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $this->challengeService->consumeChallenge($challenge);

        $tokens = [
            'ip_address' => request()->ip(),
            'device_id' => $data['device_id']
        ];

        $tokenData  = $this->tokenService->issueTokens(
            $user,
            $tokens
        );

        $user->update(['last_login_at' => now(), 'last_login_ip' => request()->ip()]);

        $response = response()->json([
            'message'    => 'Login successful.',
            'success' => true,
            'expires_in' => $tokenData['expires_in'],
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);

        return $this->cookieService->attachAuthCookies($response, $tokenData);
    }

    public function refresh()
    {
        $refreshToken = request()->cookie('__refresh_token');
        if (!$refreshToken) {
            $response = response()->json(['message' => 'Token not found'], 401);
            return $this->cookieService->clearAuthCookies($response);
        }

        $hashed = hash('sha256', $refreshToken);
        $token  = RefreshToken::where('token_hash', $hashed)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            $response = response()->json(['message' => 'Token expired'], 401);
            return $this->cookieService->clearAuthCookies($response);
        }

        $user = $this->userModel->find($token->user_id);
        $token->delete(); 

        $tokenData = $this->tokenService->issueTokens($user, [
            'ip_address' => request()->ip(),
            'device_id'  => request()->cookie('__device_id'),
        ]);

        $response = response()->json([
            'message'    => 'Token refreshed.',
            'success' => true,
            'expires_in' => $tokenData['expires_in'],
            'user'       => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);

        return $this->cookieService->attachAuthCookies($response, $tokenData);
    }

    public function logout()
    {
        auth()->user()?->currentAccessToken()?->delete();

        $response = response()->json([
            'message'    => 'Token refreshed.',
            'success' => true
        ]);
        return $this->cookieService->clearAuthCookies($response); 
    }
}
