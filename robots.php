<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/security.php';
$app = require __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/seo-helpers.php';
seo_resource_headers('robots.txt', 'text/plain');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') exit;
echo "User-agent: *\n";
if (!seo_indexable()) {
    echo "Disallow: /\n";
} else {
    // Crawling rules are not access control; server rules protect private files.
    $prefix = (string) (parse_url($app['url'], PHP_URL_PATH) ?? '');
    foreach (['api/', 'config/', 'includes/', 'components/', 'src/', 'vendor/', 'node_modules/', 'deploy/', 'tests/'] as $directory) echo 'Disallow: ' . $prefix . '/' . $directory . "\n";
    echo "\nSitemap: " . page_url('sitemap.xml') . "\n";
}
