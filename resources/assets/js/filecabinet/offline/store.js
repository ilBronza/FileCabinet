/* Shared persistence/state transitions. No network or DOM access. */
'use strict';
const clone = value => JSON.parse(JSON.stringify(value));
function fresh(documentKey, field) {
    return { key: documentKey + ':' + field.id, documentKey, field, value: field.value,
        generation: 0, dirty: false, pending: null, problem: null };
}
function edit(row, value) {
    row.value = value;
    row.generation++;
    row.dirty = true;
    if (row.problem?.kind === 'validation') row.problem = null;
    return row;
}
function prepare(row, owner, operationId) {
    if (!row.dirty || row.problem || !row.field.editable) return row;
    if (!row.pending) row.pending = { generation: row.generation, payload: {
        owner, operation_id: operationId, row_id: row.field.id,
        schema: row.field.schema, revision: row.field.revision, value: clone(row.value)
    }};
    return row;
}
function acknowledge(row, response) {
    if (row.pending?.payload.operation_id !== response.operation_id) return row;
    if (row.generation === row.pending.generation) {
        row.value = response.field.value;
        row.dirty = false;
    }
    row.field = response.field;
    row.pending = null;
    row.problem = null;
    return row;
}
function merge(row, field) {
    if (!row || (!row.dirty && !row.pending)) return fresh(row?.documentKey, field);
    // Never replace an uncertain request: it may already have committed on the server.
    if (!row.pending && (row.field.schema !== field.schema || row.field.revision !== field.revision))
        row.problem = { kind: 'conflict', message: 'Il campo è cambiato sul server.', field };
    return row;
}
function resolve(row, keepLocal) {
    const field = row.problem?.field;
    if (!field || (keepLocal && !field.editable)) return row;
    row.field = field;
    row.pending = null;
    row.problem = null;
    row.generation++;
    row.dirty = keepLocal;
    if (!keepLocal) row.value = field.value;
    return row;
}
class Database {
    static open() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open('filecabinet-offline-v1', 1);
            request.onupgradeneeded = () => {
                request.result.createObjectStore('documents', { keyPath: 'key' });
                const rows = request.result.createObjectStore('rows', { keyPath: 'key' });
                rows.createIndex('documentKey', 'documentKey');
            };
            request.onerror = () => reject(request.error);
            request.onblocked = () => reject(new Error('Chiudere le altre schede del questionario e riprovare.'));
            request.onsuccess = () => {
                request.result.onversionchange = () => request.result.close();
                resolve(new Database(request.result));
            };
        });
    }
    constructor(db) { this.db = db; }
    transaction(stores, mode, action) {
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(stores, mode);
            let result;
            tx.oncomplete = () => resolve(result);
            tx.onerror = tx.onabort = () => reject(Object.assign(new Error('Accesso al database locale non riuscito.'), {
                name: 'OfflineStorageError', cause: tx.error
            }));
            action(tx, value => { result = value; });
        });
    }
    getDocument(key) {
        return this.transaction(['documents'], 'readonly', (tx, done) => {
            tx.objectStore('documents').get(key).onsuccess = e => done(e.target.result);
        });
    }
    rows(key) {
        return this.transaction(['rows'], 'readonly', (tx, done) => {
            tx.objectStore('rows').index('documentKey').getAll(key).onsuccess = e => done(e.target.result);
        });
    }
    mutate(key, fn) {
        return this.transaction(['rows'], 'readwrite', (tx, done) => {
            const store = tx.objectStore('rows');
            store.get(key).onsuccess = e => {
                if (!e.target.result) { tx.abort(); return; }
                try {
                    const row = fn(e.target.result);
                    store.put(row);
                    done(row);
                } catch { tx.abort(); }
            };
        });
    }
    install(snapshot) {
        const key = snapshot.owner + ':' + snapshot.id;
        return this.transaction(['documents', 'rows'], 'readwrite', (tx, done) => {
            const store = tx.objectStore('rows');
            store.index('documentKey').getAll(key).onsuccess = e => {
                const previous = new Map(e.target.result.map(row => [row.field.id, row]));
                const sections = snapshot.sections.map(section => ({ ...section, fields: section.fields.map(field => {
                    const old = previous.get(field.id);
                    previous.delete(field.id);
                    store.put(old ? merge(old, field) : fresh(key, field));
                    return field.id;
                }) }));
                const removed = [];
                for (const row of previous.values()) {
                    if (row.dirty || row.pending) {
                        row.problem = { kind: 'removed', message: 'Campo rimosso: la risposta locale è conservata nella copia esportabile.' };
                        store.put(row);
                        removed.push(row.field.id);
                    } else store.delete(row.key);
                }
                if (removed.length) sections.push({ id: 'removed', title: 'Risposte da recuperare', fields: removed });
                const document = { ...snapshot, key, sections };
                tx.objectStore('documents').put(document);
                done(document);
            };
        });
    }
}
module.exports = { fresh, edit, prepare, acknowledge, merge, resolve, Database };
