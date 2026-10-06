<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/security.php';
$app = require __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/seo-helpers.php';
seo_resource_headers('sitemap.xml', 'application/xml');
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'HEAD') exit;
$pages = require __DIR__ . '/config/seo.php';
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as $path => $page) {
    $modified = 0;
    // Reflect actual content/schema changes, not the time of each request.
    foreach (array_merge($page['files'], ['includes/seo.php', 'config/seo.php', 'config/company.php', 'includes/navbar.php', 'includes/footer.php']) as $file) {
        if (is_file(__DIR__ . '/' . $file)) $modified = max($modified, (int) filemtime(__DIR__ . '/' . $file));
    }
    echo '  <url><loc>' . htmlspecialchars(page_url($path), ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
    if ($modified > 0) echo '<lastmod>' . gmdate('Y-m-d\TH:i:s\Z', $modified) . '</lastmod>';
    echo "</url>\n";
}
echo "</urlset>\n";
