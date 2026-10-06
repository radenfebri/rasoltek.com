// Self-contained diagnostics: temporary native PHP server, no npm dependencies.
// Does not edit .env or any hosting/FlyEnv configuration. No RDASH requests.
import assert from 'node:assert/strict';
import http from 'node:http';
import net from 'node:net';
import { spawn, spawnSync } from 'node:child_process';
import { readdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const root = fileURLToPath(new URL('../', import.meta.url));
const origin = 'https://seo-check.rasoltek.com';
const routes = ['', 'domain', 'hosting', 'vps', 'vps-jakarta', 'vps-bogor', 'ssl', 'about', 'contact', 'privacy', 'terms'];
const sleep = ms => new Promise(resolve => setTimeout(resolve, ms));
let assertions = 0;
function check(value, message) { assert.ok(value, message); assertions++; }
const phpBinary = process.env.PHP_BINARY || 'php';
for (const directory of ['', 'api', 'components', 'config', 'includes']) {
  for (const name of readdirSync(path.join(root, directory)).filter(name => name.endsWith('.php'))) {
    const file = path.join(root, directory, name);
    const result = spawnSync(phpBinary, ['-l', file], { encoding: 'utf8', windowsHide: true });
    check(result.status === 0, `PHP syntax ${directory}/${name}: ${result.stderr || result.stdout}`);
  }
}
function request(port, url, { method = 'GET', host = 'seo-check.rasoltek.com' } = {}) {
  return new Promise((resolve, reject) => {
    const req = http.request({ hostname: '127.0.0.1', port, path: url, method, headers: { Host: host } }, res => {
      const chunks = [];
      res.on('data', chunk => chunks.push(chunk));
      res.on('end', () => resolve({ status: res.statusCode, headers: res.headers, body: Buffer.concat(chunks).toString('utf8') }));
    });
    req.setTimeout(8000, () => req.destroy(new Error('Test request timeout')));
    req.on('error', reject);
    req.end();
  });
}
async function withServer(overrides, callback) {
  const listener = net.createServer();
  await new Promise(resolve => listener.listen(0, '127.0.0.1', resolve));
  const port = listener.address().port;
  await new Promise(resolve => listener.close(resolve));
  const child = spawn(phpBinary, ['-S', `127.0.0.1:${port}`, 'router.php'], {
    cwd: root, env: { ...process.env, APP_URL: origin, APP_ENV: 'production', SEO_INDEXING: 'true', ...overrides },
    stdio: ['ignore', 'ignore', 'pipe'], windowsHide: true,
  });
  let log = '', startupError;
  child.stderr.on('data', chunk => { log = (log + chunk).slice(-4000); });
  child.on('error', error => { startupError = error; });
  try {
    let started = false;
    for (let attempt = 0; attempt < 100; attempt++) {
      if (startupError) throw startupError;
      if (child.exitCode !== null) throw new Error(`PHP server exited: ${log}`);
      try { await request(port, '/robots.txt'); started = true; break; } catch { await sleep(50); }
    }
    check(started, 'PHP diagnostic server starts');
    await callback(port);
    check(!/(?:PHP Warning|PHP Fatal|PHP Parse|Uncaught)/i.test(log), 'No PHP warnings or fatal errors');
  } finally {
    child.kill();
    if (child.exitCode === null) await new Promise(resolve => child.once('exit', resolve));
  }
}

await withServer({}, async port => {
  const titles = new Set(), descriptions = new Set(), assets = new Set();
  for (const route of routes) {
    const url = '/' + route;
    const page = await request(port, url + '?utm_source=seo-check');
    check(page.status === 200, `${url} is HTTP 200`);
    check(page.headers['x-robots-tag']?.startsWith('index,follow'), `${url} allows production indexing`);
    check(page.body.includes(`rel="canonical" href="${origin}${url}"`), `${url} canonical excludes query`);
    check((page.body.match(/<h1(?:\s|>)/g) || []).length === 1, `${url} has one H1`);
    check(page.body.includes('<html lang="id">'), `${url} has Indonesian language`);
    check(page.body.includes(`property="og:image" content="${origin}/assets/images/logo.png"`), `${url} has absolute social image`);
    check(page.body.includes('name="twitter:image"'), `${url} has Twitter image`);
    const title = page.body.match(/<title>(.*?)<\/title>/s)?.[1];
    const description = page.body.match(/name="description" content="([^"]+)"/)?.[1];
    check(title && !titles.has(title), `${url} unique title`);
    check(description && !descriptions.has(description), `${url} unique description`);
    titles.add(title); descriptions.add(description);
    const schema = JSON.parse(page.body.match(/<script type="application\/ld\+json">(.*?)<\/script>/s)?.[1] || 'null');
    check(schema?.['@context'] === 'https://schema.org', `${url} valid JSON-LD`);
    check(schema['@graph'].some(node => node['@type'] === 'Organization'), `${url} Organization schema`);
    check(schema['@graph'].some(node => node['@type'] === 'WebSite'), `${url} WebSite schema`);
    const webpage = schema['@graph'].find(node => node['@id'] === `${origin}${url}#webpage`);
    check(webpage?.url === origin + url, `${url} schema matches canonical`);
    if (route) {
      const breadcrumb = schema['@graph'].find(node => node['@type'] === 'BreadcrumbList');
      check(breadcrumb.itemListElement.at(-1).item === origin + url, `${url} breadcrumb matches page`);
      check(page.body.includes('aria-label="Breadcrumb"'), `${url} visible breadcrumb`);
      if (route.startsWith('vps-')) check(breadcrumb.itemListElement.length === 3, `${url} VPS hierarchy`);
    }
    for (const match of page.body.matchAll(/(?:src|href)="(\/assets\/[^"<>]+)"/g)) assets.add(match[1].replaceAll('&amp;', '&'));
    for (const match of page.body.matchAll(/href="(\/[^"<>]*)"/g)) {
      const target = match[1].split(/[?#]/)[0];
      if (!target.startsWith('/assets/')) check(routes.includes(target.slice(1)), `${url} internal link ${target} exists`);
    }
  }
  for (const asset of assets) check((await request(port, asset)).status === 200, `Static asset ${asset.split('?')[0]} serves`);
  const sitemap = await request(port, '/sitemap.xml');
  check(sitemap.status === 200 && sitemap.headers['content-type'].includes('application/xml'), 'Sitemap XML HTTP/content-type');
  check(sitemap.body.startsWith('<?xml version="1.0" encoding="UTF-8"?>'), 'XML declaration');
  const parsedXml = spawnSync(phpBinary, ['-r', 'libxml_use_internal_errors(true); $xml = simplexml_load_string(stream_get_contents(STDIN)); if ($xml === false) exit(1); if ($xml->getName() !== "urlset") exit(2); $ns = $xml->getNamespaces(true); if (($ns[""] ?? "") !== "http://www.sitemaps.org/schemas/sitemap/0.9") exit(3); echo count($xml->url);'], { input: sitemap.body, encoding: 'utf8', windowsHide: true });
  check(parsedXml.status === 0 && parsedXml.stdout === '11', 'Sitemap parsed by XML library: namespace and 11 entries');
  const locations = [...sitemap.body.matchAll(/<loc>(.*?)<\/loc>/g)].map(match => match[1]);
  check(JSON.stringify(locations) === JSON.stringify(routes.map(route => `${origin}/${route}`)), 'Sitemap has exactly 11 public canonical URLs');
  check(!/localhost|\.php|\/api\/|\/404/.test(sitemap.body), 'No local/private/error URLs in production sitemap');
  for (const match of sitemap.body.matchAll(/<lastmod>(.*?)<\/lastmod>/g)) {
    check(!Number.isNaN(Date.parse(match[1])) && Date.parse(match[1]) <= Date.now() + 60000, 'lastmod is valid and not future');
  }
  check((await request(port, '/sitemap.xml')).body === sitemap.body, 'Sitemap dates stable across requests');
  const robots = await request(port, '/robots.txt');
  check(robots.status === 200 && robots.headers['content-type'].includes('text/plain'), 'Robots HTTP/content-type');
  check(robots.body.includes(`Sitemap: ${origin}/sitemap.xml`), 'Robots points to canonical sitemap');
  check(!/^Disallow: \/$/m.test(robots.body), 'Production crawling not globally blocked');
  check(!robots.body.includes('Disallow: /assets/'), 'CSS/JS crawlable');
  for (const [alias, canonical] of [['/index.php', '/'], ['/index', '/'], ['/hosting.php', '/hosting'], ['/hosting/', '/hosting'], ['/VPS-JAKARTA', '/vps-jakarta'], ['/sitemap.php', '/sitemap.xml'], ['/sitemap', '/sitemap.xml'], ['/robots.php', '/robots.txt']]) {
    const response = await request(port, alias);
    check(response.status === 301 && response.headers.location === canonical, `${alias} redirects once to ${canonical}`);
  }
  for (const url of ['/404', '/not-an-existing-page', '/config/app.php', '/includes/env.php', '/.env', '/router.php']) {
    const page = await request(port, url);
    check(page.status === 404, `${url} is HTTP 404`);
    check(page.headers['x-robots-tag'] === 'noindex,follow', `${url} noindex`);
    check(!page.body.includes('rel="canonical"'), `${url} no misleading canonical`);
  }
  const local = await request(port, '/hosting', { host: 'localhost' });
  check(local.headers['x-robots-tag'] === 'noindex,follow', 'Local host is noindex even with production APP_URL');
  check((await request(port, '/robots.txt', { host: 'localhost' })).body.includes('Disallow: /'), 'Local host crawling blocked');
  for (const url of ['/sitemap.xml', '/robots.txt']) {
    check((await request(port, url, { method: 'HEAD' })).body === '', `${url} HEAD has no body`);
    check((await request(port, url, { method: 'POST' })).status === 405, `${url} rejects POST`);
  }
});
for (const overrides of [{ APP_ENV: 'staging' }, { SEO_INDEXING: 'false' }, { SEO_INDEXING: '0' }, { APP_URL: 'http://localhost' }, { APP_URL: 'https://seo-check.rasoltek.com?invalid=query' }]) {
  await withServer(overrides, async port => {
    check((await request(port, '/hosting')).headers['x-robots-tag'] === 'noindex,follow', `Noindex guard ${JSON.stringify(overrides)}`);
    check((await request(port, '/robots.txt')).body.includes('Disallow: /'), `Crawl guard ${JSON.stringify(overrides)}`);
  });
}
console.log(`SEO checks passed: ${assertions} assertions, 11 pages, sitemap/robots, redirects, static assets, 404 and staging/local guards.`);
