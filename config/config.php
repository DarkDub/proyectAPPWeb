<?php
function env($key, $default = null) {
    // Verifica si la variable existe en $_ENV o $_SERVER
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];

    // Si no existe, intenta leer el .env manualmente
    $envFile = __DIR__ . '/.env';
    if (!file_exists($envFile)) return $default;

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignorar comentarios
        [$k, $v] = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if ($k === $key) return $v;
    }

    return $default;
}
