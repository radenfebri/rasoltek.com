<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/env.php';
load_env_file();

$url = rtrim(trim(getenv('APP_URL') ?: 'https://www.rasoltek.com'), '/');
$urlParts = parse_url($url);
$validUrl = true;
// Never derive canonical URLs from an untrusted Host header.
if (!filter_var($url, FILTER_VALIDATE_URL) || !is_array($urlParts) || !in_array($urlParts['scheme'] ?? '', ['http', 'https'], true)
    || empty($urlParts['host']) || isset($urlParts['user']) || isset($urlParts['pass'])
    || isset($urlParts['query']) || isset($urlParts['fragment'])
    || preg_match('/[\x00-\x20\x7f]/', $url)) {
    $url = 'https://www.rasoltek.com';
    $validUrl = false;
}
$seoIndexing = getenv('SEO_INDEXING');

return [
    'name' => 'RasolTek Hosting',
    'url' => $url,
    'environment' => strtolower(getenv('APP_ENV') ?: 'production'),
    'seo_indexing' => $validUrl && filter_var($seoIndexing === false ? 'true' : $seoIndexing, FILTER_VALIDATE_BOOLEAN),
    'locale' => 'id_ID',
    'status_label' => null,
];
