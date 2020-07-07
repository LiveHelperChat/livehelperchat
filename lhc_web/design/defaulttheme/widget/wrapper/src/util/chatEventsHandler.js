
class chatEventsHandler {

    constructor(attr) {
        this.attributes = attr
    }

    getJSVarsValues(jsVars) {
        var js_args = {};

        if (jsVars.length > 0) {

            var currentVar = null;

            for (var index in jsVars) {
                try {
                    currentVar = eval('window.'+jsVars[index].var);
                    if (typeof currentVar !== 'undefined' && currentVar !== null && currentVar !== '') {
                        js_args[jsVars[index].id] = currentVar;
                    }
                } catch(err) {
                }
            }
            return js_args;
        }

        return js_args;
    }

    getInitAttributes() {
        let attr =  {
            'onlineStatus' : this.attributes['onlineStatus'].value,
            'toggleSound' : this.attributes['toggleSound'].value,
            'widgetStatus' : this.attributes['widgetStatus'].value,
            'jsVars' : this.getJSVarsValues(this.attributes['jsVars'].value),
            'isMobile' : this.attributes['isMobile'],
            'department' : this.attributes['department'],
            'product' : this.attributes['product'],
            'theme' : this.attributes['theme'],
            'base_url' : this.attributes['base_url'],
            'mode' : this.attributes['mode'],
            'captcha' : this.attributes['captcha'],
            'staticJS' : this.attributes['staticJS'],
            'static_chat' : this.attributes['userSession'].getSessionAttributes()
        };

        if (this.attributes['fresh'] == true) {
            if (attr['static_chat']['id']) {
                delete attr['static_chat']['id'];
            }

            if (attr['static_chat']['hash']) {
                delete attr['static_chat']['hash'];
            }
        }

        if (this.attributes['bot_id'] != '') {
            attr['bot_id'] = this.attributes['bot_id'];
        }

        if (this.attributes['priority'] != '') {
            attr['priority'] = this.attributes['priority'];
        }
        
        if (this.attributes['phash'] !== null) {
            attr['phash'] = this.attributes['phash'];
        }

        if (this.attributes['survey'] !== null) {
            attr['survey'] = this.attributes['survey'];
        }

        if (this.attributes['pvhash'] !== null) {
            attr['pvhash'] = this.attributes['pvhash'];
        }

        if (this.attributes['operator'] !== null) {
            attr['operator'] = this.attributes['operator'];
        }

        if (this.attributes['lang'] != '') {
            attr['lang'] = this.attributes['lang'];
        }

        if (this.attributes['proactive']['invitation']) {
            attr['proactive'] = this.attributes['proactive'];
        }

        if (window.LHCChatOptions && window.LHCChatOptions.attr) {
            var prefillOptions = window.LHCChatOptions.attr;
            let fieldsCustom = [];
            prefillOptions.forEach((item, index) => {
                fieldsCustom.push({show : (((typeof item.show != 'undefined' && (item.show == 'on' || item.show == 'off')) ? item.show : 'b')), value : item.value, index : index, name : item.name, "class": "form-control form-control-sm", 'type' : item.type, 'identifier': ('additional_' + index), 'placeholder' : '', 'width' : (item.size || 6), 'encrypted': (item.encrypted || false), 'required' : (item.req || false), 'label' : item.name});
            });

            attr['CUSTOM_FIELDS'] = fieldsCustom;
        }

        if (window.LHCChatOptions && window.LHCChatOptions.attr_prefill) {
            var prefillOptions = window.LHCChatOptions.attr_prefill;
            let prefilOptionsList = [];
            prefillOptions.forEach((item) => {
                if (item.name == 'email') {
                    prefilOptionsList.push({'Email' : item.value});
                } else if (item.name == 'username') {
                    prefilOptionsList.push({'Username' : item.value});
                } else if (item.name == 'phone') {
                    prefilOptionsList.push({'Phone' : item.value});
                } else if (item.name == 'question') {
                    prefilOptionsList.push({'Question' : item.value});
                }
            })
            attr['attr_prefill'] = prefilOptionsList;
        }

        if (window.LHCChatOptions && window.LHCChatOptions.attr_prefill_admin) {
            var prefillOptions = window.LHCChatOptions.attr_prefill_admin;
            let prefilOptionsList = [];
            prefillOptions.forEach((item) => {
                  prefilOptionsList.push({'value' : item.value, 'index' : item.index});
            })
            attr['attr_prefill_admin'] = prefilOptionsList;
        }

        if (this.attributes['userSession'].getSessionReferrer() !== null) {
            attr['ses_ref'] = this.attributes['userSession'].getSessionReferrer()
        }

        return attr;
    }

    sendChildCommand(command) {
        if (this.attributes.mainWidget.cont.elmDom && this.attributes.mainWidget.cont.elmDom.contentWindow)
        {
            this.attributes.mainWidget.cont.elmDom.contentWindow.postMessage(command, '/');
        }

        if (this.attributes.popupWidget.cont.elementReferrerPopup && this.attributes.popupWidget.cont.elementReferrerPopup.closed === false)
        {
            this.attributes.popupWidget.cont.elementReferrerPopup.postMessage(command, '/');
        }
    }

    sendReadyEvent (popup) {

        let args = this.getInitAttributes();

        if (!(popup === true) && this.attributes.mainWidget.cont.elmDom && this.attributes.mainWidget.cont.elmDom.contentWindow)
        {
            this.attributes.mainWidget.cont.elmDom.contentWindow.postMessage('lhc_init:' + JSON.stringify(args), '/');
        }
     }

    sendChildEvent(event, args, type) {
        if (typeof type === 'undefined') {
            type = 'lhc_event';
        }
        this.sendChildCommand(type+':'+event + '::' + JSON.stringify(args));
    }
}

export { chatEventsHandler };