<?php
$pageKey = 'contact';
$pageTitle = 'Hubungi RasolTek Hosting';
$pageDescription = 'Hubungi tim RasolTek Hosting untuk pertanyaan layanan, akun, dan kebutuhan infrastruktur digital.';
$canonicalPath = 'contact';
require __DIR__ . '/includes/header.php';
$heroTitle = 'Mari bicarakan kebutuhan Anda.';
$heroDescription = 'Isi formulir di bawah untuk menyiapkan pesan di aplikasi email Anda.';
$heroEyebrow = 'CONTACT';
require __DIR__ . '/components/page-hero.php';
?>
<section class="section section-soft"><div class="container contact-grid">
<div><div class="section-head"><p class="eyebrow">Hubungi kami</p><h2>Kami siap membantu.</h2></div>
<div class="contact-cards">
<a class="contact-card" href="mailto:admin@rasoltek.com"><?= icon('mail') ?><span><h3>Email</h3><p>admin@rasoltek.com</p></span></a>
<a class="contact-card" href="<?= e($portal['portal_url']) ?>"><?= icon('server') ?><span><h3>Client Area</h3><p>Kelola layanan dan pemesanan</p></span></a>
<a class="contact-card" href="https://www.rasoltek.co.id"><?= icon('globe') ?><span><h3>Corporate Website</h3><p>Informasi lengkap RasolTek</p></span></a>
</div></div>
<form id="contact-form" class="contact-form" action="mailto:admin@rasoltek.com" method="get" data-contact-form>
<div class="form-grid">
<div class="field"><label for="name">Nama</label><input id="name" name="name" autocomplete="name" maxlength="100" required></div>
<div class="field"><label for="email">Email</label><input id="email" name="email" type="email" autocomplete="email" maxlength="254" required></div>
<div class="field full"><label for="subject">Topik</label><input id="subject" name="subject" maxlength="180" required></div>
<div class="field full"><label for="message">Pesan</label><textarea id="message" name="body" maxlength="10000" required></textarea></div>
<div class="field full"><button class="btn" type="submit">Kirim pesan <?= icon('arrow') ?></button><p class="contact-mail-note">Aplikasi email Anda akan terbuka dengan pesan yang sudah terisi. Tekan Kirim di aplikasi tersebut untuk mengirimnya.</p></div>
<div class="field full contact-mail-status" data-contact-status role="status" hidden>Pesan siap dibuka di aplikasi email. <a data-contact-mail-link href="mailto:admin@rasoltek.com">Buka email</a></div>
</div></form></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
