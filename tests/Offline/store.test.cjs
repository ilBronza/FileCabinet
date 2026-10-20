const test = require('node:test');
const assert = require('node:assert/strict');
const state = require('../../resources/assets/js/filecabinet/offline/store.js');
const field = () => ({ id: 'row', dossierId: 'section', type: 'text', editable: true, value: 'original', schema: 's1', revision: 'r1' });
const edited = () => state.edit(state.fresh('owner:cabinet', field()), 'local');

test('a lost response reuses exactly the same operation and answer after another local edit', () => {
    let row = state.prepare(edited(), 'owner', 'operation-one');
    row = state.edit(row, 'new local');
    row = state.prepare(row, 'owner', 'operation-two');
    assert.equal(row.pending.payload.operation_id, 'operation-one');
    assert.equal(row.pending.payload.value, 'local');
    row = state.acknowledge(row, { operation_id: 'operation-one', field: { ...field(), value: 'local', revision: 'r2' } });
    assert.equal(row.value, 'new local');
    assert.equal(row.dirty, true);
    row = state.prepare(row, 'owner', 'operation-three');
    assert.equal(row.pending.payload.revision, 'r2');
    assert.equal(row.pending.payload.value, 'new local');
});

test('an acknowledgement marks only the sent generation as saved', () => {
    const row = state.prepare(edited(), 'owner', 'one');
    state.acknowledge(row, { operation_id: 'one', field: { ...field(), value: 'local', revision: 'r2' } });
    assert.equal(row.dirty, false);
    assert.equal(row.pending, null);
    assert.equal(row.field.revision, 'r2');
});

test('refresh preserves unsent input and reports server changes', () => {
    const row = state.merge(edited(), { ...field(), value: 'server', revision: 'r2' });
    assert.equal(row.value, 'local');
    assert.equal(row.problem.kind, 'conflict');
    const prepared = state.prepare(row, 'owner', 'one');
    assert.equal(prepared.pending, null);
});

test('refresh preserves an uncertain operation until the server acknowledges it', () => {
    const row = state.prepare(edited(), 'owner', 'one');
    state.merge(row, { ...field(), value: 'local', revision: 'r2' });
    assert.equal(row.pending.payload.operation_id, 'one');
    assert.equal(row.field.revision, 'r1');
    assert.equal(row.problem, null);
});

test('explicit conflict resolution rebases the local answer on the server version', () => {
    let row = state.merge(edited(), { ...field(), value: 'server', revision: 'r2' });
    row = state.resolve(row, true);
    assert.equal(row.value, 'local');
    assert.equal(row.field.revision, 'r2');
    assert.equal(row.dirty, true);
    assert.equal(row.problem, null);
    row = state.merge(row, { ...field(), value: 'latest', revision: 'r3' });
    row = state.resolve(row, false);
    assert.equal(row.value, 'latest');
    assert.equal(row.dirty, false);
});

test('read-only schema changes cannot be resolved by uploading local values', () => {
    const row = state.merge(edited(), { ...field(), editable: false, revision: 'r2' });
    state.resolve(row, true);
    assert.equal(row.problem.kind, 'conflict');
    state.resolve(row, false);
    assert.equal(row.field.editable, false);
    assert.equal(row.dirty, false);
});

test('a stale response cannot acknowledge a different operation', () => {
    const row = state.prepare(edited(), 'owner', 'two');
    state.acknowledge(row, { operation_id: 'one', field: { ...field(), revision: 'r2' } });
    assert.equal(row.dirty, true);
    assert.equal(row.pending.payload.operation_id, 'two');
});

test('null, false, zero and multiple selections are preserved in the durable payload', () => {
    for (const value of [null, false, 0, '', ['A', 'B']]) {
        const row = state.prepare(state.edit(state.fresh('doc', field()), value), 'owner', 'one');
        assert.deepEqual(row.pending.payload.value, value);
    }
});
