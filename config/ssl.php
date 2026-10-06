<?php
declare(strict_types=1);

// Catalog snapshot verified at the source on 2026-10-04; update when portal pricing changes.
return [
    'source_url' => 'https://portal.rasoltek.com/store/ssl-certificates',
    'plans' => [
        [
            'name' => 'Sectigo PositiveSSL',
            'tag' => 'Satu domain',
            'description' => 'Perlindungan HTTPS untuk website pribadi, blog, dan situs bisnis dengan satu domain utama.',
            'price' => 100000,
            'features' => ['Melindungi satu domain utama', 'Kompatibel dengan server dan browser modern', 'Penerbitan cepat tanpa dokumen tambahan'],
            'icon' => 'lock',
            'popular' => false,
            'order_url' => 'https://portal.rasoltek.com/store/ssl/ssl-domain-name',
        ],
        [
            'name' => 'Sectigo PositiveSSL Wildcard',
            'tag' => 'Domain & subdomain',
            'description' => 'Satu sertifikat untuk domain utama dan seluruh subdomain, agar pengelolaan keamanan lebih praktis.',
            'price' => 1150000,
            'features' => ['Melindungi domain utama dan seluruh subdomain', 'Kompatibel dengan server dan browser modern', 'Penerbitan cepat tanpa dokumen tambahan'],
            'icon' => 'shield',
            'popular' => true,
            'order_url' => 'https://portal.rasoltek.com/store/ssl/sectigo-positivessl-wildcard',
        ],
    ],
];
