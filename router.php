<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/security.php';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) ? rawurldecode($path) : '';
// Only known public pages can be dispatched. Never turn arbitrary URLs into include paths.
$route = strtolower(trim($path, '/'));
if (str_ends_with($route, '.php')) $route = substr($route, 0, -4);
$routes = [
    '' => 'index.php', 'index' => 'index.php', 'index.php' => 'index.php',
    'about' => 'about.php', 'contact' => 'contact.php', 'domain' => 'domain.php',
    'hosting' => 'hosting.php', 'privacy' => 'privacy.php', 'terms' => 'terms.php',
    'ssl' => 'ssl.php', 'vps' => 'vps.php', 'vps-jakarta' => 'vps-jakarta.php',
    'vps-bogor' => 'vps-bogor.php', '404' => '404.php',
    'api/domain-check' => 'api/domain-check.php',
    'api/domain-extensions' => 'api/domain-extensions.php',
    'sitemap.xml' => 'sitemap.php', 'sitemap' => 'sitemap.php',
    'robots.txt' => 'robots.php', 'robots' => 'robots.php',
];
if (isset($routes[$route])) {
    require __DIR__ . '/' . $routes[$route];
    return true;
}
// PHP's development server may serve only static assets inside this directory.
if (PHP_SAPI === 'cli-server' && str_starts_with($path, '/assets/')) {
    $asset = realpath(__DIR__ . $path);
    $assetRoot = realpath(__DIR__ . '/assets') . DIRECTORY_SEPARATOR;
    if ($asset !== false && str_starts_with($asset, $assetRoot) && is_file($asset)
        && preg_match('/\.(?:css|js|png|jpg|jpeg|svg|webp|ico|woff2)$/i', $asset)) return false;
}
require __DIR__.'/404.php'; return true;
