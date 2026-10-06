<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/env.php';
load_env_file();

$portalUrl = rtrim(getenv('PORTAL_URL') ?: 'https://portal.rasoltek.com', '/');

return [
    'portal_url' => $portalUrl,
    'ssl_store_url' => $portalUrl . '/store/ssl-certificates',
    'domain_search_url' => getenv('DOMAIN_SEARCH_URL') ?: $portalUrl . '/cart.php?a=add&domain=register',
    'login_url' => getenv('LOGIN_URL') ?: $portalUrl,
    'register_url' => getenv('REGISTER_URL') ?: $portalUrl . '/register.php',
    'support_url' => getenv('SUPPORT_URL') ?: $portalUrl,
    'knowledgebase_url' => getenv('KNOWLEDGEBASE_URL') ?: $portalUrl,
    'network_status_url' => getenv('NETWORK_STATUS_URL') ?: $portalUrl,
];
