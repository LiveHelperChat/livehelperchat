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
        if (typeof this.attributes != 'undefined') {
            if (typeof this.attributes.LHCChatOptions.attr != 'undefined') {
                if (this.attributes.LHCChatOptions.attr.length > 0) {
                    for (var index in this.attributes.LHCChatOptions.attr) {
                        if (typeof this.attributes.LHCChatOptions.attr[index] != 'undefined' && typeof this.attributes.LHCChatOptions.attr[index].type != 'undefined') {
                            argumentsQuery.push('name[]=' + encodeURIComponent(this.attributes.LHCChatOptions.attr[index].name) + '&encattr[]=' + (typeof this.attributes.LHCChatOptions.attr[index].encrypted != 'undefined' && this.attributes.LHCChatOptions.attr[index].encrypted == true ? 't' : 'f') + '&value[]=' + encodeURIComponent(this.attributes.LHCChatOptions.attr[index].value) + '&type[]=' + encodeURIComponent(this.attributes.LHCChatOptions.attr[index].type) + '&size[]=' + encodeURIComponent(this.attributes.LHCChatOptions.attr[index].size) + '&req[]=' + (typeof this.attributes.LHCChatOptions.attr[index].req != 'undefined' && this.attributes.LHCChatOptions.attr[index].req == true ? 't' : 'f') + '&sh[]=' + ((typeof this.attributes.LHCChatOptions.attr[index].show != 'undefined' && (this.attributes.LHCChatOptions.attr[index].show == 'on' || this.attributes.LHCChatOptions.attr[index].show == 'off')) ? this.attributes.LHCChatOptions.attr[index].show : 'b'));
                        }
                    }
                }
            }

            if (typeof this.attributes.LHCChatOptions.attr_prefill != 'undefined') {
                if (this.attributes.LHCChatOptions.attr_prefill.length > 0) {
                    for (var index in this.attributes.LHCChatOptions.attr_prefill) {
                        if (typeof this.attributes.LHCChatOptions.attr_prefill[index] != 'undefined' && typeof this.attributes.LHCChatOptions.attr_prefill[index].name != 'undefined') {
                            argumentsQuery.push('prefill[' + this.attributes.LHCChatOptions.attr_prefill[index].name + ']=' + encodeURIComponent(this.attributes.LHCChatOptions.attr_prefill[index].value));
                        }
                    }
                }
            }

            if (typeof this.attributes.LHCChatOptions.attr_prefill_admin != 'undefined') {
                if (this.attributes.LHCChatOptions.attr_prefill_admin.length > 0) {
                    for (var index in this.attributes.LHCChatOptions.attr_prefill_admin) {
                        if (typeof this.attributes.LHCChatOptions.attr_prefill_admin[index] != 'undefined') {
                            argumentsQuery.push('value_items_admin[' + this.attributes.LHCChatOptions.attr_prefill_admin[index].index + ']=' + encodeURIComponent(this.attributes.LHCChatOptions.attr_prefill_admin[index].value));
                        }
                    }
                }
            }

            if (argumentsQuery.length > 0) {
                paramsReturn = '&' + argumentsQuery.join('&');
            }
        }

        return paramsReturn;
    }

    init(attributes, chatEvents, paramsPopup) {

        if (this.cont.elementReferrerPopup && this.cont.elementReferrerPopup.closed === false) {
            typeof paramsPopup !== 'undefined' && paramsPopup.event !== 'undefined' && paramsPopup.event.preventDefault();
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

            if (attr['static_chat']['vid'] !== null && this.attributes.storageHandler.cookieEnabled === true) {
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

            if (this.attributes['operator']) {
                urlArgumetns = urlArgumetns + "/(operator)/" + this.attributes['operator'];
            }

            if (this.attributes['survey']) {
                urlArgumetns = urlArgumetns + "/(survey)/" + this.attributes['survey'];
            }

            if (this.attributes['bot_id']) {
                urlArgumetns = urlArgumetns + "/(bot)/" + this.attributes['bot_id'];
            }

            if (this.attributes['trigger_id']) {
                urlArgumetns = urlArgumetns + "/(trigger)/" + this.attributes['trigger_id'];
            }

            if (this.attributes['priority']) {
                urlArgumetns = urlArgumetns + "/(priority)/" + this.attributes['priority'];
            }

            if (this.attributes['prefixLowercase'] != 'lhc') {
                urlArgumetns = urlArgumetns + "/(scope)/" + this.attributes['prefixLowercase'];
            }

            urlArgumetns = urlArgumetns + "/(sound)/" + (this.attributes.toggleSound.value == true ? 1 : 0);

            if (this.attributes['proactive']['invitation']) {
                urlArgumetns = urlArgumetns + "/(inv)/" + this.attributes['proactive']['invitation'];
                if (this.attributes['mode'] == 'popup') {
                    this.attributes.storageHandler.setSessionStorage(this.attributes['prefixStorage']+'_invt', 1);
                }
            }

            var fontSize = this.attributes.storageHandler.getLocalStorage(this.attributes['prefixStorage']+'_dfs');

            if (fontSize) {
                urlArgumetns = urlArgumetns + "/(fs)/" + parseInt(fontSize);
            }

            if (this.attributes['leaveMessage'] === true) {
                urlArgumetns = urlArgumetns + "/(leaveamessage)/true";
            }

            if (this.attributes['userSession'].getSessionReferrer() !== null && this.attributes['userSession'].getSessionReferrer() != '') {
                urlArgumetns = urlArgumetns + '?ses_ref=' + this.attributes['userSession'].getSessionReferrer() + this.parseOptions();
            } else {
                urlArgumetns = urlArgumetns + '?' + this.parseOptions();
            }

            const dualScreenLeft = window.screenLeft !==  undefined ? window.screenLeft : window.screenX;
            const dualScreenTop = window.screenTop !==  undefined   ? window.screenTop  : window.screenY;

            const width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
            const height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

            const systemZoom = width / window.screen.availWidth;
            const left = (width - parseInt(this.attributes['popupDimesnions']['pwidth'])) / 2 / systemZoom + dualScreenLeft;
            const top = (height - parseInt(this.attributes['popupDimesnions']['pheight'])) / 2 / systemZoom + dualScreenTop;

            var paramsWindow = "scrollbars=yes,menubar=1,resizable=1,width=" + this.attributes['popupDimesnions']['pwidth'] + ",height=" + this.attributes['popupDimesnions']['pheight'] + ",top=" + top + ",left=" + left;
            var newWin = window.open("", this.attributes['prefixStorage'] + '_popup_v2', paramsWindow);
            var needWindow = false;
            var windowCreated = false;

            // First try to find any existing window
            try {
                // It has to be new window or popup was blocked
                if (!newWin || newWin.closed || typeof newWin.closed=='undefined' || newWin.location.href === "about:blank") {
                    newWin = this.cont.elementReferrerPopup = window.open(this.attributes['base_url'] + this.attributes['lang'] + "chat/start" + urlArgumetns, this.attributes['prefixStorage']+'_popup_v2', paramsWindow);
                    windowCreated = true;
                } else {
                    needWindow = true;
                }
            } catch (e) { // We get cross-origin error only if window exist and it's location is other one than about:blank
                needWindow = true;
            }

            // Now if visitor has blocked popup change chat status link and just allow browser handle the rest.
            if (!newWin || newWin.closed || typeof newWin.closed=='undefined') {
                try {
                    this.attributes.viewHandler.cont.getElementById("status-icon").href = this.attributes['base_url'] + this.attributes['lang'] + "chat/start" + urlArgumetns;
                } catch (e) {
                    alert('You have disabled popups!');
                }
            } else if (windowCreated == true) {
                typeof chatEvents !== 'undefined' && this.attributes.kcw === false && chatEvents.sendChildEvent('endedChat', [{'sender': 'endButton'}]);
                typeof paramsPopup !== 'undefined' && paramsPopup.event !== 'undefined' && paramsPopup.event.preventDefault();
            } else if (needWindow === true) {
                this.cont.elementReferrerPopup = newWin;
                newWin.focus();
                typeof paramsPopup !== 'undefined' && paramsPopup.event !== 'undefined' && paramsPopup.event.preventDefault();
            }
        }
    }

    sendParameters(chatEvents) {
        if (this.cont.elementReferrerPopup && this.cont.elementReferrerPopup.closed === false) {
            var js_vars = this.attributes['jsVars'].value;
            var js_args = {};
            var currentVar = null;
            for (var index in js_vars) {
                try {
                    currentVar = eval(js_vars[index].var);
                    if (typeof currentVar !== 'undefined' && currentVar !== null && currentVar !== '') {
                        js_args[js_vars[index].id] = currentVar;
                    }
                } catch (err) {

                }
            }
            chatEvents.sendChildEvent('jsVars', [js_args]);
        }
    }
}