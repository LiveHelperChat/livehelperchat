import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class mainWidget{
    constructor() {

        this.attributes = {};

        this.width = '350';
        this.height = '520';
        this.bottom = '30';
        this.right = '30';
        this.units = 'px';
        this.originalCSS = '';

        this.cont = new UIConstructorIframe('lhc_widget_v2', helperFunctions.getAbstractStyle({
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

        this.cont.tmpl = '<div id="root" class="container-fluid d-flex flex-column flex-grow-1 overflow-auto fade-in"></div>';
        
        this.isLoaded = false;
    }

    resize() {

        let restyleStyle = {
            height: this.height + this.units + " !important",
            "min-height": this.height + this.units + " !important",
            "max-height": this.height + this.units + " !important",
            width: this.width + this.units + " !important",
            "min-width": this.width + this.units + " !important",
            "max-width": this.width + this.units +  " !important",
            bottom: (this.units == 'px' ? this.bottom + "px !important" : '0px !important'),
            right: (this.units == 'px' ? this.right + "px !important" : '0px !important'),
        };

        if (this.attributes.mode == 'embed') {
            restyleStyle["max-width"] = '100%';
            restyleStyle["min-width"] = '100%';
            restyleStyle["width"] = '100%';
            restyleStyle["position"] = 'relative';
            restyleStyle["bottom"] = 'auto';
            restyleStyle["right"] = 'auto';
        }

        this.cont.massRestyle(restyleStyle);
    }

    init(attributes, lazyLoad) {

        this.attributes = attributes;

        this.cont.constructUIIframe('', this.attributes.staticJS['dir'], this.attributes.staticJS['cl'], this.attributes.hhtml);

        if (this.attributes.cont_ss) {
            this.originalCSS = this.cont.elmDom.style.cssText;
            this.cont.elmDom.style.cssText += this.attributes.cont_ss;
        }

        const chatParams = this.attributes['userSession'].getSessionAttributes();

        if (chatParams['id'] || !lazyLoad) {
            this.bootstrap();
        }
        
        this.toggleVisibilityWrap = (data) => {
                this.toggleVisibility(data);
        };

        attributes.widgetStatus.subscribe(this.toggleVisibilityWrap);

        this.monitorDimensionsWrap = (data) => {
            this.monitorDimensions(data);
        };

        attributes.widgetDimesions.subscribe(this.monitorDimensionsWrap);
    }

    bootstrap() {

        if (this.isLoaded === true) {
            return ;
        }

        this.isLoaded = true;

        if (this.attributes.staticJS['fontCSS']) {
            this.cont.insertCssRemoteFile({rel:"stylesheet", crossOrigin : "anonymous",  href : this.attributes.staticJS['fontCSS']});
        }

        if (this.attributes.staticJS['font_widget']) {
            this.cont.insertCssRemoteFile({"as":"font", rel:"preload", type: "font/woff2", crossOrigin : "anonymous",  href : this.attributes.staticJS['font_widget']});
        }

        if (this.attributes.theme > 0) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : LHC_API.args.lhc_base_url + '/widgetrestapi/theme/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        }

        this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']}, true);

        if (this.attributes.isMobile == true && this.attributes.mode == 'widget') {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_mobile_css']});
        }

        if (this.attributes.mode == 'embed') {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['embed_css'] });
        }

        this.cont.insertJSFile(this.attributes.staticJS['app'], false);

        if (this.attributes.staticJS['ex_js'] && this.attributes.staticJS['ex_js'].length > 0) {
            this.attributes.staticJS['ex_js'].forEach((item) => {
                this.cont.insertJSFile(item, false);
            });
        }
    }

    toggleVisibility(data) {
        data == false ? this.hide() : this.show();
    }

    monitorDimensions(data) {
        this.width = data.width_override || data.width;
        this.height = data.height_override || data.height;
        this.bottom = data.bottom_override || 30;
        this.right = data.right_override || 30;

        this.units = (data.width_override || data.height_override || data.bottom_override || data.right_override) ? 'px' : data.units;
        this.resize();
    }

    hide () {
        this.cont.hide();
    }

    hideInvitation() {
        if (this.attributes.cont_ss) {
            this.cont.elmDom.style.cssText += this.attributes.cont_ss;
        }
    }

    showInvitation() {
        if (this.attributes.cont_ss) {
            this.cont.elmDom.style.cssText = this.originalCSS;
        }
        this.show();
    }

    show () {
         if (this.isLoaded === false) {
             this.bootstrap();
         }
         this.cont.show();
    }
}