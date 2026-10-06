<?php
declare(strict_types=1);
require_once __DIR__.'/security.php';
$app = require __DIR__.'/../config/app.php';
$portal = require __DIR__.'/../config/portal.php';
$products = require __DIR__.'/../config/products.php';
require_once __DIR__.'/helpers.php';
require_once __DIR__.'/seo-helpers.php';
seo_redirect_page_alias($canonicalPath ?? '');
start_local_url_rewrite();
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');
?>
<!doctype html><html lang="id"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="theme-color" content="#ffffff">
<link rel="icon" type="image/png" data-theme-icon data-light-src="<?= e(asset_url('assets/images/logo-light.png')) ?>" data-dark-src="<?= e(asset_url('assets/images/logo-dark.png')) ?>" href="<?= e(asset_url('assets/images/logo-light.png')) ?>">
<link rel="apple-touch-icon" data-theme-icon data-light-src="<?= e(asset_url('assets/images/logo-light.png')) ?>" data-dark-src="<?= e(asset_url('assets/images/logo-dark.png')) ?>" href="<?= e(asset_url('assets/images/logo-light.png')) ?>">
<script src="<?= e(asset_url('assets/js/theme-init.js')) ?>"></script>
<?php require __DIR__.'/seo.php'; ?>
<link rel="preload" href="<?= e(local_url('assets/fonts/manrope-latin.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset_url('assets/css/fonts.css')) ?>">
<?php if (($pageKey ?? '') === 'home'): ?><link rel="stylesheet" href="<?= e(asset_url('assets/css/infrastructure.css')) ?>"><?php endif; ?>
<?php if (($pageKey ?? '') === 'hosting'): ?><link rel="stylesheet" href="<?= e(asset_url('assets/css/hosting-pricing.css')) ?>"><?php endif; ?>
<?php if (($pageKey ?? '') === 'contact'): ?><link rel="stylesheet" href="<?= e(asset_url('assets/css/contact.css')) ?>"><?php endif; ?>
<link rel="stylesheet" href="<?= e(asset_url('assets/css/tailwind.css')) ?>"><link rel="stylesheet" href="<?= e(asset_url('assets/css/app.css')) ?>"><link rel="stylesheet" href="<?= e(asset_url('assets/css/theme.css')) ?>"><link rel="stylesheet" href="<?= e(asset_url('assets/css/branding.css')) ?>"><link rel="stylesheet" href="<?= e(asset_url('assets/css/domain-picker.css')) ?>"><?php if (($pageKey ?? '') === 'about'): ?><link rel="stylesheet" href="<?= e(asset_url('assets/css/about.css')) ?>"><?php endif; ?></head><body data-page="<?= e($pageKey ?? '') ?>">
<a class="skip-link" href="#main">Lewati ke konten</a>
<?php require __DIR__.'/navbar.php'; ?>
<main id="main">
