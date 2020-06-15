import {settings} from '../settings.js';
import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class needhelpWidget{
    constructor() {

        this.attributes = {};
        this.hidden = false;

        this.cont = new UIConstructorIframe('lhc_needhelp_widget_v2', helperFunctions.getAbstractStyle({
            zindex: "1000000",
            width: "320px",
            height: "135px",
            position: "fixed",
            display: "none",
            bottom: "70px",
            right: "45px",
        }), null, "iframe");

        this.loadStatus = {main : false, theme: false};
    }

    init(attributes, settings) {

        this.attributes = attributes;
        this.cont.tmpl = settings['html'];
        this.cont.constructUIIframe('');

        this.cont.attachUserEventListener("click", function (a) {
            attributes.eventEmitter.emitEvent('nhClicked', [{'sender' : 'closeButton'}]);
            attributes.eventEmitter.emitEvent('showWidget', [{'sender' : 'closeButton'}]);
        }, "start-chat-btn",'nhstrt');

        var _that = this;

        this.cont.attachUserEventListener("click", function (a) {
            attributes.eventEmitter.emitEvent('nhClosed', [{'sender' : 'closeButton'}]);
            a.stopPropagation();
            _that.hide();
        }, "close-need-help-btn",'nhcls');

        if (settings.dimensions) {
            this.cont.massRestyle(settings.dimensions);
        }

        this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']}, true);

        if (this.attributes.theme > 0) {
            this.loadStatus['theme'] = false;
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : LHC_API.args.lhc_base_url + '/widgetrestapi/themeneedhelp/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
        }

        attributes.eventEmitter.addListener('showInvitation',() => {
            this.hide();
        });

        setTimeout(() => {
            attributes.widgetStatus.subscribe((data) => {
                data == true ? this.hide() : this.show();
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

    hide () {

        this.attributes.userSession.hnh = Math.round(Date.now() / 1000);
        this.attributes.storageHandler.storeSessionInformation(this.attributes.userSession.getSessionAttributes())

        this.hidden = true;
        this.cont.hide();
    }

    show () {

        if (this.hidden == true) {
            return;
        }

        if (this.attributes.hideOffline === false) {
            this.cont.show();
        } else {
            this.cont.hide();
        }
    }
}