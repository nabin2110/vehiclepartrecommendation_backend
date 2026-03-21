<?php

namespace App\Http\Services;

use App\Models\AuthChallenge;

class ChallengeService
{

    public function createChallenge(array $data)
    {
        AuthChallenge::where('email', $data['email'])
            ->where('device_id', $data['device_id'])
            ->where('expires_at', '<', now())
            ->delete();

        return AuthChallenge::create($data);
    }

    public function validateChallenge(
        string $challengeId,
        string $challengeResponse,
        string $email,
        string $deviceId,
        string $ipAddress
    ): ?AuthChallenge {
        $challenge = AuthChallenge::where('challenge_id', $challengeId)
            ->where('email', strtolower(trim($email)))
            ->where('device_id', $deviceId)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$challenge) {
            return null;
        }

        $expectedResponse = hash('sha256',
            $challenge->nonce . ':' . strtolower(trim($email)) . ':' . $deviceId
        );

        if (!hash_equals($expectedResponse, strtolower($challengeResponse))) {
            $challenge->increment('attempt_count');
            if ($challenge->attempt_count >= 3) {
                $challenge->update(['used' => true]);
            }
            return null;
        }

        return $challenge;
    }

     public function consumeChallenge(AuthChallenge $challenge): void
    {
        $challenge->update(['used' => true]);
    }
}
