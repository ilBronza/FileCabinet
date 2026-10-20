const state = require('./filecabinet/offline/store.js');

async function startOfflineQuestionnaire() {
    'use strict';
    const base = new URL('./', location.href);
    const params = new URLSearchParams(location.search);
    const cabinet = params.get('cabinet');
    const owner = params.get('owner');
    const $ = id => document.getElementById(id);
    const documentKey = owner + ':' + cabinet;
    let db, documentData, rows = new Map(), syncing = false, refreshing = false, saving = 0;
    let storageFailed = false, authBlocked = false, timer;
    const unsaved = new Map();
    const elements = new Map();
    const channel = 'BroadcastChannel' in window ? new BroadcastChannel('filecabinet-offline') : null;
    const el = (tag, text, className) => {
        const node = document.createElement(tag);
        if (text !== undefined) node.textContent = text;
        if (className) node.className = className;
        return node;
    };
    function notice(message) { $('notice').textContent = message || ''; $('notice').hidden = !message; }
    function storageError(error) {
        storageFailed = true;
        notice('Salvataggio locale non riuscito. Mantieni aperta la pagina ed esporta subito una copia. ' + error.message);
        updateStatus();
    }
    function updateStatus() {
        const dirty = [...rows.values()].filter(row => row.dirty || row.pending).length;
        const problems = [...rows.values()].filter(row => row.problem).length;
        $('status').textContent = storageFailed ? 'Attenzione: alcune risposte non sono salvate' : saving ? 'Salvataggio sul dispositivo…' :
            syncing ? 'Sincronizzazione in corso…' : dirty ? `Salvato sul dispositivo · ${dirty} risposte da sincronizzare` :
            documentData ? 'Tutte le risposte locali sono sincronizzate' : 'Nessun questionario scaricato';
        if (problems) $('status').textContent += ` · ${problems} da verificare`;
        $('sync').disabled = !documentData || syncing || refreshing || !!saving || storageFailed;
        $('refresh').disabled = !db || syncing || refreshing || !!saving || storageFailed;
        $('export').disabled = !documentData;
    }
    async function request(path, options = {}) {
        const controller = new AbortController();
        const timeout = setTimeout(() => controller.abort(), 20000);
        try {
            const response = await fetch(new URL(path, base), {
                credentials: 'same-origin', cache: 'no-store', ...options,
                headers: { Accept: 'application/json', ...options.headers }, signal: controller.signal
            });
            if (response.redirected)
                throw Object.assign(new Error('Accedi dalla versione online, poi torna qui e premi Sincronizza ora.'), { status: 401 });
            if (!(response.headers.get('Content-Type') || '').includes('application/json'))
                throw Object.assign(new Error(response.ok ? 'Accedi dalla versione online, poi torna qui e premi Sincronizza ora.' : 'Risposta del server non disponibile.'), {
                    status: response.ok ? 401 : response.status
                });
            const body = await response.json();
            if (!response.ok) throw Object.assign(new Error(body.message || 'Richiesta non riuscita.'), { status: response.status, body });
            return body;
        } finally { clearTimeout(timeout); }
    }
    async function session() {
        const data = await request('api/session');
        if (data.owner !== owner)
            throw Object.assign(new Error('La sessione appartiene a un altro utente. Accedi con l’utente che ha scaricato questo questionario.'), { status: 403 });
        return data;
    }
    async function reloadRows() {
        rows = new Map((await db.rows(documentKey)).map(row => [row.field.id, row]));
    }
    function showValue(value) { return value === null ? '(vuoto)' : typeof value === 'object' ? JSON.stringify(value) : String(value); }
    function setInputValue(input, row) {
        if (row.field.type === 'multipleSelect') {
            const values = (row.value || []).map(String);
            for (const option of input.options) option.selected = values.includes(option.value);
        } else input.value = row.value === null ? '' : String(row.value);
    }
    function readInput(input, field) {
        if (field.type === 'multipleSelect') return [...input.selectedOptions].map(option => option.value);
        if (input.value === '') return null;
        if (field.type === 'boolean') return input.value === 'true';
        // Keep decimal strings intact rather than introducing floating point rounding.
        return input.value;
    }
    function updateRow(row, updateInput = false) {
        rows.set(row.field.id, row);
        const entry = elements.get(row.field.id);
        if (!entry) return;
        entry.status.textContent = row.problem ? 'Da verificare' : row.dirty || row.pending ? 'Salvato sul dispositivo · da inviare' : row.field.reason || 'Sincronizzato';
        if (updateInput && document.activeElement !== entry.input && entry.input) setInputValue(entry.input, row);
        entry.problem.replaceChildren();
        entry.problem.hidden = !row.problem;
        if (row.problem) {
            entry.problem.append(el('div', row.problem.message));
            if (row.problem.field) {
                entry.problem.append(el('pre', 'Risposta sul server: ' + showValue(row.problem.field.value)));
                for (const [label, keepLocal] of [['Usa risposta del server', false], ['Conserva la mia risposta', true]]) {
                    if (keepLocal && !row.problem.field.editable) continue;
                    const button = el('button', label);
                    button.onclick = async () => {
                        try {
                            const resolved = await db.mutate(row.key, current => state.resolve(current, keepLocal));
                            rows.set(resolved.field.id, resolved);
                            entry.container.replaceWith(makeField(resolved));
                            channel?.postMessage(documentKey);
                            updateStatus();
                            scheduleSync();
                        } catch (error) { storageError(error); }
                    };
                    entry.problem.append(button);
                }
            }
        }
    }
    function makeField(row) {
        const field = row.field;
        const container = el('div', undefined, 'field');
        const label = el('label', field.label);
        label.htmlFor = 'field-' + field.id;
        if (field.required) label.append(el('small', 'Obbligatorio'));
        container.append(label);
        if (field.description) container.append(el('p', field.description, 'description'));
        let input;
        if (['boolean', 'singleSelect', 'multipleSelect'].includes(field.type)) {
            input = el('select');
            input.multiple = field.type === 'multipleSelect';
            if (!input.multiple) { const option = el('option', 'Seleziona…'); option.value = ''; input.append(option); }
            const options = field.type === 'boolean' ? [{ value: 'true', label: 'Sì' }, { value: 'false', label: 'No' }] : field.options;
            for (const value of options) { const option = el('option', value.label); option.value = value.value; input.append(option); }
        } else if (field.type === 'textarea') input = el('textarea');
        else {
            input = el('input');
            input.type = ({ date: 'date', datetime: 'datetime-local', integer: 'number', decimal: 'number' })[field.type] || 'text';
            if (input.type === 'number') input.step = field.type === 'integer' ? '1' : 'any';
            if (input.type === 'datetime-local') input.step = '1';
        }
        input.id = label.htmlFor;
        input.disabled = !field.editable || row.problem?.kind === 'removed';
        input.setAttribute('aria-required', field.required ? 'true' : 'false');
        input.autocomplete = 'off';
        setInputValue(input, row);
        const status = el('p', '', 'field-status');
        status.id = 'status-' + field.id;
        const problem = el('div', undefined, 'problem');
        problem.id = 'problem-' + field.id;
        input.setAttribute('aria-describedby', status.id + ' ' + problem.id);
        container.append(input, status, problem);
        elements.set(field.id, { container, input, status, problem });
        input.addEventListener('input', async () => {
            const value = readInput(input, field);
            unsaved.set(field.id, value);
            saving++;
            updateStatus();
            try {
                const updated = await db.mutate(row.key, current => state.edit(current, value));
                updateRow(updated);
                if (JSON.stringify(unsaved.get(field.id)) === JSON.stringify(value)) unsaved.delete(field.id);
                channel?.postMessage(documentKey);
                scheduleSync();
            } catch (error) { storageError(error); }
            finally { saving--; updateStatus(); }
        });
        updateRow(row);
        return container;
    }
    function render() {
        elements.clear();
        $('questionnaire').replaceChildren();
        $('sections').replaceChildren();
        $('title').textContent = documentData.title || 'Questionario';
        $('summary').textContent = `${documentData.sections.length} sezioni · copia scaricata il ${new Date(documentData.downloadedAt).toLocaleString('it-IT')}`;
        for (const section of documentData.sections) {
            const container = el('section');
            container.id = 'section-' + section.id;
            container.append(el('h2', section.title));
            if (section.description) container.append(el('p', section.description, 'description'));
            for (const id of section.fields) if (rows.has(id)) container.append(makeField(rows.get(id)));
            const link = el('a', section.title);
            link.href = '#' + container.id;
            $('sections').append(link);
            $('questionnaire').append(container);
        }
        if (!documentData.sections.length) $('questionnaire').append(el('p', 'Questo fascicolo non contiene dossier. Apri online il fascicolo da compilare.'));
        updateStatus();
    }
    function scheduleSync() { clearTimeout(timer); timer = setTimeout(() => synchronize(), 1500); }
    async function synchronize(manual = false) {
        if (!db || !documentData || syncing || refreshing || storageFailed || saving || (!manual && authBlocked)) return;
        syncing = true;
        if (manual) { authBlocked = false; notice(''); }
        updateStatus();
        try {
            await reloadRows();
            const candidates = [...rows.values()].filter(row => row.dirty && !row.problem && row.field.editable);
            if (!candidates.length) return;
            const credentials = await session();
            notice('');
            for (const candidate of candidates) {
                if (storageFailed) break;
                const row = await db.mutate(candidate.key, current => state.prepare(current, owner, crypto.randomUUID()));
                if (!row.pending || row.problem) continue;
                updateRow(row);
                const sent = row.pending.payload;
                try {
                    const result = await request(`api/sync/${encodeURIComponent(cabinet)}/${encodeURIComponent(row.field.dossierId)}`, {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': credentials.csrf }, body: JSON.stringify(sent)
                    });
                    updateRow(await db.mutate(row.key, current => state.acknowledge(current, result)), true);
                } catch (error) {
                    if (error.status === 409 || error.status === 422 || error.status === 404) {
                        updateRow(await db.mutate(row.key, current => {
                            if (current.pending?.payload.operation_id !== sent.operation_id) return current;
                            const wasEdited = current.generation !== current.pending.generation;
                            current.pending = null;
                            current.problem = error.status === 422 && wasEdited ? null : {
                                kind: error.status === 409 ? 'conflict' : error.status === 404 ? 'removed' : 'validation',
                                message: error.body?.errors ? Object.values(error.body.errors).flat().join(' ') : error.message,
                                field: error.body?.field
                            };
                            return current;
                        }));
                    } else throw error;
                }
                channel?.postMessage(documentKey);
            }
        } catch (error) {
            if ([401, 403, 419].includes(error.status)) {
                authBlocked = true;
                notice(error.status === 419 ? 'Sessione scaduta. Premi Sincronizza ora per riprovare; se necessario accedi dalla versione online.' : error.message);
            } else if (['QuotaExceededError', 'InvalidStateError', 'OfflineStorageError'].includes(error.name)) storageError(error);
            else notice('Server non raggiungibile o temporaneamente non disponibile. Le risposte restano sul dispositivo; riproveremo automaticamente.');
        } finally { syncing = false; updateStatus(); }
    }
    async function refresh() {
        if (refreshing || syncing || saving || storageFailed) return;
        refreshing = true;
        updateStatus();
        try {
            await session();
            const snapshot = await request('api/snapshot/' + encodeURIComponent(cabinet));
            if (snapshot.owner !== owner || snapshot.id !== cabinet) throw new Error('Questionario non corrispondente.');
            documentData = await db.install(snapshot);
            await reloadRows();
            notice('');
            render();
            channel?.postMessage(documentKey);
        } catch (error) {
            if (['QuotaExceededError', 'OfflineStorageError'].includes(error.name)) storageError(error);
            else notice(error.status ? error.message : 'Download non riuscito. Riprova quando il server è raggiungibile.');
        } finally { refreshing = false; updateStatus(); }
    }
    async function exportCopy() {
        // Include even input still in memory when IndexedDB is full or a write has failed.
        let exported;
        try { exported = await db.rows(documentKey); } catch { exported = [...rows.values()]; }
        for (const row of exported) if (unsaved.has(row.field.id)) { row.value = unsaved.get(row.field.id); row.dirty = true; }
        const data = { format: 'filecabinet-offline-backup-v1', exportedAt: new Date().toISOString(), document: documentData, rows: exported };
        const url = URL.createObjectURL(new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' }));
        const link = el('a');
        link.href = url;
        link.download = 'filecabinet-' + cabinet.replace(/[^a-zA-Z0-9_-]/g, '_') + '.json';
        link.click();
        setTimeout(() => URL.revokeObjectURL(url), 1000);
    }
    async function prepareOffline() {
        if (!('serviceWorker' in navigator)) { $('offline-ready').textContent = 'Riapertura offline non disponibile in questo browser'; return; }
        try {
            await navigator.serviceWorker.register(new URL('sw.js', base), { scope: base.pathname });
            await navigator.serviceWorker.ready;
            $('offline-ready').textContent = 'Pagina disponibile offline';
        } catch { $('offline-ready').textContent = 'Pagina non ancora disponibile offline: mantienila aperta'; }
    }
    $('sync').onclick = () => synchronize(true);
    $('refresh').onclick = refresh;
    $('export').onclick = () => exportCopy().catch(storageError);
    window.addEventListener('beforeunload', event => {
        if (saving || storageFailed) { event.preventDefault(); event.returnValue = ''; }
    });
    window.addEventListener('online', () => synchronize());
    window.addEventListener('focus', () => synchronize());
    document.addEventListener('visibilitychange', () => { if (!document.hidden) synchronize(); });
    channel?.addEventListener('message', async event => {
        if (event.data !== documentKey || saving || storageFailed || !db) return;
        await reloadRows();
        for (const row of rows.values()) updateRow(row, true);
        updateStatus();
    });
    try {
        if (!cabinet || !/^[a-f0-9]{64}$/.test(owner || '')) throw new Error('Apri un fascicolo in FileCabinet e scegli “Compila offline”.');
        if (!window.isSecureContext) throw new Error('La compilazione offline richiede HTTPS (oppure localhost per sviluppo).');
        $('online').href = new URL('../filecabinets/' + encodeURIComponent(cabinet) + '/populate', base).href;
        $('online').target = '_blank';
        $('online').rel = 'noopener';
        $('online').hidden = false;
        db = await state.Database.open();
        documentData = await db.getDocument(documentKey);
        if (documentData) { await reloadRows(); render(); }
        else await refresh();
        prepareOffline();
        // Persistence is best effort; an export remains available even if denied.
        navigator.storage?.persist?.().catch(() => {});
        await synchronize();
        setInterval(() => { if (!document.hidden) synchronize(); }, 30000);
    } catch (error) { notice(error.message); $('status').textContent = 'Compilazione offline non disponibile'; }
}

function bootOfflineQuestionnaire() {
    const screen = document.querySelector('[data-filecabinet-offline]');
    if (!screen || screen.dataset.filecabinetStarted) return;
    screen.dataset.filecabinetStarted = 'true';
    startOfflineQuestionnaire();
}

// Safe to require from the host application's shared app.js, including in <head>.
if (document.readyState === 'loading')
    document.addEventListener('DOMContentLoaded', bootOfflineQuestionnaire, { once: true });
else
    bootOfflineQuestionnaire();
