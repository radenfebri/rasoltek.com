<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/security.php';

require_once __DIR__ . '/../includes/rdash.php';
$portal = require __DIR__ . '/../config/portal.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');
enforce_api_request(20, 'domain-check');

function json_response(array $payload, int $status = 200): never {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function normalize_domain_input(string $name, string $extension): string {
    $name = strtolower(trim($name));
    $extension = strtolower(trim($extension));
    $name = preg_replace('~^https?://~', '', $name) ?? '';
    $name = preg_replace('~/.*$~', '', $name) ?? '';
    $name = preg_replace('~\s+~', '', $name) ?? '';
    $name = trim($name, '.');

    if ($extension !== '' && $extension[0] !== '.') {
        $extension = '.' . $extension;
    }
    if ($name !== '' && !str_contains($name, '.') && $extension !== '') {
        $name .= $extension;
    }
    return $name;
}

function find_key_recursive(mixed $data, array $keys): mixed {
    if (!is_array($data)) {
        return null;
    }
    foreach ($keys as $key) {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
    }
    foreach ($data as $value) {
        $found = find_key_recursive($value, $keys);
        if ($found !== null) {
            return $found;
        }
    }
    return null;
}

function rdash_availability_item(array $payload): array {
    $data = $payload['data'] ?? [];
    if (is_array($data) && isset($data[0]) && is_array($data[0])) {
        return $data[0];
    }
    return is_array($data) ? $data : [];
}

function bool_from_availability(mixed $value): ?bool {
    if (is_bool($value)) {
        return $value;
    }
    if (is_int($value) || is_float($value)) {
        return (int) $value === 1;
    }
    if (is_string($value)) {
        $text = strtolower(trim($value));
        if ($text === '1' || $text === 'true' || $text === 'available') {
            return true;
        }
        if ($text === '0' || $text === 'false' || $text === 'unavailable' || $text === 'not available' || $text === 'registered') {
            return false;
        }
    }
    return null;
}

function infer_availability(array $payload): ?bool {
    $item = rdash_availability_item($payload);
    $value = $item['available'] ?? find_key_recursive($payload, ['available', 'is_available', 'availability']);
    $known = bool_from_availability($value);
    if ($known !== null) {
        return $known;
    }
    if (is_string($value)) {
        $text = strtolower($value);
        if (str_contains($text, 'not available') || str_contains($text, 'unavailable') || str_contains($text, 'registered')) {
            return false;
        }
        if (str_contains($text, 'available') || str_contains($text, 'domain not found')) {
            return true;
        }
    }

    $message = strtolower((string) ($payload['message'] ?? ''));
    if ($message !== '') {
        if (str_contains($message, 'not available') || str_contains($message, 'unavailable') || str_contains($message, 'registered')) {
            return false;
        }
        if (str_contains($message, 'available') || str_contains($message, 'domain not found')) {
            return true;
        }
    }
    return null;
}

function rupiah_label(int|float|null $value): ?string {
    if ($value === null || $value <= 0) {
        return null;
    }
    return 'Rp' . number_format((float) $value, 0, ',', '.');
}

function build_order_url(string $baseUrl, string $domain): string {
    if ($baseUrl === '' || $baseUrl === '#') {
        return '#';
    }
    $parts = parse_url($baseUrl);
    $path = $parts['path'] ?? '';
    if ($path === '' || $path === '/') {
        $baseUrl = rtrim($baseUrl, '/') . '/cart.php?a=add&domain=register';
    }
    $separator = str_contains($baseUrl, '?') ? '&' : '?';
    return $baseUrl . $separator . http_build_query(['query' => $domain]);
}

$nameInput = $_GET['domain'] ?? '';
$extensionInput = $_GET['extension'] ?? '';
if (!is_string($nameInput) || !is_string($extensionInput) || strlen($nameInput) > 512 || strlen($extensionInput) > 253) {
    json_response(['success' => false, 'message' => 'Masukkan nama domain yang valid.'], 422);
}
$domain = normalize_domain_input($nameInput, $extensionInput);
if ($domain === '' || !preg_match('~^(?=.{4,253}$)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])$~', $domain)) {
    json_response([
        'success' => false,
        'message' => 'Masukkan nama domain yang valid.',
    ], 422);
}

$includePremium = filter_var(getenv('RDASH_INCLUDE_PREMIUM_DOMAINS') ?: 'true', FILTER_VALIDATE_BOOLEAN);

if (!rdash_is_configured()) {
    json_response([
        'success' => false,
        'domain' => $domain,
        'message' => domain_search_unavailable_message(),
    ], 500);
}

$response = rdash_get('domains/availability', [
    'domain' => $domain,
    'include_premium_domains' => $includePremium ? 'true' : 'false',
]);

if (!$response['ok']) {
    json_response([
        'success' => false,
        'domain' => $domain,
        'message' => domain_search_unavailable_message(),
    ], (int) ($response['status'] >= 500 ? 502 : 503));
}

$payload = is_array($response['data']) ? $response['data'] : [];
$item = rdash_availability_item($payload);
$available = infer_availability($payload);
$domainName = $domain;
$isPremium = filter_var($item['is_premium_name'] ?? false, FILTER_VALIDATE_BOOLEAN);
$premiumPrice = isset($item['premium_registration_price']) ? (float) $item['premium_registration_price'] : null;
$message = $available === true
        ? ($isPremium ? 'Domain tersedia sebagai nama premium.' : 'Domain tersedia dan siap dipesan.')
        : ($available === false ? 'Domain sudah terdaftar atau tidak tersedia.' : 'Status domain berhasil dicek.');

json_response([
    'success' => true,
    'domain' => $domainName,
    'available' => $available,
    'premium' => $isPremium,
    'premium_registration_price' => $premiumPrice,
    'premium_registration_price_label' => rupiah_label($premiumPrice),
    'message' => $message,
    'order_url' => build_order_url($portal['domain_search_url'] ?? '#', $domainName),
]);
