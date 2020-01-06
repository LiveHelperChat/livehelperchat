import {UIConsturctor} from '../UIConsturctor';

export class containerChat{
    constructor() {
        this.cont = new UIConsturctor('lhc_container_v2', "border: 0 none !important; padding: 0 !important; margin: 0 !important; z-index: 999999999 !important; overflow : visible !important; min-width: 0 !important; min-height: 0 !important; max-width: none !important; max-height: none !important; width : auto !important; height : auto !important;");
        this.cont.constructUI();
        document.body.appendChild(this.cont.elmDom);
    }
}