<?php

if (!function_exists('getCsrfToken')) {
    function getCsrfToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}

if (!function_exists('webLog')) {
    function webLog(string $errorMessage): string
    {
        $dirPath = storage_path('app/public/log');
        $fileName = 'web.log';
        $filePath = $dirPath . DIRECTORY_SEPARATOR . $fileName;

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        if (!file_exists($filePath)) {
            fopen($filePath, 'w');
        }

        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . $errorMessage . PHP_EOL;
        file_put_contents($filePath, $logMessage, FILE_APPEND);

        return $filePath;
    }
}
