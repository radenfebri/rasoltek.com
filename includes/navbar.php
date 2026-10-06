<?php
$vpsMenu = [
    ['/vps-jakarta', 'VPS Jakarta', 'Deploy dekat pusat bisnis Indonesia', 'server'],
    ['/vps-bogor', 'VPS Bogor', 'Cloud lokal dengan resource fleksibel', 'server'],
    ['/vps', 'Bandingkan semua', 'Temukan konfigurasi yang tepat', 'layers'],
];
$companyMenu = [
    ['/about', 'Tentang kami', 'Kenali RasolTek lebih dekat', 'globe'],
    ['/contact', 'Kontak', 'Hubungi tim kami', 'mail'],
    ['/privacy', 'Kebijakan privasi', 'Informasi perlindungan data', 'shield'],
    ['/terms', 'Syarat layanan', 'Ketentuan penggunaan layanan', 'lock'],
];
?>
<header class="site-header" data-header><div class="container nav-wrap">
<a class="brand" href="/" aria-label="RasolTek Hosting beranda"><?php require __DIR__ . '/../components/brand-logo.php'; ?></a>
<nav class="desktop-nav" aria-label="Navigasi utama">
<a<?= nav_link_attributes('/') ?> href="/">Home</a>
<a<?= nav_link_attributes('/domain') ?> href="/domain">Domain</a>
<a<?= nav_link_attributes('/hosting') ?> href="/hosting">Hosting</a>
<div class="nav-dropdown"><button class="<?= nav_active('vps') ?>" type="button" aria-expanded="false" aria-controls="vps-menu">VPS</button><div class="dropdown-panel" id="vps-menu">
<?php foreach ($vpsMenu as [$path, $label, $description, $symbol]): ?>
<a<?= nav_link_attributes($path) ?> href="<?= e($path) ?>"><span class="nav-item-icon"><?= icon($symbol) ?></span><span><b><?= e($label) ?></b><small><?= e($description) ?></small></span></a>
<?php endforeach; ?>
</div></div>
<a<?= nav_link_attributes('/ssl') ?> href="/ssl">SSL</a>
<div class="nav-dropdown"><button class="<?= nav_active('company') ?>" type="button" aria-expanded="false" aria-controls="company-menu">Company</button><div class="dropdown-panel" id="company-menu">
<?php foreach ($companyMenu as [$path, $label, $description, $symbol]): ?>
<a<?= nav_link_attributes($path) ?> href="<?= e($path) ?>"><span class="nav-item-icon"><?= icon($symbol) ?></span><span><b><?= e($label) ?></b><small><?= e($description) ?></small></span></a>
<?php endforeach; ?>
</div></div>
</nav>
<div class="nav-actions"><button class="theme-toggle" type="button" aria-label="Ganti tema" title="Ganti tema"><svg class="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"/></svg><svg class="moon-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z"/></svg></button><a class="btn btn-sm" href="<?= e($portal['register_url']) ?>">Mulai sekarang <?= icon('arrow') ?></a><button class="menu-btn" type="button" aria-expanded="false" aria-controls="mobile-menu" aria-label="Buka menu"><?= icon('menu') ?></button></div>
</div><div id="mobile-menu" class="mobile-menu" hidden><nav aria-label="Navigasi mobile">
<a<?= nav_link_attributes('/') ?> href="/">Home</a>
<a<?= nav_link_attributes('/domain') ?> href="/domain">Domain</a>
<a<?= nav_link_attributes('/hosting') ?> href="/hosting">Hosting</a>
<details class="mobile-nav-group<?= nav_active('vps') ?>"<?= nav_active('vps') ? ' open' : '' ?>>
<summary><span>VPS</span><span class="mobile-nav-caret"><?= icon('arrow') ?></span></summary>
<div class="mobile-submenu">
<?php foreach ($vpsMenu as [$path, $label, $description, $symbol]): ?>
<a<?= nav_link_attributes($path) ?> href="<?= e($path) ?>"><span class="nav-item-icon"><?= icon($symbol) ?></span><span><b><?= e($label) ?></b><small><?= e($description) ?></small></span></a>
<?php endforeach; ?>
</div>
</details>
<a<?= nav_link_attributes('/ssl') ?> href="/ssl">SSL</a>
<details class="mobile-nav-group<?= nav_active('company') ?>"<?= nav_active('company') ? ' open' : '' ?>>
<summary><span>Company</span><span class="mobile-nav-caret"><?= icon('arrow') ?></span></summary>
<div class="mobile-submenu">
<?php foreach ($companyMenu as [$path, $label, $description, $symbol]): ?>
<a<?= nav_link_attributes($path) ?> href="<?= e($path) ?>"><span class="nav-item-icon"><?= icon($symbol) ?></span><span><b><?= e($label) ?></b><small><?= e($description) ?></small></span></a>
<?php endforeach; ?>
</div>
</details>
<a class="mobile-cta" href="<?= e($portal['register_url']) ?>">Mulai sekarang <?= icon('arrow') ?></a>
</nav></div></header>
