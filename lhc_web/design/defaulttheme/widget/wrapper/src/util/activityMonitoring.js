import {domEventsHandler} from '../util/domEventsHandler';
import {helperFunctions} from '../lib/helperFunctions';

class _activityMonitoring {
    constructor() {
        this.params = {};
        this.timeoutStatuscheck = null;
        this.timeoutActivity = null;
        this.attributes = null;
        this.userActive = 1;
    }

    attatchActivityListeners() {
        if (this.params['track_activity']) {

            var resetTimeout = () => {
                this.resetTimeoutActivity();
            };

            if (this.params['track_mouse']) {
                domEventsHandler.listen(window, 'mousemove', resetTimeout, 'lhc_mousemove_w');
                domEventsHandler.listen(document, 'mousemove', resetTimeout, 'lhc_mousemove_d');
            }

            domEventsHandler.listen(window, 'mousedown', resetTimeout, 'lhc_mousedown');
            domEventsHandler.listen(window, 'click', resetTimeout, 'lhc_click');
            domEventsHandler.listen(window, 'scroll', resetTimeout, 'lhc_scroll');
            domEventsHandler.listen(window, 'keypress', resetTimeout, 'lhc_keypress');
            domEventsHandler.listen(window, 'load', resetTimeout, 'lhc_load');
            domEventsHandler.listen(document, 'scroll', resetTimeout, 'lhc_scroll');
            domEventsHandler.listen(document, 'touchstart', resetTimeout, 'lhc_touchstart');
            domEventsHandler.listen(document, 'touchend', resetTimeout, 'lhc_touchend');

            this.resetTimeoutActivity();
        }
    }

    resetTimeoutActivity() {
        var wasInactive = this.userActive == 0;

        this.userActive = 1;

        if (wasInactive == true) {
            this.syncUserStatus(1);
        }

        clearTimeout(this.timeoutActivity);

        this.timeoutActivity = setTimeout(() => {
            this.userActive = 0;
            this.syncUserStatus(1);
        }, 300 * 1000);
    }

    setParams(params, attributes) {
        this.params = params;
        this.attributes = attributes;
        this.attatchActivityListeners();
        this.initMonitoring();
    }

    initMonitoring() {
        clearTimeout(this.timeoutStatuscheck);
        this.timeoutStatuscheck = setTimeout(() => {
            this.syncUserStatus(0);
            this.initMonitoring();
        }, this.params['timeout'] * 1000);
    }

    syncUserStatus(sender) {
        const chatParams = this.attributes['userSession'].getSessionAttributes();

        let params = {
            'vid': this.attributes.userSession.getVID(),
            'wopen': (this.attributes.widgetStatus.value ? 1 : 0),
            'uaction': sender,
            'uactiv': this.userActive,
            'dep': this.attributes.department.join(',')
        };

        if (chatParams['id'] && chatParams['hash']) {
            params['hash'] = chatParams['id'] + '_' + chatParams['hash'];
        }

        helperFunctions.makeRequest(LHC_API.args.lhc_base_url + this.attributes['lang'] + 'widgetrestapi/chatcheckstatus', {params: params}, (data) => {
            if (data.change_status == true && this.attributes.onlineStatus.value != data.online) {
                this.attributes.onlineStatus.next(data.online);
            }
        });
    }

}

const activityMonitoring = new _activityMonitoring();
export {activityMonitoring};