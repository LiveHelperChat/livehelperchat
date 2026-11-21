import assert from 'node:assert/strict';
import test from 'node:test';
import {preserveMessageScroll} from '../src/util/messageScroll.js';

// Model a message viewport without native scroll anchoring. As in Firefox,
// becoming taller than its contents clamps the scroll position to zero.
function conversation() {
    let height = 414;
    let rowHeight = 136;
    let scrollTop = 0;
    const makeRow = index => ({
        getBoundingClientRect() {
            const top = index * rowHeight - scrollTop;
            return {top, bottom: top + rowHeight, height: rowHeight};
        }
    });
    const rows = Array.from({length: 5}, (_, index) => makeRow(index));
    const list = {
        get clientHeight() { return Math.min(height, this.scrollHeight); },
        get scrollHeight() { return rows.length * rowHeight; },
        get lastElementChild() { return rows.at(-1); },
        get scrollTop() { return scrollTop; },
        set scrollTop(value) {
            scrollTop = Math.max(0, Math.min(value, this.scrollHeight - this.clientHeight));
        },
        querySelectorAll: () => rows,
        contains: row => rows.includes(row),
        getBoundingClientRect: () => ({top: 0})
    };
    const doc = {getElementById: id => id === 'messages-scroll' ? list : null};
    function resize(newHeight, newRowHeight = rowHeight) {
        preserveMessageScroll(doc, () => {
            height = newHeight;
            rowHeight = newRowHeight;
            list.scrollTop = scrollTop;
        });
    }
    return {list, resize, append: () => rows.push(makeRow(rows.length))};
}

test('latest message stays visible after the entire conversation fits', () => {
    const {list, resize} = conversation();
    list.scrollTop = list.scrollHeight;
    resize(754);
    assert.equal(list.scrollTop, 0);
    resize(414);
    assert.equal(list.scrollTop, 266);
});

test('reading position survives a round trip through a non-scrolling layout', () => {
    const {list, resize} = conversation();
    list.scrollTop = 149;
    resize(754);
    assert.equal(list.scrollTop, 0);
    resize(414);
    assert.equal(list.scrollTop, 149);
});

test('reading position survives a larger layout that can only partially restore it', () => {
    const {list, resize} = conversation();
    list.scrollTop = 149;
    resize(560); // 120px of scrolling remains after the toggle.
    assert.equal(list.scrollTop, 120);
    resize(414);
    assert.equal(list.scrollTop, 149);

    resize(560);
    list.scrollTop = 32;
    resize(414);
    assert.equal(list.scrollTop, 32);
});

test('width reflow keeps the visible message and respects scrolling in the larger layout', () => {
    const {list, resize} = conversation();
    list.scrollTop = 149; // 13px into message two
    resize(420, 120);
    assert.equal(list.scrollTop, 133);
    list.scrollTop = 8; // The visitor reads a different message in the larger view.
    resize(414, 136);
    assert.equal(list.scrollTop, 8);
});

test('a changed conversation uses its current position instead of a clamped anchor', () => {
    const {list, resize, append} = conversation();
    list.scrollTop = 149;
    resize(560);
    assert.equal(list.scrollTop, 120);
    append();
    resize(414);
    assert.equal(list.scrollTop, 120);
});

test('a view without messages can still resize', () => {
    let resized = false;
    preserveMessageScroll({getElementById: () => null}, () => { resized = true; });
    assert.equal(resized, true);
});
