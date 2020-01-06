import {UIConstructorIframe} from '../UIConstructorIframe';
import {helperFunctions} from '../helperFunctions';

export class mainWidget{
    constructor() {

        this.attributes = {};

        this.width = '350';
        this.height = '520';
        this.units = 'px';

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

        this.cont.tmpl = '<div id="root" class="container-fluid d-flex flex-column flex-grow-1 overflow-auto"></div>';
    }

    resize() {

        let restyleStyle = {
            height: this.height + this.units + " !important",
            "min-height": this.height + this.units + " !important",
            "max-height": this.height + this.units + " !important",
            width: this.width + this.units + " !important",
            "min-width": this.width + this.units + " !important",
            "max-width": this.width + this.units +  " !important",
            bottom: (this.units == 'px' ? "30px !important" : '0px !important'),
            right: (this.units == 'px' ? "30px !important" : '0px !important'),
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

    init(attributes) {
        this.cont.constructUIIframe('');
        this.attributes = attributes;

        this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']});
        
        if (this.attributes.isMobile == true && this.attributes.mode == 'widget') {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_mobile_css']});
        }

        if (this.attributes.mode == 'embed') {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['embed_css'] });
        }

        if (this.attributes.theme > 0) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : LHC_API.args.lhc_base_url + '/widgetrestapi/theme/' + this.attributes.theme});
        }

        this.cont.insertJSFile(this.attributes.staticJS['app'], false);

        this.monitorDimensionsWrap = (data) => {
            this.monitorDimensions(data);
        };

        this.toggleVisibilityWrap = (data) => {
            this.toggleVisibility(data);
        };

        attributes.widgetDimesions.subscribe(this.monitorDimensionsWrap);

        //setTimeout(() => {
            attributes.widgetStatus.subscribe(this.toggleVisibilityWrap);
        //},250);
    }

    toggleVisibility(data) {
        data == false ? this.hide() : this.show();
    }

    monitorDimensions(data) {
        this.width = data.width;
        this.height = data.height;
        this.units = data.units;
        this.resize();
    }

    hide () {
        this.cont.hide();
    }

    show () {
         this.cont.show();
    }
}