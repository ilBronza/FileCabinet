<?php

namespace IlBronza\FileCabinet\Offline;

class OfflineAssets
{
    public function urls() : array
    {
        $manifestPath = public_path('mix-manifest.json');
        $manifest = is_file($manifestPath) ? json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR) : [];
        $resolve = function (string $path) use ($manifest) : string {
            $path = '/' . ltrim($path, '/');
            $versioned = $manifest[$path] ?? $path;
            // Mix without .version() still writes a manifest. Version those files too,
            // so each worker release retains the matching bundle even after deployment.
            if ($versioned === $path && is_file(public_path($path)))
                $versioned .= '?id=' . substr(hash_file('sha256', public_path($path)), 0, 20);

            // Keep offline resources on the application's origin, even with ASSET_URL set.
            return url($versioned);
        };

        return [
            'scripts' => array_map($resolve, config('filecabinet.offline.assets.scripts', ['js/app.js'])),
            'styles' => array_map($resolve, config('filecabinet.offline.assets.styles', ['css/ilbronza.filecabinet.offline.css'])),
            'cache' => array_map($resolve, config('filecabinet.offline.assets.cache', [])),
        ];
    }

    public function worker() : string
    {
        $assets = $this->urls();
        $source = file_get_contents(__DIR__ . '/../../resources/offline/sw.js');
        $settings = [
            'shell' => route(config('filecabinet.routePrefix') . 'offline.shell'),
            'assets' => array_values(array_unique(array_merge(...array_values($assets)))),
        ];
        $settings['version'] = hash('sha256', $source . json_encode($settings, JSON_THROW_ON_ERROR));

        return 'self.FILECABINET_OFFLINE = ' . json_encode($settings, JSON_THROW_ON_ERROR | JSON_HEX_TAG) . ";\n" . $source;
    }
}
