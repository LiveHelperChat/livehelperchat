import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class mainWidget{
    constructor(prefix) {

        this.attributes = {};

        this.width = '350';
        this.height = '520';
        this.bottom = '30';
        this.right = '30';
        this.units = 'px';
        this.originalCSS = '';
        this.bottom_override = false;

        this.cont = new UIConstructorIframe((prefix || 'lhc')+'_widget_v2', helperFunctions.getAbstractStyle({
            zindex: "2147483640",
            width: "95px",
            height: "95px",
            position: "fixed",
            display: "none",
            maxheight: "95px",
            maxwidth: "95px",
            minheight: "95px",
            minwidth: "95px"
        }),  {"role":"presentation"}, "iframe");

        this.isLoaded = false;

        this.loadStatus = {main: false, css: false, theme: false, font_status: false, font_preload: true, css_preload: true};
    }

    resize() {

        let restyleStyle = {
            height: this.height + this.units,
            "min-height": this.height + this.units,
            "max-height": this.height + this.units,
            width: this.width + this.units,
            "min-width": this.width + this.units,
            "max-width": this.width + this.units,
            bottom: (this.units == 'px' ? this.bottom + "px" : '0px')
        };

        if ((this.attributes.position_placement == 'middle_right' || this.attributes.position_placement == 'middle_left') && this.bottom_override == true) {
            restyleStyle['bottom'] =  "calc(50% + 20px)";
        }

        if (this.attributes.position_placement == 'middle_left' || this.attributes.position_placement == 'bottom_left' || this.attributes.position_placement == 'full_height_left') {
            restyleStyle['left'] = (this.units == 'px' ? this.right + "px" : '0px');
        } else {
            restyleStyle['right'] = (this.units == 'px' ? this.right + "px" : '0px');
        }

        if ((this.attributes.position_placement == 'full_height_right' || this.attributes.position_placement == 'full_height_left') && !this.bottom_override) {
            restyleStyle['min-height'] = '100%';
            restyleStyle['max-height'] = '100%';
            restyleStyle['height'] = '100%';
            restyleStyle['bottom'] = '0px';

            if (this.attributes.position_placement == 'full_height_left') {
                restyleStyle['left'] = '0px';
            } else {
                restyleStyle['right'] = '0px';
            }
        }

        if (this.attributes.mode == 'embed') {
            restyleStyle["max-width"] = '100%';
            restyleStyle["min-width"] = '100%';
            restyleStyle["width"] = '100%';
            restyleStyle["position"] = (this.attributes.fscreen ? 'fixed' : 'relative') + '!important';
            restyleStyle["bottom"] = 'auto';
            restyleStyle["right"] = 'auto';
        }

        this.cont.massRestyle(restyleStyle);

        var eldoc = null;

        if (this.cont.elmDomDoc && (eldoc = this.cont.elmDomDoc.getElementById('root')) && eldoc) {
            if (this.attributes.position_placement == 'full_height_left' || this.attributes.position_placement == 'full_height_right') {
                eldoc.classList.add('lhc-full-height');
            } else if (eldoc.classList.contains('lhc-full-height')) {
                eldoc.classList.remove('lhc-full-height');
            }

            if (this.cont.elmDomDoc.getElementById('id-invitation-height')) {
                eldoc.classList.add('lhc-invitation-view');
            } else {
                eldoc.classList.remove('lhc-invitation-view');
            }
        }
    }

    checkLoadStatus() {
        if (this.loadStatus['css'] == true && this.loadStatus['theme'] == true && this.loadStatus['font_status'] == true && this.loadStatus['font_preload'] == true && this.loadStatus['css_preload'] == true) {
            this.loadApp();
        }
    }

    checkLoadApp() {
        this.attributes.wloaded.next(true);
    }

    makeContent() {
        this.cont.bodyId = 'chat-widget';

        this.cont.tmpl = '<div id="root" class="container-fluid d-flex flex-column flex-grow-1 fade-in ' + (this.attributes.isMobile === true ? 'lhc-mobile' : 'lhc-desktop') + (this.attributes.fscreen ? ' lhc-fscreen' : '') + (this.attributes.position_placement == 'full_height_left' || this.attributes.position_placement == 'full_height_right' ? ' lhc-full-height' : '')+'"></div>';

        if (this.cont.constructUIIframe('', this.attributes.staticJS['dir'], this.attributes.staticJS['cl'], this.attributes.hhtml, !this.attributes.viewport_enabled) === null) {
            this.isLoaded = true;
            return null;
        }

        this.cont.elmDom.className = this.attributes.isMobile === true ? 'lhc-mobile lhc-mode-'+this.attributes.mode : 'lhc-desktop lhc-mode-'+this.attributes.mode;

        if (this.attributes.cont_ss) {
            this.originalCSS = this.cont.elmDom.style.cssText;
            this.cont.elmDom.style.cssText += this.attributes.cont_ss;
        }
    }

    init(attributes, lazyLoad) {

        this.attributes = attributes;

        if (this.makeContent() === null) {
            return null;
        };

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

        if (attributes.widgetDimesions.valueInternal['units'] == 'px' && this.attributes.isMobile == false && this.attributes.mode != 'embed')
        {
            this.screenAttributesUpdate = () => {

                if (window.innerHeight < attributes.widgetDimesions.valueInternal['height'] + 60 + (this.attributes.clinst === true ? 70 : 0)) {
                    attributes.widgetDimesions.nextPropertySilent('height_soverride', window.innerHeight - 60 - (this.attributes.clinst === true ? 70 : 0));
                } else {
                    attributes.widgetDimesions.nextPropertySilent('height_soverride', null);
                }

                if (window.innerWidth < attributes.widgetDimesions.valueInternal['width'] + 60) {
                    attributes.widgetDimesions.nextPropertySilent('width_soverride', window.innerWidth - 60);
                } else {
                    attributes.widgetDimesions.nextPropertySilent('width_soverride', null);
                }

                attributes.widgetDimesions.callListeners();
            };

            this.screenAttributesUpdate();

            window.addEventListener('resize', this.screenAttributesUpdate);
        }

        attributes.widgetDimesions.subscribe(this.monitorDimensionsWrap);

        attributes.eventEmitter.addListener('reloadWidget',() => {
            this.isLoaded = false;
            this.makeContent();
            attributes.eventEmitter.emitEvent('widgetHeight',[{'reset_height' : true}]);
            this.toggleVisibility(attributes.widgetStatus.valueInternal);
        });

    }

    bootstrap() {

        if (this.isLoaded === true) {
            return ;
        }

        this.isLoaded = true;

        if (this.attributes.staticJS['fontCSS']) {
            this.cont.insertCssRemoteFile({rel:"stylesheet", crossOrigin : "anonymous",  href : this.attributes.staticJS['fontCSS']});
        }

        if (this.attributes.staticJS['font_status']) {
            this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['font_status'] = true; this.checkLoadStatus()},"as":"font", rel:"preload", type: "font/woff", crossOrigin : "anonymous",  href : this.attributes.staticJS['font_status']});
        }

        if (this.attributes.staticJS['font_preload']) {
            this.loadStatus['font_preload'] = false;
            this.attributes.staticJS['font_preload'].forEach((item) => {
                this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['font_preload'] = true; this.checkLoadStatus()},"as":"font", rel:"preload", type: "font/woff", crossOrigin : "anonymous",  href : item});
            });
        }

        if (this.attributes.staticJS['css_preload']) {
            this.loadStatus['css_preload'] = false;
            this.attributes.staticJS['css_preload'].forEach((item) => {
                this.cont.insertCssRemoteFile({onload: () => {this.loadStatus['css_preload'] = true; this.checkLoadStatus()}, "as":"style", crossOrigin : null, rel:"preload", href : item});
            });
        }

        if (this.attributes.theme) {
            this.loadStatus['theme'] = false;
            this.cont.insertCssRemoteFile({onload: ()=> {this.loadStatus['theme'] = true; this.checkLoadStatus()}, crossOrigin : "anonymous",  href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/theme/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
        }

        this.cont.insertCssRemoteFile({onload: () => {
                this.loadStatus['css'] = true;
                this.checkLoadStatus();
            },crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']}, true);

        if (this.attributes.isMobile == true && this.attributes.mode == 'widget') {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_mobile_css']});
        }

        if (this.attributes.mode == 'embed') {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['embed_css'] });

            if (this.attributes.staticJS['page_css']) {
                helperFunctions.insertCssRemoteFile({crossOrigin : "anonymous", id: "lhc-theme-page", href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themepage/' + this.attributes.theme + '?v=' + this.attributes.theme_v});
            }
        }
    }

    loadApp() {
        this.cont.insertJSFile(this.attributes.staticJS['vendor'], false, () => {
            this.cont.insertJSFile(this.attributes.staticJS['app'], false, () => {
                this.loadStatus['main'] = true;
                this.checkLoadApp();
            }, {'scope': this.attributes.prefixLowercase});
        }, {'scope': this.attributes.prefixLowercase});

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
        this.width = data.width_override || data.width_soverride || data.width;
        this.height = data.height_override || data.height_soverride || data.height;
        this.bottom = data.bottom_override ? (data.bottom_override + (data.wbottom ? data.wbottom : 0)) : (30 + (this.attributes.clinst === true ? 70 : 0) + (data.wbottom ? data.wbottom : 0));
        this.right = data.right_override ? (data.right_override + (data.wright_inv ? data.wright_inv : 0)) : (30 + (data.wright ? data.wright : 0));
        this.units = (data.width_override || data.height_override || data.bottom_override || data.right_override) ? 'px' : data.units;

        this.resize();

        this.bottom_override = !!data.bottom_override;
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