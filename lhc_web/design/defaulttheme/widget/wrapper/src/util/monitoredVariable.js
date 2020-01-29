
export class monitoredVariable {
    constructor(value, params) {
        this.valueInternal = value;
        this.listeners = [];
        this.params = params || {};
    }

    get value() {
        return this.valueInternal;
    }

    set value(val) {
        this.next(val);
    }

    next(val) {
        this.valueInternal = val;
        this.callListeners();
    }

    nextProperty(key, val) {
        this.valueInternal[key] = val;
        this.callListeners();
    }

    callListeners() {
        this.listeners.forEach((item) => {
            if (item && typeof item === "function") {
                item(this.valueInternal);
            }
        });
    }

    unsubscribe(callback) {
        if (this.listeners.indexOf(callback) !== -1) {
            this.listeners.splice(this.listeners.indexOf(callback), 1);
        }
    }

    subscribe(callback) {
        this.listeners.push(callback);
        
        if (!this.params['ignore_sub']) {
            callback(this.valueInternal);
        }
    }
}

