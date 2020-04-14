export class mainWidgetPopup{
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

    init(attributes) {

        if (this.cont.elementReferrerPopup && this.cont.elementReferrerPopup.closed === false) {
            this.cont.elementReferrerPopup.focus();
        } else {

            this.attributes = attributes;

            let attr =  {
                'static_chat' : this.attributes['userSession'].getSessionAttributes()
            };

            let urlArgumetns = '';

            if (attr['static_chat']['id'] && attr['static_chat']['hash']) {
                urlArgumetns = urlArgumetns + "/(id)/"+attr['static_chat']['id'] + "/(hash)/" + attr['static_chat']['hash'];
            }

            if (this.attributes['theme'] !== null) {
                urlArgumetns = urlArgumetns + "/(theme)/" + this.attributes['theme'];
            }

            if (attr['static_chat']['vid']!== null) {
                urlArgumetns = urlArgumetns + "/(vid)/" + attr['static_chat']['vid'];
            }

            if (this.attributes['isMobile']){
                urlArgumetns = urlArgumetns + "/(mobile)/true";
            }

            if (this.attributes['department'].length > 0) {
                urlArgumetns = urlArgumetns + "/(department)/" + this.attributes['department'].join('/');
            }

            if (this.attributes['identifier'] != '') {
                urlArgumetns = urlArgumetns + "/(identifier)/" +this.attributes['identifier'];
            }

            if (this.attributes['operator'] !== null) {
                urlArgumetns = urlArgumetns + "/(operator)/" +this.attributes['operator'];
            }

            if (this.attributes['survey'] !== null) {
                urlArgumetns = urlArgumetns + "/(survey)/" +this.attributes['survey'];
            }

            if (this.attributes['priority'] !== null) {
                urlArgumetns = urlArgumetns + "/(priority)/" +this.attributes['priority'];
            }

            if (this.attributes['proactive']['invitation']) {
                urlArgumetns = urlArgumetns + "/(inv)/" +this.attributes['proactive']['invitation'];
                //this.attributes['proactive'] = {};
                if (this.attributes['mode'] == 'popup') {
                    this.attributes.storageHandler.setSessionStorage('LHC_invt',1);
                }
            }

            if (this.attributes['userSession'].getSessionReferrer() !== null) {
                urlArgumetns = urlArgumetns + '?ses_ref=' + this.attributes['userSession'].getSessionReferrer();
            }

            this.cont.elementReferrerPopup = window.open(this.attributes['base_url']+this.attributes['lang']+"chat/start"+urlArgumetns,'lhc_popup_v2',"scrollbars=yes,menubar=1,resizable=1,width="+this.attributes['popupDimesnions']['pwidth']+",height="+this.attributes['popupDimesnions']['pheight']);

        }


    }
}