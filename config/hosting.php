<?php
declare(strict_types=1);

// 1 GB: flat monthly rate for all durations. 5/10 GB: discounts of 5%, 10%, 15%.
// Keep these frontend prices synchronized with the portal billing catalog.
$hostingFeatures = ['Unlimited Bandwidth', 'SSH/Terminal Access', 'Support Web API MikroTik', 'Support (NodeJS, Python, Ruby, dll)'];

return [
    'source_url' => 'https://portal.rasoltek.com/store/hosting',
    'plans' => [
        [
            'name' => 'Cheap Hosting 1 GB',
            'description' => 'Untuk website pertama, blog, dan proyek personal.',
            'price' => 8000,
            'prices' => ['monthly' => 8000, 'quarterly' => 24000, 'semiannual' => 48000, 'annual' => 96000],
            'period' => '/bulan',
            'domains' => 5,
            'storage_gb' => 1,
            'email_accounts' => 5,
            'subdomains' => 5,
            'mysql_databases' => 5,
            'features' => $hostingFeatures,
            'free_domain_extensions' => ['.my.id'],
            'free_domain_min_months' => 12,
            'popular' => false,
            'order_url' => 'https://portal.rasoltek.com/store/hosting/cheap-hosting-1-gb',
        ],
        [
            'name' => 'Cheap Hosting 5 GB',
            'description' => 'Ruang lebih luas untuk beberapa website dan kebutuhan bisnis.',
            'price' => 16000,
            'prices' => ['monthly' => 16000, 'quarterly' => 45600, 'semiannual' => 86400, 'annual' => 163200],
            'period' => '/bulan',
            'domains' => 25,
            'storage_gb' => 5,
            'email_accounts' => 25,
            'subdomains' => 25,
            'mysql_databases' => 25,
            'features' => $hostingFeatures,
            'free_domain_extensions' => ['.my.id'],
            'free_domain_min_months' => 12,
            'popular' => true,
            'order_url' => 'https://portal.rasoltek.com/store/hosting/cheap-hosting-5-gb',
        ],
        [
            'name' => 'Cheap Hosting 10 GB',
            'description' => 'Kapasitas penyimpanan lebih besar untuk website yang berkembang.',
            'price' => 25000,
            'prices' => ['monthly' => 25000, 'quarterly' => 71250, 'semiannual' => 135000, 'annual' => 255000],
            'period' => '/bulan',
            'domains' => 'Unlimited',
            'storage_gb' => 10,
            'email_accounts' => 'Unlimited',
            'subdomains' => 'Unlimited',
            'mysql_databases' => 'Unlimited',
            'features' => $hostingFeatures,
            'free_domain_extensions' => ['.my.id'],
            'free_domain_min_months' => 12,
            'popular' => false,
            'order_url' => 'https://portal.rasoltek.com/store/hosting/cheap-hosting-10-gb',
        ],
    ],
];
