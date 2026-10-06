<?php /** @var array $plan */ $location = $location ?? 'Jakarta'; $hideLocation = basename($_SERVER['SCRIPT_NAME'] ?? '') === 'index.php' && $location === 'Bogor'; ?>
<article class="price-card vps-card<?= !empty($plan['popular']) ? ' popular' : '' ?><?= $hideLocation ? ' hidden-location' : '' ?>" data-vps-card data-location="<?= e(strtolower($location)) ?>">
<?php if (!empty($plan['popular'])): ?><span class="popular-label">Terlaris</span><?php endif; ?>
<div class="card-top"><div><p class="eyebrow"><?= icon('pin') ?> <?= e($location) ?>, Indonesia</p><h3><?= e($plan['name']) ?></h3></div><span class="availability">Tersedia</span></div>
<div class="spec-grid"><span><?= icon('cpu') ?><b><?= (int)$plan['cpu'] ?> vCPU</b><small>Compute</small></span><span><?= icon('layers') ?><b><?= (int)$plan['ram'] ?> GB</b><small>RAM</small></span><span><?= icon('drive') ?><b><?= (int)$plan['storage'] ?> GB</b><small>NVMe</small></span></div>
<div class="price-line"><span data-price data-prices='<?= e(json_encode($plan['prices'])) ?>'><?= rupiah($plan['prices']['monthly']) ?></span><small data-period>/bulan</small></div><p class="equivalent" data-equivalent>Ditagihkan setiap bulan</p>
<a class="btn btn-block order-link" data-base-url="<?= e($plan['order_url']) ?>" href="<?= e($plan['order_url']) ?>">Deploy VPS <?= icon('arrow') ?></a>
<?php if ($plan['bandwidth'] || $plan['virtualization'] || $plan['ipv4']): ?><details><summary>Detail lanjutan</summary><ul><?php foreach (['bandwidth'=>'Bandwidth','virtualization'=>'Virtualization','ipv4'=>'IPv4'] as $key=>$label): if($plan[$key]): ?><li><?= e($label) ?>: <?= e((string)$plan[$key]) ?></li><?php endif; endforeach; ?></ul></details><?php endif; ?>
</article>
