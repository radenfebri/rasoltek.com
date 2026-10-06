<section class="page-hero"><div class="container"><nav class="breadcrumbs" aria-label="Breadcrumb">
<?php foreach ($breadcrumbs as $position => $breadcrumb): ?>
<?php if ($position > 0): ?><span aria-hidden="true">/</span><?php endif; ?>
<?php if ($position === count($breadcrumbs) - 1): ?><span aria-current="page"><?= e($breadcrumb['label']) ?></span><?php else: ?><a href="<?= e(local_url($breadcrumb['path'])) ?>"><?= e($breadcrumb['label']) ?></a><?php endif; ?>
<?php endforeach; ?>
</nav><?php if (!empty($heroIcon)): ?><div class="page-hero-icon" aria-hidden="true"><?= icon($heroIcon) ?></div><?php endif; ?><p class="eyebrow"><?= e($heroEyebrow ?? 'RASOLTEK HOSTING') ?></p><h1><?= e($heroTitle) ?></h1><p><?= e($heroDescription) ?></p><?php if(!empty($heroCta)): ?><div class="hero-actions"><a class="btn" href="<?= e($heroCta['url']) ?>"><?= e($heroCta['label']) ?> <?= icon('arrow') ?></a></div><?php endif; ?></div></section>
