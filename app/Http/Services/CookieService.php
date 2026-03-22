<?php

namespace App\Http\Services;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

class CookieService
{
    public const ACCESS_COOKIE  = '__access_token';
    public const REFRESH_COOKIE = '__refresh_token';
    public const DEVICE_COOKIE  = '__device_id';
    public const CSRF_COOKIE    = 'XSRF-TOKEN';

    private const ACCESS_TTL_MIN  = 15;
    private const REFRESH_TTL_MIN = 10080;
    private const CSRF_TTL_MIN    = 10080;

    public function attachAuthCookies(JsonResponse $response, array $tokenData): JsonResponse
    {
        $secure     = $this->isSecure();
        $refreshTtl = self::REFRESH_TTL_MIN;
        $csrfToken  = getCsrfToken();

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
            path: '/',
            httpOnly: true,
            secure: $secure,
        ));

        $response->withCookie($this->makeCookie(
            name: self::DEVICE_COOKIE,
            value: $tokenData['device_id'] ?? '',
            minutes: $refreshTtl,
            path: '/',
            httpOnly: true,
            secure: $secure,
        ));

        $response->withCookie($this->makeCookie(
            name: self::CSRF_COOKIE,
            value: $csrfToken,
            minutes: self::CSRF_TTL_MIN,
            path: '/',
            httpOnly: false,
            secure: $secure,
        ));

        $data               = $response->getData(true);
        $data['csrf_token'] = $csrfToken;
        $response->setData($data);

        return $response;
    }

    public function clearAuthCookies(JsonResponse $response): JsonResponse
    {
        $secure = $this->isSecure();

        $cookies = [
            ['name' => self::ACCESS_COOKIE,  'httpOnly' => true,  'path' => '/'],
            ['name' => self::REFRESH_COOKIE, 'httpOnly' => true,  'path' => '/'],
            ['name' => self::DEVICE_COOKIE,  'httpOnly' => true,  'path' => '/'],
            ['name' => self::CSRF_COOKIE,    'httpOnly' => false, 'path' => '/'],
        ];

        foreach ($cookies as $cookie) {
            $response->withCookie(
                $this->makeCookie(
                    name: $cookie['name'],
                    value: '',
                    minutes: -1,
                    path: $cookie['path'],
                    httpOnly: $cookie['httpOnly'],
                    secure: $secure,
                )
            );
        }

        return $response;
    }

    private function makeCookie(
        string $name,
        string $value,
        int    $minutes,
        string $path,
        bool   $httpOnly,
        bool   $secure,
    ): Cookie {
        return new Cookie(
            name: $name,
            value: $value,
            expire: $minutes > 0 ? time() + ($minutes * 60) : 1,
            path: $path,
            domain: null,
            secure: $secure,
            httpOnly: $httpOnly,
            raw: false,
            sameSite: $secure
                ? Cookie::SAMESITE_NONE
                : Cookie::SAMESITE_LAX,
        );
    }

    private function isSecure(): bool
    {
        return config('app.env') !== 'local';
    }
}
