
class _helperFunctions {

    constructor() {
        var EventEmitter = require('wolfy87-eventemitter');
        this.eventEmitter = new EventEmitter();
        this.hasSessionStorage = !!window.sessionStorage;
    }

    emitEvent(event, data, internal) {
        this.eventEmitter.emitEvent(event,data);

        /*let eventEmiter = null;
        if (window.parent && window.parent.$_LHC && window.parent.closed === false) {
            eventEmiter = window.parent.$_LHC.eventListener;
        } else if (window.opener && window.opener.$_LHC && window.opener.closed === false) {
            eventEmiter = window.opener.$_LHC.eventListener;
        } else {
            eventEmiter = window.lhcChat.eventEmitter;
        }

        if (eventEmiter) {
            eventEmiter.emitEvent(event, data);

            // Emiter changed because we are in popup mode and parent window was refreshed.
            if (eventEmiter !== window.lhcChat.eventEmitter) {
                window.lhcChat.eventEmitter.emitEvent(event, data);
            }
        }*/
    }

    sendMessageParent(key, data) {
        if (window.opener && window.opener.closed === false) {
            window.opener.postMessage('lhc::'+key+'::'+JSON.stringify(data || null),'*');
        } else if (window.parent && window.parent.closed === false) {
            window.parent.postMessage('lhc::'+key+'::'+JSON.stringify(data || null),'*');
        }
    }

    setSessionStorage(key, value) {
        if (this.hasSessionStorage && sessionStorage.setItem) try {
            sessionStorage.setItem(key, value)
        } catch (d) {
        }
    }

    getSessionStorage(a) {
        return this.hasSessionStorage && sessionStorage.getItem ? sessionStorage.getItem(a) : null
    }

    removeSessionStorage(a) {
        this.hasSessionStorage && sessionStorage.removeItem && sessionStorage.removeItem(a);
    }

};

const helperFunctions = new _helperFunctions();
export { helperFunctions };