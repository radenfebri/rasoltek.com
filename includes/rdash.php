<?php
declare(strict_types=1);

require_once __DIR__ . '/env.php';

function rdash_config(): array {
    load_env_file();
    return [
        'base_url' => rtrim(getenv('RDASH_API_BASE_URL') ?: 'https://api.rdash.id/v1', '/'),
        'reseller_id' => getenv('RDASH_RESELLER_ID') ?: '',
        'api_key' => getenv('RDASH_API_KEY') ?: '',
    ];
}

function rdash_is_configured(): bool {
    $config = rdash_config();
    return $config['reseller_id'] !== '' && $config['api_key'] !== '';
}

function rdash_get(string $path, array $query = []): array {
    $config = rdash_config();
    $base = parse_url($config['base_url']);
    if (!is_array($base) || ($base['scheme'] ?? '') !== 'https'
        || ($base['host'] ?? '') !== 'api.rdash.id' || isset($base['user']) || isset($base['pass'])
        || (isset($base['port']) && $base['port'] !== 443)) {
        return ['ok' => false, 'status' => 500, 'data' => null, 'message' => 'Domain check is not configured.'];
    }
    if ($config['reseller_id'] === '' || $config['api_key'] === '') {
        return ['ok' => false, 'status' => 500, 'data' => null, 'message' => 'Domain check is not configured.'];
    }
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'status' => 500, 'data' => null, 'message' => 'Domain check is not available.'];
    }

    $url = $config['base_url'] . '/' . ltrim($path, '/');
    if ($query !== []) {
        $url .= '?' . http_build_query($query);
    }

    $ch = curl_init($url);
    if ($ch === false) {
        return ['ok' => false, 'status' => 500, 'data' => null, 'message' => 'Domain check is not available.'];
    }

    $body = '';
    curl_setopt_array($ch, [
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use (&$body): int {
            if (strlen($body) + strlen($chunk) > 1048576) return 0;
            $body .= $chunk;
            return strlen($chunk);
        },
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => $config['reseller_id'] . ':' . $config['api_key'],
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 18,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ]);

    $completed = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);

    if ($completed === false || $error !== '') {
        return ['ok' => false, 'status' => 502, 'data' => null, 'message' => 'Domain check is temporarily unavailable.'];
    }

    $payload = json_decode((string) $body, true);
    if (!is_array($payload)) {
        return ['ok' => false, 'status' => 502, 'data' => null, 'message' => 'Domain check is temporarily unavailable.'];
    }

    return [
        'ok' => $status >= 200 && $status < 300,
        'status' => $status,
        'data' => $payload,
        'message' => (string) ($payload['message'] ?? 'Domain check is temporarily unavailable.'),
    ];
}

function domain_search_unavailable_message(): string {
    return 'Pencarian domain sedang tidak tersedia. Silakan coba lagi sebentar lagi.';
}
