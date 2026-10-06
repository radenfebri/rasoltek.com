<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('log_errors', '1');
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: DENY');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; font-src 'self'; img-src 'self' data:; connect-src 'self'; form-action 'self' mailto: https://portal.rasoltek.com; base-uri 'self'; object-src 'none'; frame-ancestors 'none'");

function private_cache_directory(): string {
    $directory = sys_get_temp_dir() . '/rasoltek-' . hash('sha256', dirname(__DIR__));
    if (!is_dir($directory) && !@mkdir($directory, 0700, true) && !is_dir($directory)) {
        throw new RuntimeException('Private cache unavailable.');
    }
    return $directory;
}

function enforce_api_request(int $limit, string $bucket): void {
    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        header('Allow: GET, HEAD');
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Metode permintaan tidak diizinkan.']);
        exit;
    }
    // Use the actual peer, not user-controlled X-Forwarded-For headers.
    // A fixed endpoint bucket prevents bypass through clean, .php and case variants.
    $key = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . ':' . $bucket);
    $handle = null;
    try {
        $handle = fopen(private_cache_directory() . '/request-limits.json', 'c+');
        if (!$handle || !flock($handle, LOCK_EX)) {
            throw new RuntimeException('Rate limit storage unavailable.');
        }
        $now = time();
        $entries = json_decode(stream_get_contents($handle), true) ?: [];
        foreach ($entries as $hash => $entry) {
            if (($entry['until'] ?? 0) <= $now) unset($entries[$hash]);
        }
        $entry = $entries[$key] ?? ['until' => $now + 60, 'count' => 0];
        $allowed = $entry['count'] < $limit && (isset($entries[$key]) || count($entries) < 10000);
        if ($allowed) {
            $entry['count']++;
            $entries[$key] = $entry;
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($entries));
            fflush($handle);
        }
        flock($handle, LOCK_UN);
        fclose($handle);
        $handle = null;
        if (!$allowed) {
            header('Retry-After: ' . max(1, $entry['until'] - $now));
            http_response_code(429);
            echo json_encode(['success' => false, 'message' => 'Terlalu banyak permintaan. Silakan coba lagi sebentar lagi.']);
            exit;
        }
    } catch (Throwable $error) {
        if (is_resource($handle)) fclose($handle);
        error_log('API rate limit storage unavailable.');
        http_response_code(503);
        echo json_encode(['success' => false, 'message' => 'Layanan sementara tidak tersedia.']);
        exit;
    }
}
