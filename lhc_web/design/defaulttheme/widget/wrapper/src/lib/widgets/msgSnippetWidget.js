import {settings} from '../settings.js';
import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class msgSnippetWidget{
    constructor(prefix) {

        this.attributes = {};
        this.hidden = false;
        this.widgetOpen = false;
        this.invitationOpen = false;
        this.nhOpen = false;

        this.msop = null;   // Last operator ID who wrote a message
        this.msg = null;    // Container of the message

        this.cont = new UIConstructorIframe((prefix || 'lhc')+'_msgsnippet_widget_v2', helperFunctions.getAbstractStyle({
            zindex: "2147483639",
            width: "300px",
            height: "200px",
            position: "fixed",
            display: "none",
        }), null, "iframe");

        this.loadStatus = {main : false, theme: false, status: false};
    }

    checkLoadStatus() {
        if (this.loadStatus['theme'] == true && this.loadStatus['main'] == true && this.loadStatus['status'] == true) {
            this.cont.elmDomDoc.body.style.display = "";
            this.fitContent();
        }
    }

    renderBody() {

        this.cont.tmpl = this.msg.replace('{dev_type}',(this.attributes.isMobile === true ? 'lhc-mobile' : 'lhc-desktop')).replace('{msg_body}',this.settings['msg_body']).replace('{operator_profile}',this.settings['operator_profile']);

        this.cont.bodyId = 'msgsnippet';

        if (this.cont.constructUIIframe('', this.attributes.staticJS['dir']) === null){
            return null;
        }

        // Content invisible untill media loads
        this.cont.elmDomDoc.body.style.display = "none";

        this.cont.elmDom.className += this.attributes.isMobile === true ? ' lhc-mobile' : ' lhc-desktop';

        this.cont.attachUserEventListener("click",  (e) => {
            this.attributes.eventEmitter.emitEvent('msgSnippetClicked', [{'event': e, 'sender' : 'closeButton'}]);
            this.attributes.eventEmitter.emitEvent('showWidget', [{'event': e}]);
        }, "start-chat-btn",'msgsnippetstart');

        var _that = this;

        this.cont.attachUserEventListener("click",  (a) => {
            this.attributes.eventEmitter.emitEvent('msgsnippetClosed', [{'sender' : 'closeButton'}]);
            a.stopPropagation();
            _that.hide(true);
        }, "close-need-help-btn",'msgsnippetclose');

        this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['main'] = true; this.checkLoadStatus()},crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']}, true);

        if (this.attributes.isMobile == true) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_mobile_css']});
        }

        if (this.attributes.theme > 0) {
            this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['theme'] = true; this.checkLoadStatus()}, id : "lhc-theme-msgsnippet", crossOrigin : "anonymous",  href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/theme/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
            this.checkLoadStatus();
        }

        // Show need help only if status widget is loaded
        this.attributes.sload.subscribe((data) => {if(data){this.loadStatus['status'] = true; this.checkLoadStatus()}});

        this.attributes.eventEmitter.emitEvent('showMsgSnippet', [{'sender' : 'closeButton'}]);

        this.attributes.eventEmitter.addListener('unread_message', (dataUnread) => {
            if (dataUnread.msop) {
                if (dataUnread.msop == this.msop && dataUnread.msg_body) {
                    this.showSnippet(dataUnread, true);
                } else {
                    this.msop = dataUnread.msop;
                    helperFunctions.makeRequest(this.attributes.LHC_API.args.lhc_base_url + this.attributes['lang'] + 'widgetrestapi/getmessagesnippet', {params: this.attributes['userSession'].getSessionAttributes()}, (data) => {
                        this.msop = data.msop;
                        this.msg = data.msg;
                        this.showSnippet(data, true);
                    })
                }
            }
        });

        this.attributes.eventEmitter.addListener('hide_msg_snippet', () => {
            this.hide(true);
        });

        this.attributes.widgetStatus.subscribe((data) => {
            data == true ? (this.widgetOpen = true, this.hide(true)) : (this.widgetOpen = false, this.show());
        });

        this.attributes.eventEmitter.addListener('reloadWidget',() => {
            this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['theme'] = true; this.checkLoadStatus()}, id : "lhc-theme-msgsnippet", crossOrigin : "anonymous",  href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/theme/' + this.attributes.theme + '?v=' + Date.now()}, true);
        });
    }

    init(attributes, settings) {
        this.attributes = attributes;
        this.settings = settings;

        if (this.settings['msg']) {
            this.msg = this.settings['msg'];
        }

        if (this.settings['msop']) {
            this.msop = this.settings['msop'];
        }

        if (!this.msg || (this.settings['msop'] && this.msop !== this.settings['msop'])) {
            helperFunctions.makeRequest(this.attributes.LHC_API.args.lhc_base_url + this.attributes['lang'] + 'widgetrestapi/getmessagesnippet', {params: this.attributes['userSession'].getSessionAttributes()}, (data) => {
                this.msop = data.msop;
                this.msg = data.msg;
                this.settings['operator_profile'] = data.operator_profile;
                this.renderBody();
            })
        } else {
            this.renderBody();
        }

    }

    hide (persistent) {

        if (typeof persistent !== 'undefined' && persistent === true) {
            this.hidden = true;
        }

        this.cont.hide();

        if (this.nhOpen == true) {
            this.attributes.eventEmitter.emitEvent('msgSnippetHide', []);
        }

        this.attributes.msgsnippet_status.next(false);
        this.nhOpen = false;
    }

    showSnippet(data, showSnippet) {
        if (showSnippet == true) {
            this.hidden = false;
            this.show();
        }
        this.cont.elmDomDoc.getElementById('messages-scroll').innerHTML = data.msg_body;

        if (data.operator_profile) {
            this.cont.elmDomDoc.getElementById('operator-profile-snippet').innerHTML = data.operator_profile;
        }

        this.fitContent();
    }

    fitContent() {

        var documentHeight = this.cont.elmDomDoc.getElementById('messages-scroll').offsetHeight;

        var placement = {bottom: (70 + this.attributes.widgetDimesions.value.wbottom - (91 - documentHeight)) +"px", right: (65+this.attributes.widgetDimesions.value.wright) + "px"};

        var leftPosition = false;

        if (this.attributes.position_placement == 'bottom_left' || this.attributes.position_placement == 'full_height_left') {
            placement = {bottom: (70 + this.attributes.widgetDimesions.value.wbottom - (91 - documentHeight)) +"px", left: (65+this.attributes.widgetDimesions.value.wright) + "px"};
            leftPosition = true;
        } else if (this.attributes.position_placement == 'middle_left') {
            placement = {bottom: "calc(50% + 35px)", left: (65+this.attributes.widgetDimesions.value.wright) + "px"};
            leftPosition = true;
        } else if (this.attributes.position_placement == 'middle_right') {
            placement = {bottom: "calc(50% + 35px)", right: (65+this.attributes.widgetDimesions.value.wright) + "px"};
        }

        this.cont.massRestyle(placement);
    }

    show () {
        if (this.hidden == true || this.widgetOpen == true || this.invitationOpen == true || this.attributes.onlineStatus.value == false) {
            return;
        }

        if (this.attributes.hideOffline === false) {
            this.cont.show();
            if (this.nhOpen == false) {
                this.attributes.eventEmitter.emitEvent('msgSnippetShow', []);
                this.attributes.msgsnippet_status.next(true);
            }
            this.nhOpen = true;
        } else {
            this.cont.hide();
            if (this.nhOpen == true) {
                this.attributes.eventEmitter.emitEvent('msgSnippetHide', []);
                this.attributes.msgsnippet_status.next(false);
            }
            this.nhOpen = false;
        }
    }
}