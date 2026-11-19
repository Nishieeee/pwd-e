import { describe, it, beforeEach } from 'node:test';
import assert from 'node:assert/strict';

const store = new Map();
globalThis.localStorage = {
    getItem: (k) => (store.has(k) ? store.get(k) : null),
    setItem: (k, v) => store.set(k, String(v)),
    removeItem: (k) => store.delete(k),
    clear: () => store.clear(),
};

const { suggest, currentWordInfo, addHistory, getHistory, getContext } = await import('../../resources/js/predictive-text-core.js');

beforeEach(() => store.clear());

describe('predictive text - suggest', () => {
    it('returns empty for fewer than 2 chars', () => {
        assert.deepEqual(suggest(''), []);
        assert.deepEqual(suggest('a'), []);
        assert.deepEqual(suggest('a', 'search'), []);
    });

    it('returns up to 5 completions', () => {
        const res = suggest('ac', 'description');
        assert.ok(res.length > 0);
        assert.ok(res.length <= 5);
        for (const w of res) assert.ok(w.toLowerCase().startsWith('ac'));
    });

    it('does not return the exact prefix', () => {
        const res = suggest('the', 'description');
        assert.ok(!res.includes('the'));
    });

    it('includes Filipino vocabulary', () => {
        const res = suggest('mag', 'description');
        assert.ok(res.includes('magandang'));
    });

    it('includes personal history', () => {
        addHistory('mycustomword');
        const res = suggest('myc', 'default');
        assert.ok(res.includes('mycustomword'));
    });

    it('context: search suggests job titles', () => {
        const res = suggest('acc', 'search');
        assert.ok(res.some(w => w.includes('accessibility')));
    });

    it('context: default excludes job titles (less noise)', () => {
        const res = suggest('acc', 'default');
        // default only has common words; 'accessible' not in common
        assert.ok(!res.includes('accessibility specialist'));
    });

    it('context auto-detect from input', () => {
        const input = { tagName: 'TEXTAREA', name: 'description', id: 'desc' };
        assert.equal(getContext(input), 'description');
        const input2 = { tagName: 'INPUT', type: 'search', name: 'q', id: 'q' };
        assert.equal(getContext(input2), 'search');
        const input3 = { tagName: 'INPUT', type: 'text', name: 'title', id: 'title' };
        assert.equal(getContext(input3), 'title');
    });
});

describe('predictive text - currentWordInfo', () => {
    it('extracts the trailing word', () => {
        const info = currentWordInfo('hello wor', 9);
        assert.equal(info.word, 'wor');
        assert.equal(info.start, 6);
        assert.equal(info.pos, 9);
    });

    it('handles empty input', () => {
        const info = currentWordInfo('', 0);
        assert.equal(info.word, '');
        assert.equal(info.start, 0);
    });

    it('handles cursor mid-text', () => {
        const info = currentWordInfo('hello world', 5);
        assert.equal(info.word, 'hello');
    });
});

describe('predictive text - history', () => {
    it('stores and retrieves history', () => {
        addHistory('foo');
        assert.ok(getHistory().includes('foo'));
    });

    it('dedups history', () => {
        addHistory('foo');
        addHistory('foo');
        assert.equal(getHistory().filter((w) => w === 'foo').length, 1);
    });
});