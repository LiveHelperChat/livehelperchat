// A taller viewport can clamp the desired reading position, even when the
// conversation still overflows. Remember it until the next layout toggle.
const clampedPositions = new WeakMap();

function readPosition(list) {
    const saved = clampedPositions.get(list);
    if (saved && Math.abs(saved.top - list.scrollTop) <= 1 &&
        saved.height === list.scrollHeight && saved.lastChild === list.lastElementChild &&
        (!saved.position.row || list.contains(saved.position.row))) {
        return saved.position;
    }
    if (list.scrollHeight - list.clientHeight - list.scrollTop <= 1) {
        return null; // Follow the bottom.
    }
    const top = list.getBoundingClientRect().top;
    const row = Array.from(list.querySelectorAll('.message-row')).find(row => {
        const rect = row.getBoundingClientRect();
        return rect.height > 0 && rect.bottom > top;
    });
    return {row, offset: row && row.getBoundingClientRect().top - top, scrollTop: list.scrollTop};
}

export function preserveMessageScroll(doc, resize) {
    const list = doc && doc.getElementById('messages-scroll');
    if (!list || !list.clientHeight) {
        resize();
        return;
    }

    const position = readPosition(list);
    resize();

    clampedPositions.delete(list);
    if (!position) {
        list.scrollTop = list.scrollHeight;
        return;
    }

    const target = position.row && list.contains(position.row)
        ? list.scrollTop + position.row.getBoundingClientRect().top - list.getBoundingClientRect().top - position.offset
        : position.scrollTop;
    list.scrollTop = target;
    if (Math.abs(list.scrollTop - target) > 1) {
        clampedPositions.set(list, {
            position, top: list.scrollTop, height: list.scrollHeight, lastChild: list.lastElementChild
        });
    }
}
