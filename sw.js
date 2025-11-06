const CACHE_NAME = 'lizplay-cache-v1';
const urlsToCache = [
    '/',
    '/index.php',
    '/public/css/bootstrap.css',
    '/public/css/player.css',
    '/public/css/slick.css',
    '/public/css/slick-theme.css',
    '/public/css/style.css',
    '/public/js/app.js',
    '/public/js/bootstrap.js',
    '/public/js/fontawesome.min.js',
    '/public/js/jquery.lazy.min.js',
    '/public/js/jquery-3.6.0.min.js',
    '/public/js/player.js',
    '/public/js/slick.js',
    '/public/imagens/icon-192.png',
    '/public/imagens/icon-512.png'
];

// Instalação: Cacheia os arquivos essenciais
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

// Busca: Tenta servir a partir do cache, senão vai para a rede
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Retorna item do cache se encontrado
                if (response) {
                    return response;
                }
                // Senão, busca na rede
                return fetch(event.request);
            })
    );
});

// Ativação: Limpa caches antigos
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});