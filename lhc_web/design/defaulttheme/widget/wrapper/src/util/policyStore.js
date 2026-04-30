const _store = new WeakMap();

export const policyStore = {
    set(attributes, policy) { _store.set(attributes, policy); },
    get(attributes) { return _store.get(attributes) || null; }
};
