<?php $company = require __DIR__ . '/../config/company.php'; ?>
</main><footer class="site-footer"><div class="container footer-grid">
<div class="footer-brand"><a class="brand brand-light" href="/" aria-label="RasolTek Hosting beranda"><?php require __DIR__ . '/../components/brand-logo.php'; ?></a><p>Infrastruktur digital lokal untuk membantu bisnis dan ide Anda tumbuh dengan fondasi yang lebih andal.</p>
<address class="footer-contact"><strong><?= e($company['name']) ?></strong><a href="<?= e($company['maps_url']) ?>" target="_blank" rel="noopener noreferrer"><?= icon('pin') ?><span><?= e($company['address']) ?></span></a><a href="<?= e($company['phone_url']) ?>"><?= icon('phone') ?><span><?= e($company['phone']) ?></span></a><a href="<?= e($company['website']) ?>"><?= icon('globe') ?><span>rasoltek.co.id</span></a></address>
<a class="footer-map-link" href="<?= e($company['maps_url']) ?>" target="_blank" rel="noopener noreferrer">Lihat lokasi di Maps <?= icon('arrow') ?></a></div>
<div><h3>Produk</h3><a href="/domain">Domain</a><a href="/hosting">Cheap Hosting</a><a href="/vps-jakarta">VPS Jakarta</a><a href="/vps-bogor">VPS Bogor</a><a href="/ssl">SSL Certificates</a></div>
<div><h3>Dukungan</h3><a href="<?= e($portal['portal_url']) ?>">Client Area</a><a href="<?= e($portal['support_url']) ?>">Support Ticket</a><a href="<?= e($portal['knowledgebase_url']) ?>">Knowledgebase</a><a href="<?= e($portal['network_status_url']) ?>">Network Status</a></div>
<div><h3>Perusahaan</h3><a href="<?= e($company['website']) ?>">RasolTek</a><a href="/about">Tentang kami</a><a href="/contact">Kontak</a><a href="/privacy">Privacy Policy</a><a href="/terms">Terms of Service</a></div>
</div><div class="container footer-bottom"><p>&copy; <?= date('Y') ?> RasolTek Hosting. All Rights Reserved.</p><p>Banyuwangi &middot; Indonesia</p></div></footer>
<script src="<?= e(asset_url('assets/js/app.js')) ?>" defer></script></body></html>
