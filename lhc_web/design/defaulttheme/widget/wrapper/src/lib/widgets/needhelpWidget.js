import {settings} from '../settings.js';
import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class needhelpWidget{
    constructor() {

        this.attributes = {};
        this.hidden = false;
        this.widgetOpen = false;
        this.invitationOpen = false;
        this.nhOpen = false;

        this.cont = new UIConstructorIframe('lhc_needhelp_widget_v2', helperFunctions.getAbstractStyle({
            zindex: "2147483640",
            width: "320px",
            height: "135px",
            position: "fixed",
            display: "none",
            bottom: "70px",
            right: "45px",
        }), null, "iframe");

        this.loadStatus = {main : false, theme: false, status: false};
    }
    
    checkLoadStatus() {
        if (this.loadStatus['theme'] == true && this.loadStatus['main'] == true && this.loadStatus['status'] == true) {
            this.cont.elmDomDoc.body.style.display = "";
        }
    }
    
    init(attributes, settings) {

        this.attributes = attributes;
        
        if (this.attributes.widgetDimesions.value.wbottom > 0 || this.attributes.widgetDimesions.value.wright > 0) {
            this.cont.massRestyle({
                "bottom" : (70+this.attributes.widgetDimesions.value.wbottom) + "px",
                "right" : (45+this.attributes.widgetDimesions.value.wright) + "px",
            });
        }
        
        this.cont.tmpl = settings['html'].replace('{dev_type}',(this.attributes.isMobile === true ? 'lhc-mobile' : 'lhc-desktop'));
        this.cont.bodyId = 'need-help';
        this.cont.constructUIIframe('');
        
        // Content invisible untill media loads
        this.cont.elmDomDoc.body.style.display = "none";
        
        this.cont.elmDom.className += this.attributes.isMobile === true ? ' lhc-mobile' : ' lhc-desktop';

        this.cont.attachUserEventListener("click", function (a) {
            attributes.eventEmitter.emitEvent('nhClicked', [{'sender' : 'closeButton'}]);
            attributes.eventEmitter.emitEvent('showWidget', [{'sender' : 'closeButton'}]);
        }, "start-chat-btn",'nhstrt');

        var _that = this;

        this.cont.attachUserEventListener("click", function (a) {
            attributes.eventEmitter.emitEvent('nhClosed', [{'sender' : 'closeButton'}]);
            a.stopPropagation();
            _that.hide(true);
        }, "close-need-help-btn",'nhcls');

        if (settings.dimensions) {
            this.cont.massRestyle(settings.dimensions);
        }

        this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['main'] = true; this.checkLoadStatus()},crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']}, true);

        if (this.attributes.isMobile == true) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_mobile_css']});
        }

        if (this.attributes.theme > 0) {
            this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['theme'] = true; this.checkLoadStatus()}, crossOrigin : "anonymous",  href : LHC_API.args.lhc_base_url + '/widgetrestapi/themeneedhelp/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
            this.checkLoadStatus();
        }

        // Show need help only if status widget is loaded
        attributes.wloaded.subscribe((data) => {if(data){this.loadStatus['status'] = true; this.checkLoadStatus()}});

        attributes.eventEmitter.addListener('showInvitation',() => {
            this.invitationOpen = true;
            this.hide();
        });
        
        attributes.eventEmitter.addListener('chatStarted',() => {
            this.hide(true);
        });

        attributes.eventEmitter.addListener('hideInvitation',() => {
            this.invitationOpen = false;
            this.show();
        });

        attributes.eventEmitter.addListener('cancelInvitation',() => {
            this.invitationOpen = false;
            this.show();
        });

        setTimeout(() => {
            attributes.widgetStatus.subscribe((data) => {
                data == true ? (this.widgetOpen = true,this.hide()) : (this.widgetOpen = false,this.show());
            });

            attributes.onlineStatus.subscribe((data) => {
                if (data == false) {
                    let needHide = this.hidden;
                    this.hide();
                    // Show next time only if it was not hidden already
                    if (needHide === false) {
                        this.hidden = false;
                    }
                } else {
                    this.show();
                }
            });

        }, settings.delay);

    }

    hide (persistent) {

        if (typeof persistent !== 'undefined' && persistent === true){
            this.attributes.userSession.hnh = Math.round(Date.now() / 1000);
            this.attributes.storageHandler.storeSessionInformation(this.attributes.userSession.getSessionAttributes());
            this.hidden = true;
        }

        this.cont.hide();

        if (this.nhOpen == true) {
            this.attributes.eventEmitter.emitEvent('nhHide', []);
        }

        this.nhOpen = false;
    }

    show () {

        if (this.hidden == true || this.widgetOpen == true ||  this.invitationOpen == true || this.attributes.onlineStatus.value == false) {
            return;
        }

        if (this.attributes.hideOffline === false) {
            this.cont.show();
            if (this.nhOpen == false) {
                this.attributes.eventEmitter.emitEvent('nhShow', []);
            }
            this.nhOpen = true;
        } else {
            this.cont.hide();
            if (this.nhOpen == true) {
                this.attributes.eventEmitter.emitEvent('nhHide', []);
            }
            this.nhOpen = false;
        }
    }
}