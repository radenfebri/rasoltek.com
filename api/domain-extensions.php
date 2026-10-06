<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/security.php';

require_once __DIR__ . '/../includes/rdash.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
enforce_api_request(30, 'domain-extensions');

$cachePath = private_cache_directory() . '/extensions.json';
$cached = is_file($cachePath) ? json_decode((string) @file_get_contents($cachePath), true) : null;
if (is_array($cached) && ($cached['expires'] ?? 0) > time() && is_array($cached['extensions'] ?? null)) {
    echo json_encode(['success' => true, 'extensions' => $cached['extensions']]);
    exit;
}

function json_response(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

if (!rdash_is_configured()) {
    json_response(['success' => false, 'message' => 'Daftar ekstensi sedang tidak tersedia.'], 503);
}

$extensions = [];
$page = 1;
$deadline = time() + 30;
do {
    if ($page > 10 || time() >= $deadline) {
        json_response(['success' => false, 'message' => 'Daftar ekstensi sedang tidak tersedia.'], 503);
    }
    $response = rdash_get('account/prices', ['page' => $page, 'limit' => 100]);
    $data = is_array($response['data'] ?? null) ? $response['data'] : [];
    if (!$response['ok'] || !is_array($data['data'] ?? null)) {
        json_response(['success' => false, 'message' => 'Daftar ekstensi sedang tidak tersedia.'], 503);
    }

    foreach ($data['data'] as $item) {
        if (!is_array($item)) {
            continue;
        }
        $extension = $item['domain_extension']['extension'] ?? null;
        $status = (int) ($item['domain_extension']['status'] ?? 1);
        if (!is_string($extension) || $status !== 1) {
            continue;
        }
        $extension = '.' . strtolower(ltrim(trim($extension), '.'));
        // List active extensions regardless of sell_option; it is not an active-status flag.
        if (preg_match('~^\.[a-z0-9-]+(?:\.[a-z0-9-]+)*$~D', $extension)) {
            $extensions[] = $extension;
        }
    }

    $hasNextPage = $page < (int) ($data['meta']['last_page'] ?? $page)
        || !empty($data['links']['next']);
    if ($hasNextPage && $data['data'] === []) {
        json_response(['success' => false, 'message' => 'Daftar ekstensi sedang tidak tersedia.'], 503);
    }
    $page++;
} while ($hasNextPage);

$extensions = array_values(array_unique($extensions));
usort($extensions, static function (string $a, string $b): int {
    $preferred = ['.com', '.id', '.co.id', '.net', '.org', '.xyz'];
    $ai = array_search($a, $preferred, true);
    $bi = array_search($b, $preferred, true);
    if ($ai !== false || $bi !== false) {
        return ($ai === false ? 999 : $ai) <=> ($bi === false ? 999 : $bi);
    }
    return strlen($a) <=> strlen($b) ?: strcmp($a, $b);
});

$cacheTemp = tempnam(private_cache_directory(), 'extensions-');
if ($cacheTemp !== false) {
    file_put_contents($cacheTemp, json_encode(['expires' => time() + 3600, 'extensions' => $extensions]), LOCK_EX);
    @rename($cacheTemp, $cachePath);
    if (is_file($cacheTemp)) @unlink($cacheTemp);
}

json_response([
    'success' => true,
    'extensions' => $extensions,
]);
