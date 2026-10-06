<?php
declare(strict_types=1);

function seo_local_host(string $host): bool {
    $host = strtolower(rtrim(trim($host, '[]'), '.'));
    if ($host === '' || $host === 'localhost' || !str_contains($host, '.')
        || preg_match('/\.(?:localhost|test|local|internal|invalid|example)$/', $host)
        || in_array($host, ['example.com', 'example.org', 'example.net'], true)) return true;
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    return false;
}

function seo_indexable(): bool {
    global $app;
    $configuredHost = (string) (parse_url($app['url'], PHP_URL_HOST) ?? '');
    $requestHost = (string) (parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? $configuredHost), PHP_URL_HOST) ?? '');
    return $app['environment'] === 'production' && $app['seo_indexing']
        && !seo_local_host($configuredHost) && !seo_local_host($requestHost);
}

function seo_redirect_page_alias(string $canonicalPath): void {
    if (http_response_code() >= 400 || !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) return;
    $requested = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    if (!is_string($requested)) return;
    $relative = substr($requested, strlen(app_base_path()));
    $alias = strtolower(trim($relative, '/'));
    $expected = $canonicalPath === '' ? ['', 'index', 'index.php'] : [$canonicalPath, $canonicalPath . '.php'];
    if (in_array($alias, $expected, true) && $requested !== local_url($canonicalPath)) {
        header('Location: ' . local_url($canonicalPath), true, 301);
        exit;
    }
}

function seo_resource_headers(string $path, string $contentType): void {
    $requested = parse_url($_SERVER['REQUEST_URI'] ?? '/' . $path, PHP_URL_PATH);
    if ($requested !== local_url($path) && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        header('Location: ' . local_url($path), true, 301);
        exit;
    }
    header('Content-Type: ' . $contentType . '; charset=UTF-8');
    header('Cache-Control: no-cache, must-revalidate');
    header('X-Robots-Tag: noindex');
    if (!in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        header('Allow: GET, HEAD');
        http_response_code(405);
        exit;
    }
}
