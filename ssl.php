<?php
$pageKey = 'ssl';
$pageTitle = 'SSL Certificate Indonesia | RasolTek Hosting';
$pageDescription = 'Pilihan Sectigo PositiveSSL dan PositiveSSL Wildcard untuk melindungi domain dan subdomain website Anda.';
$canonicalPath = 'ssl';
require __DIR__ . '/includes/header.php';
$sslCatalog = require __DIR__ . '/config/ssl.php';
$heroTitle = 'Kepercayaan dimulai dari koneksi aman.';
$heroDescription = 'Lindungi koneksi website dengan sertifikat Sectigo. Pilih perlindungan untuk satu domain atau seluruh subdomain Anda.';
$heroEyebrow = 'SSL CERTIFICATES';
$heroIcon = 'shield';
$heroCta = ['label' => 'Pilih sertifikat SSL', 'url' => '#ssl-plans'];
require __DIR__ . '/components/page-hero.php';
?>
<section class="section section-soft" id="ssl-plans"><div class="container">
<div class="section-head split"><div><p class="eyebrow">Sertifikat Sectigo</p><h2>Keamanan sesuai kebutuhan.</h2></div><a class="text-link" href="<?= e($portal['ssl_store_url']) ?>">Lihat katalog SSL <?= icon('arrow') ?></a></div>
<div class="ssl-grid">
<?php foreach ($sslCatalog['plans'] as $plan): ?>
<article class="price-card ssl-card<?= $plan['popular'] ? ' popular' : '' ?>">
<?php if ($plan['popular']): ?><span class="popular-label">Perlindungan subdomain</span><?php endif; ?>
<div class="ssl-card-head"><span class="icon-box"><?= icon($plan['icon']) ?></span><span class="ssl-coverage"><?= e($plan['tag']) ?></span></div>
<h3><?= e($plan['name']) ?></h3><p class="ssl-description"><?= e($plan['description']) ?></p>
<div class="price-line"><span><?= rupiah($plan['price']) ?></span><small>/tahun</small></div><p class="equivalent">Ditagihkan setiap tahun</p>
<ul class="feature-list"><?php foreach ($plan['features'] as $feature): ?><li><?= icon('check') ?><span><?= e($feature) ?></span></li><?php endforeach; ?></ul>
<a class="btn btn-block" href="<?= e($plan['order_url']) ?>">Pilih sertifikat <?= icon('arrow') ?></a>
</article>
<?php endforeach; ?>
</div>
<p class="catalog-note">Harga dan ketentuan pemesanan terbaru dapat dilihat di <a href="<?= e($portal['ssl_store_url']) ?>">portal RasolTek</a>.</p>
</div></section>
<section class="section"><div class="container"><div class="feature-grid"><article class="feature"><?= icon('lock') ?><h3>Koneksi terenkripsi</h3><p>Membantu melindungi data saat berpindah antara browser dan server.</p></article><article class="feature"><?= icon('shield') ?><h3>Kepercayaan pengunjung</h3><p>HTTPS memberi indikator bahwa koneksi ke website menggunakan enkripsi.</p></article><article class="feature"><?= icon('globe') ?><h3>Untuk website modern</h3><p>Fondasi keamanan untuk website bisnis, toko online, dan aplikasi.</p></article></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
