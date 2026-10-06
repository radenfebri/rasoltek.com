<?php
declare(strict_types=1);

$hostingPlans = (require __DIR__ . '/hosting.php')['plans'];

function vps(string $name, int $cpu, int $ram, int $storage, array $prices, bool $popular = false): array {
    return ['name'=>$name, 'cpu'=>$cpu, 'ram'=>$ram, 'storage'=>$storage, 'bandwidth'=>null, 'virtualization'=>null, 'ipv4'=>null, 'prices'=>$prices, 'popular'=>$popular, 'status'=>'available', 'order_url'=>'#'];
}

$vpsJakarta = [
    vps('Cloud 1.1.20 NVMe',1,1,20,['monthly'=>57500,'quarterly'=>169500,'semiannual'=>333000,'annual'=>648000]),
    vps('Cloud 2.2.40 NVMe',2,2,40,['monthly'=>115000,'quarterly'=>339000,'semiannual'=>666000,'annual'=>1296000], true),
    vps('Cloud 2.4.60 NVMe',2,4,60,['monthly'=>172500,'quarterly'=>508500,'semiannual'=>999000,'annual'=>1944000]),
    vps('Cloud 4.4.80 NVMe',4,4,80,['monthly'=>230000,'quarterly'=>678000,'semiannual'=>1332000,'annual'=>2592000]),
    vps('Cloud 4.8.100 NVMe',4,8,100,['monthly'=>345000,'quarterly'=>1017000,'semiannual'=>1998000,'annual'=>3888000]),
    vps('Cloud 6.8.120 NVMe',6,8,120,['monthly'=>460000,'quarterly'=>1356000,'semiannual'=>2664000,'annual'=>5184000]),
    vps('Cloud 8.16.160 NVMe',8,16,160,['monthly'=>690000,'quarterly'=>2034000,'semiannual'=>3996000,'annual'=>7776000]),
];

$vpsBogor = [
    vps('ID Cloud 1.1 NVMe',1,1,25,['monthly'=>69000,'quarterly'=>203400,'semiannual'=>399600,'annual'=>777600]),
    vps('ID Cloud 1.2 NVMe',1,2,30,['monthly'=>103500,'quarterly'=>305100,'semiannual'=>599400,'annual'=>1166400]),
    vps('ID Cloud 2.2 NVMe',2,2,50,['monthly'=>110400,'quarterly'=>325440,'semiannual'=>639360,'annual'=>1244160], true),
    vps('ID Cloud 1.4 NVMe',1,4,30,['monthly'=>218500,'quarterly'=>644100,'semiannual'=>1265400,'annual'=>2462400]),
    vps('ID Cloud 2.4 NVMe',2,4,50,['monthly'=>244260,'quarterly'=>720036,'semiannual'=>1414584,'annual'=>2752704]),
    vps('ID Cloud 4.4 NVMe',4,4,75,['monthly'=>402500,'quarterly'=>1186500,'semiannual'=>2331000,'annual'=>4536000]),
    vps('ID Cloud 2.8 NVMe',2,8,50,['monthly'=>438840,'quarterly'=>1293624,'semiannual'=>2541456,'annual'=>4945536]),
    vps('ID Cloud 4.8 NVMe',4,8,75,['monthly'=>571550,'quarterly'=>1684830,'semiannual'=>3310020,'annual'=>6441120]),
];

// Match each product explicitly; portal slugs do not always follow the display name.
$jakartaOrderSlugs = [
    'Cloud 1.1.20 NVMe' => 'cloud-1120-nvme',
    'Cloud 2.2.40 NVMe' => 'cloud-2240-nvme',
    'Cloud 2.4.60 NVMe' => 'cloud-2460-nvme',
    'Cloud 4.4.80 NVMe' => 'cloud-4480-nvme',
    'Cloud 4.8.100 NVMe' => 'cloud-48100-nvme',
    'Cloud 6.8.120 NVMe' => 'cloud-68120-nvme',
    'Cloud 8.16.160 NVMe' => 'cloud-816160-nvme',
];
$bogorOrderSlugs = [
    'ID Cloud 1.1 NVMe' => 'id-cloud-11-nvme',
    'ID Cloud 1.2 NVMe' => 'id-cloud-12-nvme',
    'ID Cloud 1.4 NVMe' => 'id-cloud-14-nvme',
    'ID Cloud 2.2 NVMe' => 'id-cloud-22-nvme',
    'ID Cloud 2.4 NVMe' => 'id-cloud-24-nvme',
    'ID Cloud 2.8 NVMe' => 'id-cloud-28-nvme',
    'ID Cloud 4.4 NVMe' => 'id-cloud-44-nvme',
    'ID Cloud 4.8 NVMe' => 'id-cloud-48-nvme',
];
foreach ($vpsJakarta as &$plan) {
    $plan['order_url'] = 'https://portal.rasoltek.com/store/vps-id-jkt/' . $jakartaOrderSlugs[$plan['name']];
}
unset($plan);
foreach ($vpsBogor as &$plan) {
    $plan['order_url'] = 'https://portal.rasoltek.com/store/vps-id-bgr/' . $bogorOrderSlugs[$plan['name']];
}
unset($plan);

$sslPlans = [];

return compact('hostingPlans', 'vpsJakarta', 'vpsBogor', 'sslPlans');
