<?php
$pageKey = 'vps';
$pageTitle = 'Perbandingan VPS Indonesia | RasolTek Hosting';
$pageDescription = 'Bandingkan paket VPS Jakarta dan VPS Bogor berdasarkan CPU, RAM, NVMe storage, dan pilihan siklus tagihan.';
$canonicalPath = 'vps';
require __DIR__ . '/includes/header.php';
$heroTitle = 'Temukan VPS yang pas untuk Anda.';
$heroDescription = 'Bandingkan kapasitas dan harga Jakarta serta Bogor dalam satu tempat. Pilih lokasi, resource, dan siklus tagihan sesuai kebutuhan.';
$heroEyebrow = 'BANDINGKAN VPS INDONESIA';
$heroIcon = 'layers';
$heroCta = ['label' => 'Bandingkan paket', 'url' => '#compare-plans'];
require __DIR__ . '/components/page-hero.php';
?>
<section class="section section-soft compare-section" id="compare-plans"><div class="container">
<div class="compare-highlights"><span><?= icon('pin') ?><b>Jakarta &amp; Bogor</b><small>Dua lokasi Indonesia</small></span><span><?= icon('drive') ?><b>NVMe Storage</b><small>Kapasitas yang fleksibel</small></span><span><?= icon('layers') ?><b>Pilih siklus tagihan</b><small>Bulanan hingga tahunan</small></span></div>
<div class="billing-wrap compare-billing"><div><p class="eyebrow">Sesuaikan tagihan</p><h2>Satu pandangan. Semua pilihan.</h2></div><div class="billing-tabs" role="tablist" aria-label="Siklus tagihan"><button class="tab-btn active" data-cycle="monthly" role="tab" aria-selected="true">Bulanan</button><button class="tab-btn" data-cycle="quarterly" role="tab" aria-selected="false">3 Bulan</button><button class="tab-btn" data-cycle="semiannual" role="tab" aria-selected="false">6 Bulan</button><button class="tab-btn" data-cycle="annual" role="tab" aria-selected="false">Tahunan</button></div></div>
<?php foreach (['Jakarta' => $products['vpsJakarta'], 'Bogor' => $products['vpsBogor']] as $location => $plans): ?>
<div class="compare-location">
<div class="section-head split"><div class="compare-location-title"><span class="icon-box"><?= icon('server') ?></span><div><p class="eyebrow"><?= icon('pin') ?> <?= e($location) ?>, Indonesia</p><h2>VPS <?= e($location) ?></h2></div></div><a class="btn btn-secondary btn-sm" href="/vps-<?= strtolower($location) ?>">Lihat VPS <?= e($location) ?> <?= icon('arrow') ?></a></div>
<p class="compare-scroll-hint">Geser tabel untuk melihat seluruh spesifikasi, harga, dan tombol pemesanan.</p>
<div class="compare-wrap"><table class="compare-table"><caption class="visually-hidden">Perbandingan paket VPS <?= e($location) ?></caption><thead><tr><th scope="col">Paket</th><th scope="col"><span class="compare-cell"><?= icon('cpu') ?> vCPU</span></th><th scope="col"><span class="compare-cell"><?= icon('layers') ?> RAM</span></th><th scope="col"><span class="compare-cell"><?= icon('drive') ?> NVMe</span></th><th scope="col">Harga</th><th scope="col"><span class="visually-hidden">Pesan paket</span></th></tr></thead><tbody>
<?php foreach ($plans as $plan): $orderUrl = $plan['order_url'] === '#' ? $portal['portal_url'] : $plan['order_url']; ?>
<tr data-compare-row<?= !empty($plan['popular']) ? ' class="compare-popular"' : '' ?>>
<td><span class="compare-plan"><?= icon('server') ?><span><b><?= e($plan['name']) ?></b><?php if (!empty($plan['popular'])): ?><small class="compare-badge">Terlaris</small><?php endif; ?></span></span></td>
<td><span class="compare-cell"><?= icon('cpu') ?> <?= (int) $plan['cpu'] ?> vCPU</span></td>
<td><span class="compare-cell"><?= icon('layers') ?> <?= (int) $plan['ram'] ?> GB</span></td>
<td><span class="compare-cell"><?= icon('drive') ?> <?= (int) $plan['storage'] ?> GB</span></td>
<td><div class="compare-price"><b data-price data-prices='<?= e(json_encode($plan['prices'])) ?>'><?= rupiah($plan['prices']['monthly']) ?></b><small data-period>/bulan</small></div><p class="equivalent" data-equivalent>Ditagihkan setiap bulan</p></td>
<td><a class="btn btn-sm order-link" data-base-url="<?= e($plan['order_url']) ?>" href="<?= e($orderUrl) ?>" aria-label="Pilih <?= e($plan['name']) ?> di <?= e($location) ?>">Pilih paket <?= icon('arrow') ?></a></td>
</tr>
<?php endforeach; ?>
</tbody></table></div></div>
<?php endforeach; ?>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
