<?php
declare(strict_types=1);

function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function rupiah(int|float $value): string { return 'Rp' . number_format($value, 0, ',', '.'); }
function app_base_path(): string {
    static $basePath = null;
    if ($basePath !== null) {
        return $basePath;
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
    $basePath = ($dir === '' || $dir === '.' || $dir === '/') ? '' : $dir;
    return $basePath;
}
function local_url(string $path = ''): string {
    $base = app_base_path();
    $path = trim($path);
    if ($path === '' || $path === '/') {
        return $base === '' ? '/' : $base . '/';
    }
    if (preg_match('~^(?:https?:)?//|^(?:mailto|tel):|^#~i', $path)) {
        return $path;
    }
    return $base . '/' . ltrim($path, '/');
}
function asset_url(string $path): string {
    $url = local_url($path);
    $file = __DIR__ . '/../' . ltrim($path, '/');
    return is_file($file) ? $url . '?v=' . filemtime($file) : $url;
}
function rewrite_local_urls(string $html): string {
    $base = app_base_path();
    if ($base === '') {
        return $html;
    }
    $basePrefix = ltrim($base, '/');
    return preg_replace_callback('~\b(href|src|action)=(["\'])/([^"\']*)\2~i', function (array $match) use ($base, $basePrefix): string {
        $target = $match[3];
        if ($target === $basePrefix || str_starts_with($target, $basePrefix . '/')) {
            return $match[0];
        }
        return $match[1] . '=' . $match[2] . $base . '/' . $target . $match[2];
    }, $html) ?? $html;
}
function start_local_url_rewrite(): void {
    if (!defined('RASOLTEK_LOCAL_URL_REWRITE')) {
        define('RASOLTEK_LOCAL_URL_REWRITE', true);
        ob_start('rewrite_local_urls');
    }
}
function page_url(string $path = ''): string {
    global $app;
    return $app['url'] . '/' . ltrim($path, '/');
}
function nav_active(string $item): string {
    global $pageKey, $canonicalPath;
    $currentKey = $pageKey ?? $canonicalPath ?? '';
    if ($item === 'company') {
        return in_array($currentKey, ['about', 'contact', 'privacy', 'terms'], true) ? ' is-active' : '';
    }
    return $currentKey === $item ? ' is-active' : '';
}
function nav_link_attributes(string $path): string {
    global $canonicalPath;
    return trim($canonicalPath ?? '', '/') === trim($path, '/')
        ? ' class="is-active" aria-current="page"' : '';
}
function icon(string $name, string $class = 'icon'): string {
    $paths = [
        'arrow'=>'<path d="M5 12h14M13 6l6 6-6 6"/>', 'check'=>'<path d="m5 12 4 4L19 6"/>',
        'globe'=>'<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3.5 3 14 0 18M12 3c-3 3.5-3 14 0 18"/>',
        'server'=>'<rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01"/>',
        'shield'=>'<path d="M12 3 4.5 6v5c0 4.6 3.2 8.3 7.5 10 4.3-1.7 7.5-5.4 7.5-10V6L12 3Z"/><path d="m9 12 2 2 4-4"/>',
        'bolt'=>'<path d="m13 2-9 12h7l-1 8 9-12h-7l1-8Z"/>', 'headset'=>'<path d="M4 14v-2a8 8 0 0 1 16 0v2M6 19H5a2 2 0 0 1-2-2v-1a2 2 0 0 1 2-2h1v5Zm12 0h1a2 2 0 0 0 2-2v-1a2 2 0 0 0-2-2h-1v5ZM18 19c0 2-2 3-5 3"/>',
        'layers'=>'<path d="m12 3-9 5 9 5 9-5-9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>',
        'lock'=>'<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>', 'x'=>'<path d="m6 6 12 12M18 6 6 18"/>',
        'cpu'=>'<rect x="7" y="7" width="10" height="10" rx="1"/><path d="M9 1v3M15 1v3M9 20v3M15 20v3M20 9h3M20 14h3M1 9h3M1 14h3"/>',
        'drive'=>'<path d="M4 5h16l2 14H2L4 5Z"/><path d="M7 15h.01M17 15h.01"/>', 'pin'=>'<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2"/>',
        'mail'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>', 'phone'=>'<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.5c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2.1Z"/>',
    ];
    return '<svg class="'.e($class).'" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">'.($paths[$name] ?? $paths['check']).'</svg>';
}
