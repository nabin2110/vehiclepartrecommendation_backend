<?php

namespace App\Http\Services;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Support\Str;

class TokenService
{

    private const ACCESS_TOKEN_TTL  = 900;
    private const REFRESH_TOKEN_TTL = 604800;
    private const WEB_TOKEN = 'web-token';
    public function issueTokens(User $user, array $data)
    {
        $expiresIn  = self::ACCESS_TOKEN_TTL;
        $refreshTtl = self::REFRESH_TOKEN_TTL;
        $jti = (string) Str::uuid();
        $rawRefresh    = Str::random(80);
        $hashedRefresh = hash('sha256', $rawRefresh);

        $accessToken =  $user->createToken(self::WEB_TOKEN)->plainTextToken;

        RefreshToken::create([
            'token_hash' => $hashedRefresh,
            'user_id'    => $user->id,
            'device_id'  => $data['device_id'],
            'ip_address' => $data['ip_address'],
            'jti'        => $jti,
            'expires_at' => now()->addSeconds($refreshTtl),
        ]);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $rawRefresh,
            'device_id'     => $data['device_id'],
            'expires_in'    => $expiresIn,
        ];
    }
}
