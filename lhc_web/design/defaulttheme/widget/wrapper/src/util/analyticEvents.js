import {domEventsHandler} from '../util/domEventsHandler';
import {helperFunctions} from '../lib/helperFunctions';

class _analyticEvents {
    constructor() {
        this.params = {};
    }

    setParams(params, attributes) {
        this.params = params;
        this.attributes = attributes;
        this.initMonitoring();
    }

    initMonitoring() {
        console.log(this.params);
        this.params['ga']['events'].forEach((item) => {
            this.attributes.eventEmitter.addListener(item.ev, (params) => {
                 console.log('from listener ' + item.ev);
                 console.log('from listener ' + this.params['ga']['js']);
            });
        });
    }
}

const analyticEvents = new _analyticEvents();
export {analyticEvents};