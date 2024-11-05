import { writable } from "svelte/store";
export function LHCEditorStore(data) {
    const { subscribe, set, update, get } = writable(data);

    return {
        subscribe,
        update,
        get,
        set,
        addOne: () => update((n) => n + 1),
        reset: () => set(0),
        goPrev : () => update((store) => {
            if (store.index > 0) {
                store.index--;
            }
            return data;
        }),
        goNext : () => update((store) => {
            if (store.index < store.history.length - 1) {
                store.index++;
            }
            return data;
        }),
        setCurrent : (item) => update((store) => {
            store.current = item;
            return store;
        }),
        addHistory : (item) => update((store) => {
            if (item) {

                // If same item being added ignore it.
                // Happens on first letter of text editor
                if (store.current == item) return store;

                // If we are in the middle of the history remove steps from index location
                if (store.index != store.history.length - 1) {
                    store.history.splice(store.index + 1);
                }

                // Store only last 20 records of history
                // If more remove the oldest history item
                if (store.history.length >= 20) {
                    store.history.shift();
                }

                store.history.push(item);
                store.index = store.history.length - 1;
                store.current = item;
            }
            return store;
        })
    };
}