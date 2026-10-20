/* Run with Node and Playwright available (uses the installed Chrome). No external services. */
const { chromium } = require('playwright');
const assert = require('node:assert/strict');
const http = require('node:http');
const fs = require('node:fs');
const path = require('node:path');
const os = require('node:os');
const webpack = require('webpack');
const buildDirectory = fs.mkdtempSync(path.join(os.tmpdir(), 'filecabinet-webpack-'));
const root = path.resolve(__dirname, '../..');
const prefix = '/filecabinet-manager/offline/';
const owner = 'a'.repeat(64);
let currentOwner = owner, unavailable = false, dropNextResponse = false, writes = 0;
const receipts = new Map();
const fields = [
    { id: 'name', dossierId: 'one', label: 'Nome del referente', type: 'text', required: true, value: 'Mario Rossi' },
    { id: 'notes', dossierId: 'one', label: 'Note del sopralluogo', type: 'textarea', value: null },
    { id: 'checked', dossierId: 'two', label: 'Controllo completato', type: 'boolean', value: null },
    { id: 'count', dossierId: 'two', label: 'Numero di elementi', type: 'integer', value: null },
    { id: 'choices', dossierId: 'two', label: 'Aree verificate', type: 'multipleSelect', options: [{ value: 'A', label: 'Magazzino' }, { value: 'B', label: 'Uffici' }], value: null },
    { id: 'file', dossierId: 'two', label: 'Fotografie', type: 'file', editable: false, reason: 'Da completare online', value: null },
].map(field => ({ editable: true, options: [], schema: 's1', revision: 'r1', ...field }));
const snapshot = () => ({ format: 1, owner, id: 'cabinet', title: 'Sopralluogo · Sede di Milano', downloadedAt: new Date().toISOString(), sections: [
    { id: 'one', title: '01 · Informazioni generali', description: 'Compila i dati del referente e le osservazioni sul sopralluogo.', fields: fields.filter(f => f.dossierId === 'one') },
    { id: 'two', title: '02 · Verifiche operative', fields: fields.filter(f => f.dossierId === 'two') },
] });
let html = fs.readFileSync(path.join(root, 'resources/views/offline/shell.blade.php'), 'utf8');
const scriptUrl = '/js/app.js?id=bundle-one';
const styleUrl = '/css/ilbronza.filecabinet.offline.css?id=style-one';
html = html.replace(/@foreach\(\$assets\['styles'\] as \$style\)[\s\S]*?@endforeach/, `<link rel="stylesheet" href="${styleUrl}">`);
html = html.replace(/@foreach\(\$assets\['scripts'\] as \$script\)[\s\S]*?@endforeach/, `<script defer src="${scriptUrl}"></script>`);
const server = http.createServer(async (req, res) => {
    const pathname = new URL(req.url, 'http://localhost').pathname;
    const send = (status, body) => { res.writeHead(status, { 'Content-Type': 'application/json', 'Cache-Control': 'no-store' }); res.end(JSON.stringify(body)); };
    if (pathname.includes('/api/') && unavailable === 'html') { res.writeHead(503, { 'Content-Type': 'text/html' }); return res.end('Gateway unavailable'); }
    if (pathname.includes('/api/') && unavailable) return send(503, { message: 'Temporarily unavailable' });
    if (pathname === prefix + 'app') { res.writeHead(200, { 'Content-Type': 'text/html' }); return res.end(html); }
    if (pathname === '/ordinary') { res.writeHead(200, { 'Content-Type': 'text/html' }); return res.end(`<html><head><script src="${scriptUrl}"></script></head><body>Ordinary app page</body></html>`); }
    if (pathname === '/js/app.js') {
        res.writeHead(200, { 'Content-Type': 'application/javascript' });
        return res.end(fs.readFileSync(path.join(buildDirectory, 'app.js')));
    }
    if (pathname === '/css/ilbronza.filecabinet.offline.css') {
        res.writeHead(200, { 'Content-Type': 'text/css' });
        return res.end(fs.readFileSync(path.join(root, 'resources/assets/css/ilbronza.filecabinet.offline.css')));
    }
    if (pathname === prefix + 'sw.js') {
        res.writeHead(200, { 'Content-Type': 'application/javascript' });
        const settings = { version: 'webpack-v2', shell: prefix + 'app', assets: [scriptUrl, styleUrl] };
        return res.end('self.FILECABINET_OFFLINE = ' + JSON.stringify(settings) + ';\n' + fs.readFileSync(path.join(root, 'resources/offline/sw.js'), 'utf8'));
    }
    if (pathname === prefix + 'api/session') return send(200, { owner: currentOwner, csrf: 'test-token' });
    if (pathname === prefix + 'api/snapshot/cabinet') return send(200, snapshot());
    if (pathname.startsWith(prefix + 'api/sync/')) {
        let raw = ''; for await (const chunk of req) raw += chunk;
        const body = JSON.parse(raw);
        if (body.owner !== currentOwner) return send(403, { message: 'Wrong user' });
        if (receipts.has(body.operation_id)) return send(200, receipts.get(body.operation_id));
        const field = fields.find(f => f.id === body.row_id);
        if (body.revision !== field.revision || body.schema !== field.schema) return send(409, { message: 'Il campo è cambiato sul server.', field });
        field.value = body.value;
        field.revision += 'x';
        writes++;
        const response = { operation_id: body.operation_id, field: structuredClone(field) };
        receipts.set(body.operation_id, response);
        if (dropNextResponse) { dropNextResponse = false; req.socket.destroy(); return; }
        return send(200, response);
    }
    res.writeHead(404); res.end();
});
async function waitFor(check, message) {
    const end = Date.now() + 15000;
    while (!check()) { if (Date.now() > end) throw new Error(message); await new Promise(r => setTimeout(r, 100)); }
}
(async () => {
    // Exercise the same require() integration as the host application's app.js.
    const entry = path.join(buildDirectory, 'entry.js');
    fs.writeFileSync(entry, 'require(' + JSON.stringify(path.join(root, 'resources/assets/js/ilbronza.filecabinet.offline.js')) + ');');
    await new Promise((resolve, reject) => {
        const compiler = webpack({ mode: 'production', entry, target: 'web', devtool: false,
            output: { path: buildDirectory, filename: 'app.js' } });
        compiler.run((error, stats) => compiler.close(closeError => {
            if (error || closeError) reject(error || closeError);
            else if (stats.hasErrors()) reject(new Error(stats.toString({ all: false, errors: true })));
            else resolve();
        }));
    });
    console.log('PASS webpack production build with CommonJS entry and bundled store');
    await new Promise(resolve => server.listen(0, '127.0.0.1', resolve));
    const browser = await chromium.launch({ channel: 'chrome', headless: true });
    try {
        const context = await browser.newContext({ viewport: { width: 1280, height: 960 } });
        const page = await context.newPage();
        const errors = [];
        page.on('pageerror', error => errors.push(error.message));
        const url = `http://127.0.0.1:${server.address().port}${prefix}app?cabinet=cabinet&owner=${owner}`;
        await page.goto(new URL('/ordinary', url).href);
        assert.deepEqual(await page.evaluate(async () => await indexedDB.databases()), []);
        assert.equal(await page.evaluate(async () => (await navigator.serviceWorker.getRegistrations()).length), 0);
        assert.deepEqual(errors, []);
        console.log('PASS shared app.js does not initialize the questionnaire on other pages');
        unavailable = true;
        await page.goto(url);
        await page.waitForFunction(() => document.getElementById('notice').textContent.includes('Temporarily unavailable'));
        assert.equal(await page.locator('#refresh').isEnabled(), true);
        unavailable = false;
        await page.getByRole('button', { name: 'Aggiorna questionario' }).click();
        await page.waitForSelector('#field-name');
        console.log('PASS failed initial download can be retried without reloading');
        await page.waitForFunction(() => navigator.serviceWorker.controller);
        await page.waitForFunction(() => document.getElementById('offline-ready').textContent === 'Pagina disponibile offline');
        assert.equal(await page.locator('#field-name').inputValue(), 'Mario Rossi');
        await page.screenshot({ path: '/tmp/filecabinet-offline-desktop.png', fullPage: true });
        await context.setOffline(true);
        await page.locator('#field-notes').fill('Compilato senza connessione');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('1 risposte da sincronizzare'));
        await page.reload();
        await page.waitForSelector('#field-notes');
        assert.equal(await page.locator('#field-notes').inputValue(), 'Compilato senza connessione');
        await context.setOffline(false);
        await waitFor(() => fields[1].value === 'Compilato senza connessione', 'Reconnect did not sync');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        console.log('PASS offline edit, full reload and reconnect synchronization');

        fields[0].value = 'Modifica da un altro dispositivo'; fields[0].revision += 'x';
        await page.locator('#field-name').fill('La mia risposta');
        await page.getByRole('button', { name: 'Conserva la mia risposta' }).waitFor();
        assert.equal(fields[0].value, 'Modifica da un altro dispositivo');
        await page.getByRole('button', { name: 'Conserva la mia risposta' }).click();
        await waitFor(() => fields[0].value === 'La mia risposta', 'Conflict resolution did not sync');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        console.log('PASS conflict requires explicit resolution');

        const before = writes;
        dropNextResponse = true;
        await page.locator('#field-notes').fill('Risposta con conferma persa');
        await waitFor(() => fields[1].value === 'Risposta con conferma persa', 'Dropped response test did not commit');
        await page.waitForFunction(() => !document.getElementById('sync').disabled);
        await page.getByRole('button', { name: 'Sincronizza ora' }).click();
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        assert.equal(writes, before + 1);
        console.log('PASS lost response retry uses same operation without a second write');

        currentOwner = 'b'.repeat(64);
        await page.locator('#field-count').fill('0');
        await page.waitForFunction(() => document.getElementById('notice').textContent.includes('altro utente'));
        assert.equal(fields[3].value, null);
        currentOwner = owner;
        await page.getByRole('button', { name: 'Sincronizza ora' }).click();
        await waitFor(() => fields[3].value === '0', 'Correct user did not sync');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        console.log('PASS account mismatch blocks transmission without losing local answers');

        await page.locator('#field-checked').selectOption('false');
        await page.locator('#field-choices').selectOption(['A', 'B']);
        await waitFor(() => fields[2].value === false && fields[4].value?.length === 2, 'Boolean/multiple values did not sync');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        const downloadPromise = page.waitForEvent('download');
        await page.getByRole('button', { name: 'Esporta copia' }).click();
        const download = await downloadPromise;
        await download.saveAs('/tmp/filecabinet-offline-backup.json');
        const backup = JSON.parse(fs.readFileSync('/tmp/filecabinet-offline-backup.json'));
        assert.equal(backup.rows.find(r => r.field.id === 'checked').value, false);
        assert.deepEqual(backup.rows.find(r => r.field.id === 'choices').value, ['A', 'B']);
        console.log('PASS false, zero, multiple choices and downloadable backup');

        unavailable = 'html';
        await page.locator('#field-notes').fill('Rete instabile');
        await page.waitForFunction(() => document.getElementById('notice').textContent.includes('Server non raggiungibile'));
        unavailable = false;
        await context.setOffline(true);
        await context.setOffline(false);
        await waitFor(() => fields[1].value === 'Rete instabile', 'HTML gateway error incorrectly blocked automatic retries');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        console.log('PASS HTML gateway errors remain retryable without another login');

        // A schema refresh must not discard a local answer, and resolving it rebuilds the input.
        await context.setOffline(true);
        await page.locator('#field-name').fill('Local draft');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('da sincronizzare'));
        fields[0].type = 'singleSelect'; fields[0].schema = 's2'; fields[0].revision += 'x';
        fields[0].options = [{ value: 'Local draft', label: 'Local draft' }, { value: 'Server choice', label: 'Server choice' }];
        fields[0].value = 'Server choice';
        await context.setOffline(false);
        await page.getByRole('button', { name: 'Conserva la mia risposta' }).waitFor();
        await page.getByRole('button', { name: 'Conserva la mia risposta' }).click();
        await page.waitForFunction(() => document.getElementById('field-name').tagName === 'SELECT');
        assert.equal(await page.locator('#field-name').evaluate(node => node.tagName), 'SELECT');
        await waitFor(() => fields[0].value === 'Local draft', 'Schema resolution did not retain local draft');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        console.log('PASS changed question type retains local answer and rebuilds its control');

        const cachedUrls = await page.evaluate(async () => {
            const urls = [];
            for (const name of await caches.keys()) for (const request of await (await caches.open(name)).keys()) urls.push(request.url);
            return urls;
        });
        assert.equal(cachedUrls.length, 3);
        assert.equal(cachedUrls.some(url => url.endsWith(scriptUrl)), true);
        assert.equal(cachedUrls.some(url => url.endsWith(styleUrl)), true);
        assert.equal(cachedUrls.some(url => url.includes('/api/')), false);
        console.log('PASS service worker caches the shell and versioned compiled assets only');

        await page.setViewportSize({ width: 390, height: 844 });
        await page.evaluate(() => window.scrollTo(0, 0));
        await page.evaluate(() => new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve))));
        await page.screenshot({ path: '/tmp/filecabinet-offline-mobile.png' });
        assert.equal(await page.evaluate(() => document.documentElement.scrollWidth <= innerWidth), true);
        assert.equal(await page.locator('header').count(), 1);
        assert.deepEqual(errors, []);
        console.log('PASS mobile layout and no uncaught browser exceptions');

        for (let i = 0; i < 1000; i++) fields.push({ id: 'large-' + i, dossierId: 'two', label: 'Domanda ' + i,
            type: 'text', value: null, editable: true, options: [], schema: 's1', revision: 'r1' });
        await page.getByRole('button', { name: 'Aggiorna questionario' }).click();
        await page.waitForSelector('#field-large-999');
        assert.equal(await page.locator('.field').count(), 1006);
        await context.setOffline(true);
        await page.locator('#field-large-999').fill('Ultima risposta del questionario');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('da sincronizzare'));
        await page.reload();
        await page.waitForSelector('#field-large-999');
        assert.equal(await page.locator('#field-large-999').inputValue(), 'Ultima risposta del questionario');
        await context.setOffline(false);
        await waitFor(() => fields.at(-1).value === 'Ultima risposta del questionario', 'Large questionnaire did not sync');
        await page.waitForFunction(() => document.getElementById('status').textContent.includes('Tutte le risposte'));
        console.log('PASS 1,006-field questionnaire survives offline edits and reload');

        // IndexedDB failure must never be presented as a successful save; backup includes unsaved input.
        await context.setOffline(true);
        await page.evaluate(async () => {
            IDBObjectStore.prototype.put = function () { throw new DOMException('Simulated full disk', 'QuotaExceededError'); };
        });
        await page.locator('#field-notes').fill('Recover this unsaved answer');
        await page.waitForFunction(() => document.getElementById('notice').textContent.includes('Salvataggio locale non riuscito'));
        assert.equal(await page.locator('#sync').isDisabled(), true);
        const recoveryPromise = page.waitForEvent('download');
        await page.getByRole('button', { name: 'Esporta copia' }).click();
        const recovery = await recoveryPromise;
        await recovery.saveAs('/tmp/filecabinet-offline-recovery.json');
        assert.equal(JSON.parse(fs.readFileSync('/tmp/filecabinet-offline-recovery.json')).rows.find(r => r.field.id === 'notes').value, 'Recover this unsaved answer');
        console.log('PASS failed local write blocks sync and preserves input in recovery export');
        await context.close();
    } finally { await browser.close(); server.close(); fs.rmSync(buildDirectory, { recursive: true, force: true }); }
})().catch(error => { console.error(error); server.close(); fs.rmSync(buildDirectory, { recursive: true, force: true }); process.exitCode = 1; });
