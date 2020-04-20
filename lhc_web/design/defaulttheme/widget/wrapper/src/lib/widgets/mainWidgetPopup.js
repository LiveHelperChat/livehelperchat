export class mainWidgetPopup {
    constructor() {

        this.attributes = {};

        this.width = null;
        this.height = null;
        this.units = 'px';
        this.freeup();
    }

    freeup() {
        this.cont = {};
    }

    parseOptions() {
        var argumentsQuery = new Array();
        var paramsReturn = '';
        if (typeof LHCChatOptions != 'undefined') {
            if (typeof LHCChatOptions.attr != 'undefined') {
                if (LHCChatOptions.attr.length > 0) {
                    for (var index in LHCChatOptions.attr) {
                        if (typeof LHCChatOptions.attr[index] != 'undefined' && typeof LHCChatOptions.attr[index].type != 'undefined') {
                            argumentsQuery.push('name[]=' + encodeURIComponent(LHCChatOptions.attr[index].name) + '&encattr[]=' + (typeof LHCChatOptions.attr[index].encrypted != 'undefined' && LHCChatOptions.attr[index].encrypted == true ? 't' : 'f') + '&value[]=' + encodeURIComponent(LHCChatOptions.attr[index].value) + '&type[]=' + encodeURIComponent(LHCChatOptions.attr[index].type) + '&size[]=' + encodeURIComponent(LHCChatOptions.attr[index].size) + '&req[]=' + (typeof LHCChatOptions.attr[index].req != 'undefined' && LHCChatOptions.attr[index].req == true ? 't' : 'f') + '&sh[]=' + ((typeof LHCChatOptions.attr[index].show != 'undefined' && (LHCChatOptions.attr[index].show == 'on' || LHCChatOptions.attr[index].show == 'off')) ? LHCChatOptions.attr[index].show : 'b'));
                        }
                    }
                }
            }

            if (typeof LHCChatOptions.attr_prefill != 'undefined') {
                if (LHCChatOptions.attr_prefill.length > 0) {
                    for (var index in LHCChatOptions.attr_prefill) {
                        if (typeof LHCChatOptions.attr_prefill[index] != 'undefined' && typeof LHCChatOptions.attr_prefill[index].name != 'undefined') {
                            argumentsQuery.push('prefill[' + LHCChatOptions.attr_prefill[index].name + ']=' + encodeURIComponent(LHCChatOptions.attr_prefill[index].value));
                        }
                    }
                }
            }


            if (typeof LHCChatOptions.attr_prefill_admin != 'undefined') {
                if (LHCChatOptions.attr_prefill_admin.length > 0) {
                    for (var index in LHCChatOptions.attr_prefill_admin) {
                        if (typeof LHCChatOptions.attr_prefill_admin[index] != 'undefined') {
                            argumentsQuery.push('value_items_admin[' + LHCChatOptions.attr_prefill_admin[index].index + ']=' + encodeURIComponent(LHCChatOptions.attr_prefill_admin[index].value));
                        }
                    }
                }
            }

            if (argumentsQuery.length > 0) {
                paramsReturn = '&' + argumentsQuery.join('&');
            }
        }


        var js_vars = this.attributes['jsVars'].value;

        var js_args = [];
        var currentVar = null;
        for (var index in js_vars) {
            try {
                currentVar = eval(js_vars[index].var);
                if (typeof currentVar !== 'undefined' && currentVar !== null && currentVar !== '') {
                    js_args.push('jsvar[' + js_vars[index].id + ']=' + encodeURIComponent(currentVar));
                }
            } catch (err) {

            }
        }

        if (js_args.length > 0) {
            paramsReturn = paramsReturn + '&' + js_args.join('&');
        }

        return paramsReturn;
    }

    init(attributes) {

        if (this.cont.elementReferrerPopup && this.cont.elementReferrerPopup.closed === false) {
            this.cont.elementReferrerPopup.focus();
        } else {

            this.attributes = attributes;

            let attr = {
                'static_chat': this.attributes['userSession'].getSessionAttributes()
            };

            let urlArgumetns = '';

            if (attr['static_chat']['id'] && attr['static_chat']['hash']) {
                urlArgumetns = urlArgumetns + "/(id)/" + attr['static_chat']['id'] + "/(hash)/" + attr['static_chat']['hash'];
            }

            if (this.attributes['theme'] !== null) {
                urlArgumetns = urlArgumetns + "/(theme)/" + this.attributes['theme'];
            }

            if (attr['static_chat']['vid'] !== null) {
                urlArgumetns = urlArgumetns + "/(vid)/" + attr['static_chat']['vid'];
            }

            if (this.attributes['isMobile']) {
                urlArgumetns = urlArgumetns + "/(mobile)/true";
            }

            if (this.attributes['department'].length > 0) {
                urlArgumetns = urlArgumetns + "/(department)/" + this.attributes['department'].join('/');
            }

            if (this.attributes['identifier'] != '') {
                urlArgumetns = urlArgumetns + "/(identifier)/" + this.attributes['identifier'];
            }

            if (this.attributes['operator'] !== null) {
                urlArgumetns = urlArgumetns + "/(operator)/" + this.attributes['operator'];
            }

            if (this.attributes['survey'] !== null) {
                urlArgumetns = urlArgumetns + "/(survey)/" + this.attributes['survey'];
            }

            if (this.attributes['priority'] !== null) {
                urlArgumetns = urlArgumetns + "/(priority)/" + this.attributes['priority'];
            }

            if (this.attributes['proactive']['invitation']) {
                urlArgumetns = urlArgumetns + "/(inv)/" + this.attributes['proactive']['invitation'];
                if (this.attributes['mode'] == 'popup') {
                    this.attributes.storageHandler.setSessionStorage('LHC_invt', 1);
                }
            }

            if (this.attributes['userSession'].getSessionReferrer() !== null && this.attributes['userSession'].getSessionReferrer() != '') {
                urlArgumetns = urlArgumetns + '?ses_ref=' + this.attributes['userSession'].getSessionReferrer() + this.parseOptions();
            } else {
                urlArgumetns = urlArgumetns + '?' + this.parseOptions();
            }

            this.cont.elementReferrerPopup = window.open(this.attributes['base_url'] + this.attributes['lang'] + "chat/start" + urlArgumetns, 'lhc_popup_v2', "scrollbars=yes,menubar=1,resizable=1,width=" + this.attributes['popupDimesnions']['pwidth'] + ",height=" + this.attributes['popupDimesnions']['pheight']);

        }


    }
}