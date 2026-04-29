const CACHE = 'beefit-v1';

self.addEventListener('install', e => {
    self.skipWaiting();
});

self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

// Network-first: tenta rede, fallback mensagem offline para navegação
self.addEventListener('fetch', e => {
    if (e.request.mode !== 'navigate') return;
    e.respondWith(
        fetch(e.request).catch(() =>
            new Response('<html><body style="background:#09090b;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0"><p>Sem ligação à internet.</p></body></html>',
                { headers: { 'Content-Type': 'text/html' } })
        )
    );
});
