<?php

namespace App\Http\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class CookieService
{

    public const ACCESS_COOKIE   = '__access_token';
    public const REFRESH_COOKIE  = '__refresh_token';
    public const DEVICE_COOKIE   = '__device_id';
    public const CSRF_COOKIE     = 'XSRF-TOKEN';

    private const ACCESS_TTL_MIN  = 15;       // 15 minutes

    private const REFRESH_TTL_MIN = 10080;    // 7 days
    private const REMEMBER_TTL_MIN = 43200;   // 30 days
    private const CSRF_TTL_MIN    = 10080;

    public function attachAuthCookies(JsonResponse $response, array $tokenData)
    {
        $secure      = $this->isSecure();
        $refreshTtl  = self::REFRESH_TTL_MIN;
        $csrfToken   = getCsrfToken();

        $response->withCookie($this->makeCookie(
            name: self::ACCESS_COOKIE,
            value: $tokenData['access_token'],
            minutes: self::ACCESS_TTL_MIN,
            path: '/',
            httpOnly: true,
            secure: $secure,
        ));

        $response->withCookie($this->makeCookie(
            name: self::REFRESH_COOKIE,
            value: $tokenData['refresh_token'],
            minutes: $refreshTtl,
            path: '/api',
            httpOnly: true,
            secure: $secure,
        ));

        $response->withCookie($this->makeCookie(
            name: self::DEVICE_COOKIE,
            value: $tokenData['device_id'] ?? '',
            minutes: $refreshTtl,
            path: '/api',
            httpOnly: true,
            secure: $secure,
        ));

        $response->withCookie($this->makeCookie(
            name: self::CSRF_COOKIE,
            value: $csrfToken,
            minutes: $refreshTtl,
            path: '/',
            httpOnly: false,
            secure: $secure,
        ));

        $data = $response->getData(true);
        $data['csrf_token'] = $csrfToken;
        $response->setData($data);

        return $response;
    }


    private function makeCookie(
        string $name,
        string $value,
        int    $minutes,
        string $path,
        bool   $httpOnly,
        bool   $secure,
    ) {
        return new Cookie(
            name: $name,
            value: $value,
            expire: $minutes > 0 ? time() + ($minutes * 60) : 1,
            path: $path,
            domain: null,
            secure: $secure,
            httpOnly: $httpOnly,
            raw: false,
            sameSite: Cookie::SAMESITE_STRICT,
        );
    }

    private function isSecure(): bool
    {
        return config('app.env') !== 'local';
    }

    public function clearAuthCookies(JsonResponse $response): JsonResponse
    {
        $cookies = [
            CookieService::ACCESS_COOKIE,
            CookieService::REFRESH_COOKIE,
            CookieService::DEVICE_COOKIE,
            CookieService::CSRF_COOKIE,
        ];

        foreach ($cookies as $name) {
            $response->withCookie(
                cookie($name, '', -1)
            );
        }

        return $response;
    }
}
