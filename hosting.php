<?php
$pageKey = 'hosting';
$pageTitle = 'Hosting Murah SSD mulai Rp8.000/bulan | RasolTek Hosting';
$pageDescription = 'Hosting SSD 1 GB, 5 GB, dan 10 GB mulai Rp8.000/bulan. Mendukung SSH, Node.js, Python, Ruby, dan API MikroTik. Gratis .my.id untuk pembelian satu tahun.';
$canonicalPath = 'hosting';
require __DIR__ . '/includes/header.php';
$heroTitle = 'Hosting cepat, harga bersahabat.';
$heroDescription = 'Hosting SSD dengan email, database, akses SSH, dan dukungan Web API MikroTik. Mulai dari Rp8.000 per bulan untuk membawa website Anda online.';
$heroEyebrow = 'WEB HOSTING INDONESIA';
$heroIcon = 'server';
$heroCta = ['label' => 'Pilih paket hosting', 'url' => '#hosting-plans'];
require __DIR__ . '/components/page-hero.php';
?>
<section class="section section-soft" id="hosting-plans"><div class="container">
<div class="section-head"><p class="eyebrow">Pilih hosting</p><h2>Ruang untuk setiap tahap.</h2><p>Pilih kapasitas SSD sesuai kebutuhan. Setiap paket mendukung SSH/Terminal dan Web API MikroTik.</p></div>
<div class="hosting-billing"><div><strong>Pilih periode berlangganan</strong><p>Harga yang ditampilkan adalah total untuk periode pilihan Anda. Paket 5 GB dan 10 GB hemat hingga 15% dibanding pembayaran bulanan.</p></div><div class="billing-tabs" role="group" aria-label="Periode tagihan hosting"><button class="tab-btn active" type="button" data-hosting-cycle="monthly" aria-pressed="true">Bulanan</button><button class="tab-btn" type="button" data-hosting-cycle="quarterly" aria-pressed="false">3 bulan</button><button class="tab-btn" type="button" data-hosting-cycle="semiannual" aria-pressed="false">6 bulan</button><button class="tab-btn" type="button" data-hosting-cycle="annual" aria-pressed="false">Tahunan</button></div></div>
<div class="hosting-grid">
<?php foreach ($products['hostingPlans'] as $index => $plan): ?>
<article class="price-card hosting-card<?= $plan['popular'] ? ' popular' : '' ?>" data-hosting-card data-hosting-prices='<?= e(json_encode($plan['prices'])) ?>'>
<?php if ($plan['popular']): ?><span class="popular-label">Terlaris</span><?php endif; ?>
<div class="icon-box"><?= icon(['bolt', 'globe', 'server'][$index]) ?></div>
<p class="eyebrow">SSD WEB HOSTING</p><h3><?= e($plan['name']) ?></h3><p class="hosting-intro"><?= e($plan['description']) ?></p>
<div class="hosting-price-block" aria-live="polite"><div class="hosting-promo" data-hosting-promo hidden><del data-hosting-original aria-label="Total jika membayar bulanan"></del><span class="hosting-saving" data-hosting-saving></span></div><div class="price-line"><span data-hosting-price><?= rupiah($plan['prices']['monthly']) ?></span><small data-hosting-period>/bulan</small></div><p class="hosting-price-note" data-hosting-equivalent>Ditagihkan setiap bulan.</p><p class="hosting-promo-note" data-hosting-promo-note hidden>Dibandingkan total pembayaran bulanan untuk durasi yang sama.</p></div>
<ul class="feature-list hosting-specs">
<?php foreach (['domains' => 'Domain', 'storage_gb' => 'GB SSD Storage', 'email_accounts' => 'Email Account', 'subdomains' => 'Subdomain', 'mysql_databases' => 'MySQL Database'] as $key => $label): ?>
<li><?= icon('check') ?><span><strong><?= e((string) $plan[$key]) ?></strong> <?= e($label) ?></span></li>
<?php endforeach; ?>
<?php foreach ($plan['features'] as $feature): ?><li><?= icon('check') ?><span><?= e($feature) ?></span></li><?php endforeach; ?>
</ul>
<div class="hosting-domain-bonus"><?= icon('globe') ?><div><strong>Gratis domain <?= e(implode(' / ', $plan['free_domain_extensions'])) ?></strong><span>Minimal pembelian <?= (int) ($plan['free_domain_min_months'] / 12) ?> tahun.</span></div></div>
<a class="btn btn-block" data-hosting-order data-base-url="<?= e($plan['order_url']) ?>" href="<?= e($plan['order_url'] . '?billingcycle=monthly') ?>">Pesan hosting <?= icon('arrow') ?></a>
</article>
<?php endforeach; ?>
</div></div></section>
<section class="section"><div class="container"><div class="section-head"><p class="eyebrow">Dibuat untuk memulai</p><h2>Pengelolaan yang tetap sederhana.</h2></div><div class="feature-grid"><article class="feature"><?= icon('drive') ?><h3>SSD Storage</h3><p>Pilihan kapasitas 1 GB, 5 GB, atau 10 GB untuk file dan data website.</p></article><article class="feature"><?= icon('server') ?><h3>SSH/Terminal Access</h3><p>Kelola kebutuhan website melalui akses terminal yang tersedia pada setiap paket.</p></article><article class="feature"><?= icon('layers') ?><h3>Web API MikroTik</h3><p>Dukungan Web API MikroTik untuk kebutuhan integrasi aplikasi Anda.</p></article></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
