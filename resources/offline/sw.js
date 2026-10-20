/* Only cache the public shell and the configured compiled assets, never API responses. */
const settings = self.FILECABINET_OFFLINE;
const BASE = new URL('./', self.location.href);
const CACHE = 'filecabinet-shell-' + settings.version + ':' + BASE.pathname;
const SHELL = new URL(settings.shell, BASE).href;
const ASSETS = [SHELL, ...settings.assets.map(path => new URL(path, BASE).href)];
self.addEventListener('install', event => {
    event.waitUntil(caches.open(CACHE).then(cache => cache.addAll(ASSETS)));
});
self.addEventListener('activate', event => {
    event.waitUntil((async () => {
        for (const key of await caches.keys()) {
            if (key.startsWith('filecabinet-shell-') && key.endsWith(':' + BASE.pathname) && key !== CACHE)
                await caches.delete(key);
        }
        await self.clients.claim();
    })());
});
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    if (event.request.method !== 'GET' || url.origin !== BASE.origin) return;
    // Ignore only the questionnaire's query (owner/id); bundle version queries are significant.
    const key = event.request.mode === 'navigate' && url.origin + url.pathname === SHELL ? SHELL : url.href;
    if (!ASSETS.includes(key)) return;
    event.respondWith((async () => {
        const cache = await caches.open(CACHE);
        return (await cache.match(key)) || fetch(event.request);
    })());
});
