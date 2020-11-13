
class _helperFunctions {

    constructor() {

        var currentScript = document.currentScript || (function() {
            var scripts = document.getElementsByTagName('script');
            return scripts[scripts.length - 1];
        })();

        var EventEmitter = require('wolfy87-eventemitter');

        this.prefix = currentScript.getAttribute('scope') || 'lhc';
        this.prefixUppercase = this.prefix.toUpperCase();
        this.eventEmitter = new EventEmitter();

        try {
            this.hasSessionStorage = !!window.sessionStorage;
        } catch (e) {
            this.hasSessionStorage = false;
        }

    }

    emitEvent(event, data, internal) {
        this.eventEmitter.emitEvent(event,data);
    }

    sendMessageParent(key, data) {
        if (window.opener && window.opener.closed === false) {
            window.opener.postMessage(this.prefix + '::'+key+'::'+JSON.stringify(data || null),'*');
        } else if (window.parent && window.parent.closed === false) {
            window.parent.postMessage(this.prefix + '::'+key+'::'+JSON.stringify(data || null),'/');
        }
    }

    sendMessageParentDirect(key, data) {
        var eventEmiter = null;

        if (window.parent && window.parent['$_'+this.prefixUppercase] && window.parent.closed === false) {
            eventEmiter = window.parent['$_'+this.prefixUppercase].eventListener;
        } else if (window.opener && window.opener['$_'+this.prefixUppercase] && window.opener.closed === false) {
            eventEmiter = window.opener['$_'+this.prefixUppercase].eventListener;
        }

        if (eventEmiter !== null) {
            eventEmiter.emitEvent(key,data);
        } else {
            this.sendMessageParent(key, data);
        }
    }

    setSessionStorage(key, value) {
        if (this.hasSessionStorage && sessionStorage.setItem) try {
            sessionStorage.setItem(this.prefix + key, value)
        } catch (d) {
        }
    }

    getSessionStorage(a) {
        return this.hasSessionStorage && sessionStorage.getItem ? sessionStorage.getItem(this.prefix + a) : null
    }

    removeSessionStorage(a) {
        this.hasSessionStorage && sessionStorage.removeItem && sessionStorage.removeItem(this.prefix + a);
    }

    getTimeZone() {
        try {
            var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            if (tz == 'undefined') { tz = 'UTC'; }
            return tz;
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