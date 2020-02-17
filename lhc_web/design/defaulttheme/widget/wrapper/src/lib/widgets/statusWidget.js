import {settings} from '../settings.js';
import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class statusWidget{
    constructor() {

       this.attributes = {};

       this.cont = new UIConstructorIframe('lhc_status_widget_v2', helperFunctions.getAbstractStyle({
            zindex: "1000000",
            width: "95px",
            height: "95px",
            position: "fixed",
            display: "none",
            bottom: "10px",
            right: "10px",
            maxheight: "95px",
            maxwidth: "95px",
            minheight: "95px",
            minwidth: "95px"
        }), null, "iframe");

        this.cont.tmpl = '<div id="lhc_status_container" style="display: none"><i title="New messages" id="unread-msg-number">!</i><a id="status-icon" class="offline-status" href="#"></a></div>';
        
        this.loadStatus = {main : false, theme: false};
    }

    toggleOfflineIcon(onlineStatus) {
        var icon = this.cont.getElementById("status-icon");

        if (onlineStatus) {
            helperFunctions.removeClass(icon, "offline-status");
        } else {
            helperFunctions.addClass(icon, "offline-status");
        }
    }

    checkLoadStatus(){
        if (this.loadStatus['theme'] == true && this.loadStatus['main'] == true) {
             this.cont.getElementById('lhc_status_container').style.display = "";
        }
    }

    init(attributes) {
        this.cont.constructUIIframe('');

        this.cont.attachUserEventListener("click", function (a) {
            attributes.eventEmitter.emitEvent('showWidget', [{'sender' : 'closeButton'}]);
        }, "lhc_status_container", "minifiedclick");

        this.attributes = attributes;

        if (this.attributes.staticJS['fontCSS']) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['fontCSS']});
        }

        if (this.attributes.staticJS['font_status']) {
            this.cont.insertCssRemoteFile({"as":"font", rel:"preload", type: "font/woff", crossOrigin : "anonymous",  href : this.attributes.staticJS['font_status']});
        }

        if (this.attributes.theme > 0) {
            this.loadStatus['theme'] = false;
            this.cont.insertCssRemoteFile({onload: ()=>{this.loadStatus['theme'] = true; this.checkLoadStatus()}, crossOrigin : "anonymous",  href : LHC_API.args.lhc_base_url + '/widgetrestapi/themestatus/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
        }

        this.cont.insertCssRemoteFile({onload: ()=>{this.loadStatus['main'] = true; this.checkLoadStatus()}, crossOrigin : "anonymous",  href : this.attributes.staticJS['status_css'] });

        attributes.onlineStatus.subscribe((data) => this.toggleOfflineIcon(data));

        if (this.attributes.mode !== 'popup') {
            attributes.widgetStatus.subscribe((data) => {
                data == true ? this.hide() : this.show();
            });
        } else {
            this.show()
        }
 
        attributes.eventEmitter.addListener('unread_message', () => {
            var icon = this.cont.getElementById("lhc_status_container");
            helperFunctions.addClass(icon, "has-uread-message");
        });

        attributes.eventEmitter.addListener('focusChanged', (status) => {
            if (status == true) {
                this.removeUnreadIndicator();
            }
        });
    }

    hide () {
        this.cont.hide();
        this.removeUnreadIndicator();
    }

    removeUnreadIndicator() {
        var icon = this.cont.getElementById("lhc_status_container");
        helperFunctions.removeClass(icon, "has-uread-message");
    }

    show () {
        if (this.attributes.hideOffline === false) {

            const chatParams = this.attributes['userSession'].getSessionAttributes();

            // show status icon only if we are not in api mode or chat is going now
            if (this.attributes['position'] != 'api' || this.attributes['position'] == 'api' && chatParams['id'] && chatParams['hash']) {
                this.cont.show();
            }

        } else {
            this.cont.hide();
        }
    }
}