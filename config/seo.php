<?php
declare(strict_types=1);

// Only public, canonical HTML pages belong in the sitemap.
return [
    '' => ['label' => 'Beranda', 'type' => 'WebPage', 'files' => ['index.php', 'config/products.php', 'config/hosting.php']],
    'domain' => ['label' => 'Domain', 'type' => 'WebPage', 'files' => ['domain.php']],
    'hosting' => ['label' => 'Hosting', 'type' => 'WebPage', 'files' => ['hosting.php', 'config/hosting.php']],
    'vps' => ['label' => 'Bandingkan VPS', 'type' => 'CollectionPage', 'files' => ['vps.php', 'config/products.php', 'components/vps-card.php']],
    'vps-jakarta' => ['label' => 'VPS Jakarta', 'type' => 'WebPage', 'files' => ['vps-jakarta.php', 'includes/vps-page.php', 'components/vps-card.php', 'config/products.php']],
    'vps-bogor' => ['label' => 'VPS Bogor', 'type' => 'WebPage', 'files' => ['vps-bogor.php', 'includes/vps-page.php', 'components/vps-card.php', 'config/products.php']],
    'ssl' => ['label' => 'SSL Certificates', 'type' => 'WebPage', 'files' => ['ssl.php', 'config/ssl.php']],
    'about' => ['label' => 'Tentang kami', 'type' => 'AboutPage', 'files' => ['about.php']],
    'contact' => ['label' => 'Kontak', 'type' => 'ContactPage', 'files' => ['contact.php']],
    'privacy' => ['label' => 'Kebijakan privasi', 'type' => 'WebPage', 'files' => ['privacy.php']],
    'terms' => ['label' => 'Ketentuan layanan', 'type' => 'WebPage', 'files' => ['terms.php']],
];
