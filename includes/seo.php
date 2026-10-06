<?php
$title = $pageTitle ?? 'RasolTek Hosting — Infrastruktur Digital Andal';
$description = $pageDescription ?? 'Domain, hosting SSD, VPS Jakarta dan Bogor, serta sertifikat SSL untuk kebutuhan bisnis dan aplikasi Anda di Indonesia.';
$seoPages = require __DIR__ . '/../config/seo.php';
$company = require __DIR__ . '/../config/company.php';
$canonicalRoute = trim($canonicalPath ?? '', '/');
$seoPage = $seoPages[$canonicalRoute] ?? null;
$isErrorPage = http_response_code() >= 400;
$canIndex = seo_indexable() && !$isErrorPage && $seoPage !== null && !($pageNoIndex ?? false);
$robots = $canIndex ? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1' : 'noindex,follow';
header('X-Robots-Tag: ' . $robots);
$canonical = page_url($canonicalRoute);
$homeUrl = page_url();
$imageUrl = page_url('assets/images/logo.png');
$imageSize = getimagesize(__DIR__ . '/../assets/images/logo.png');
$breadcrumbs = [['label' => 'Beranda', 'path' => '']];
if ($canonicalRoute !== '' && $seoPage !== null) {
    if (in_array($canonicalRoute, ['vps-jakarta', 'vps-bogor'], true)) $breadcrumbs[] = ['label' => 'Bandingkan VPS', 'path' => 'vps'];
    $breadcrumbs[] = ['label' => $seoPage['label'], 'path' => $canonicalRoute];
}
$schema = null;
if (!$isErrorPage && $seoPage !== null) {
    $organization = [
        '@type' => 'Organization', '@id' => $homeUrl . '#organization',
        'name' => $app['name'], 'legalName' => $company['name'], 'url' => $homeUrl,
        'logo' => ['@type' => 'ImageObject', 'url' => $imageUrl, 'width' => $imageSize[0], 'height' => $imageSize[1]],
        'telephone' => str_replace('tel:', '', $company['phone_url']), 'email' => 'admin@rasoltek.com',
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Karangsari, Kedaleman, Rogojampi', 'addressLocality' => 'Banyuwangi', 'addressRegion' => 'Jawa Timur', 'postalCode' => '68462', 'addressCountry' => 'ID'],
        'sameAs' => [$company['website'], $company['maps_url']],
    ];
    $webpage = [
        '@type' => $seoPage['type'], '@id' => $canonical . '#webpage',
        'name' => $title, 'description' => $description, 'url' => $canonical, 'inLanguage' => 'id-ID',
        'isPartOf' => ['@id' => $homeUrl . '#website'], 'publisher' => ['@id' => $homeUrl . '#organization'],
    ];
    $graph = [$organization, ['@type' => 'WebSite', '@id' => $homeUrl . '#website', 'name' => $app['name'], 'url' => $homeUrl, 'inLanguage' => 'id-ID', 'publisher' => ['@id' => $homeUrl . '#organization']], $webpage];
    if ($canonicalRoute !== '') {
        $items = [];
        foreach ($breadcrumbs as $position => $breadcrumb) $items[] = ['@type' => 'ListItem', 'position' => $position + 1, 'name' => $breadcrumb['label'], 'item' => page_url($breadcrumb['path'])];
        $graph[2]['breadcrumb'] = ['@id' => $canonical . '#breadcrumb'];
        $graph[] = ['@type' => 'BreadcrumbList', '@id' => $canonical . '#breadcrumb', 'itemListElement' => $items];
    }
    $schema = ['@context' => 'https://schema.org', '@graph' => $graph];
}
?>
<title><?= e($title) ?></title>
<meta name="description" content="<?= e($description) ?>">
<meta name="robots" content="<?= e($robots) ?>">
<?php if (!$isErrorPage && $seoPage !== null): ?><link rel="canonical" href="<?= e($canonical) ?>"><?php endif; ?>
<meta property="og:type" content="website"><meta property="og:locale" content="<?= e($app['locale']) ?>">
<meta property="og:site_name" content="<?= e($app['name']) ?>"><meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e($description) ?>">
<?php if (!$isErrorPage): ?><meta property="og:url" content="<?= e($canonical) ?>"><?php endif; ?>
<meta property="og:image" content="<?= e($imageUrl) ?>"><meta property="og:image:alt" content="Logo RasolTek">
<meta property="og:image:type" content="image/png"><meta property="og:image:width" content="<?= (int) $imageSize[0] ?>"><meta property="og:image:height" content="<?= (int) $imageSize[1] ?>">
<meta name="twitter:card" content="summary"><meta name="twitter:title" content="<?= e($title) ?>"><meta name="twitter:description" content="<?= e($description) ?>">
<meta name="twitter:image" content="<?= e($imageUrl) ?>"><meta name="twitter:image:alt" content="Logo RasolTek">
<?php if ($schema !== null): ?><script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) ?></script><?php endif; ?>
