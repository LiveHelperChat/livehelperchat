
class _helperFunctions {

    constructor() {
        var EventEmitter = require('wolfy87-eventemitter');
        this.eventEmitter = new EventEmitter();
        this.hasSessionStorage = !!window.sessionStorage;
    }

    emitEvent(event, data, internal) {
        this.eventEmitter.emitEvent(event,data);
    }

    sendMessageParent(key, data) {
        if (window.opener && window.opener.closed === false) {
            window.opener.postMessage('lhc::'+key+'::'+JSON.stringify(data || null),'/');
        } else if (window.parent && window.parent.closed === false) {
            window.parent.postMessage('lhc::'+key+'::'+JSON.stringify(data || null),'/');
        }
    }

    sendMessageParentDirect(key, data) {
        var eventEmiter = null;

        if (window.parent && window.parent.$_LHC && window.parent.closed === false) {
            eventEmiter = window.parent.$_LHC.eventListener;
        } else if (window.opener && window.opener.$_LHC && window.opener.closed === false) {
            eventEmiter = window.opener.$_LHC.eventListener;
        }

        if (eventEmiter !== null) {
            eventEmiter.emitEvent(key,data);
        } else {
            this.sendMessageParent(key, data);
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

    getTimeZone() {
        try {
            return Intl.DateTimeFormat().resolvedOptions().timeZone;
        } catch (e) {
            var today = new Date();

            let stdTimezoneOffset = function() {
                var jan = new Date(today.getFullYear(), 0, 1);
                var jul = new Date(today.getFullYear(), 6, 1);
                return Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
            };

            var dst = function() {
                return today.getTimezoneOffset() < stdTimezoneOffset();
            };

            var timeZoneOffset = 0;

            if (dst()) {
                timeZoneOffset = today.getTimezoneOffset();
            } else {
                timeZoneOffset = today.getTimezoneOffset()-60;
            };

            return (((timeZoneOffset)/60) * -1);
        }
    }

    getCustomFieldsSubmit(customFields)
    {
        if (customFields.size > 0 ) {
            let customItems = {'name_items' : [],'values_req' : [], 'value_items' : [], 'value_types' : [], 'encattr' : [], 'value_show' : []};
            customFields.forEach(field => {
                customItems['value_items'].push(field.get('value'));
                customItems['name_items'].push(field.get('name'));
                customItems['values_req'].push(field.get('required') === true ? 't' : 'f');
                customItems['encattr'].push(field.get('encrypted') === true ? 't' : '');
                customItems['value_types'].push(field.get('type'));
                customItems['value_show'].push(field.get('show'));
            })
            return customItems;
        }
        return null;
    }

    prefillFields(inst) {
        const prefillOptions = inst.props.chatwidget.get('attr_prefill');
        if (prefillOptions.length > 0) {
            prefillOptions.forEach((item) => {
                inst.setState(item);
            });
        }
    }

};

const helperFunctions = new _helperFunctions();
window.lhcHelperfunctions = helperFunctions;
export { helperFunctions };