import {settings} from '../settings.js';
import {helperFunctions} from '../helperFunctions';

class LHCStatusWidget extends HTMLElement {
    constructor() {
        super();

        // Create a shadow root
        this.attachShadow({mode: 'open'});
        this._ee = null;
    }

    set ee(ee) {
        this._ee = ee;
    }

    dispatchEventStatus(event,data, e)
    {
        if (this._ee) {
            this._ee(event, data, e);
        }
    }

    show() {
        this.style.setProperty("display","block","important");
        window.orientation === undefined && this.resize();
    }

    hide() {
        this.style.setProperty("display","none","important");
    }

    connectedCallback() {

        // Create a button element and add it to the shadow DOM
        let style = document.createElement('style');
        style.textContent = `html,body,div,span,object,iframe,h1,h2,h3,h4,h5,h6,p,blockquote,pre,abbr,address,cite,code,del,dfn,em,img,ins,kbd,q,samp,small,strong,sub,sup,var,b,i,dl,dt,dd,ol,ul,li,fieldset,form,label,legend,table,caption,tbody,tfoot,thead,tr,th,td,article,aside,canvas,details,figcaption,figure,footer,header,hgroup,menu,nav,section,summary,time,mark,audio,video{margin:0;padding:0;border:0;outline:0;font-size:100%;vertical-align:baseline;background:transparent}body{line-height:1}article,aside,details,figcaption,figure,footer,header,hgroup,menu,nav,section{display:block}nav ul{list-style:none}blockquote,q{quotes:none}blockquote:before,blockquote:after,q:before,a{margin:0;padding:0;font-size:100%;vertical-align:baseline;background:transparent}ins{background-color:#ff9;color:#000;text-decoration:none}mark{background-color:#ff9;color:#000;font-style:italic;font-weight:bold}del{text-decoration:line-through}abbr[title],dfn[title]{border-bottom:1px dotted;cursor:help}table{border-collapse:collapse;border-spacing:0}hr{display:block;height:1px;border:0;border-top:1px solid #ccc;margin:1em 0;padding:0}input,select{vertical-align:middle}:root{cursor : pointer; height: 100% !important;min-height: 100% !important;max-height: 100% !important;width: 100% !important;min-width: 100% !important;max-width: 100% !important;}body{display: flex;flex-direction: column;background:transparent;font:13px Helvetica,Arial,sans-serif;position:relative}.clear{clear:both}.clearfix:after{content:\'\';display:block;height:0;clear:both;visibility:hidden}`;
        this.style = "-webkit-tap-highlight-color: transparent;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;" +
            "    user-select: none;outline: none;  box-shadow: none;user-select:none;cursor:pointer;box-shadow: none; overflow: visible; color-scheme: light;" +
            "    min-height: 95px; min-width: 95px; max-height: 95px; max-width: 95px; width: 95px; height: 95px; z-index: 2147483640;" +
            "    border-radius: unset; outline: none !important; visibility: visible !important; resize: none !important; background: none transparent !important;" +
            "    opacity: 1 !important; position: fixed !important; border: 0px !important; padding: 0px !important; margin: 0px !important;" +
            "    float: none !important;display: none !important;transition-property: transform !important;transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1) !important;" +
            "    transform: translate3d(0px," + this.getAttribute("vertical-y") + "px,0px) !important;transition-duration: 800ms !important;"+
            (this.getAttribute("vertical-placement") == "top" ? "bottom:auto;" : "top:auto;") +
            (this.getAttribute("horizontal-placement") == "right" ? "left:auto;" : "right:auto;") +
            this.getAttribute("horizontal-placement")+":"+this.getAttribute("horizontal-space")+"px;"+
            this.getAttribute("vertical-placement")+":"+this.getAttribute("vertical-space")+this.getAttribute("vertical-unit");

        this.shadowRoot.appendChild(style);
    }

    massRestyle(a) {
        for (var b in a) a.hasOwnProperty(b) && this.restyle(b, a[b])
    }

    restyle(attr, style) {
        this.style[attr] = style;
    };

    setContent(content) {
        let contentShadow = document.createElement('div');
        contentShadow.innerHTML = content;
        this.shadowRoot.appendChild(contentShadow.firstChild);
    }

    resize() {
        var initX, initY;
        const rect = this.getBoundingClientRect();

        if (rect.height == 0) {
            return;
        }

        let verticalSpace = parseInt(this.getAttribute('vertical-space'));
        if (rect.top - verticalSpace < 0) {
            const style = window.getComputedStyle(this)
            const matrix = new DOMMatrixReadOnly(style.transform)
            initY = matrix.m42;
            initX = matrix.m41;
            this.style.transform = "translate3d(" + initX + "px, " + (initY + Math.abs(rect.top - verticalSpace)) + "px, 0px)";
            this.dispatchEventStatus('move_finish', {"x" : 0, "y": (initY - ((rect.bottom + verticalSpace) - (window.innerHeight || document.documentElement.clientHeight))), "bottom": this.style.bottom, "top": this.style.top, "left": this.style.left, "right": this.style.right});
        } else if (rect.bottom + verticalSpace > (window.innerHeight || document.documentElement.clientHeight)) {
            const style = window.getComputedStyle(this)
            const matrix = new DOMMatrixReadOnly(style.transform)
            initY = matrix.m42;
            initX = matrix.m41;
            this.style.transform = "translate3d(" + initX + "px, " + (initY - ((rect.bottom + verticalSpace) - (window.innerHeight || document.documentElement.clientHeight))) + "px, 0px)";
            this.dispatchEventStatus('move_finish', {"x" : 0, "y": (initY - ((rect.bottom + verticalSpace) - (window.innerHeight || document.documentElement.clientHeight))), "bottom": this.style.bottom, "top": this.style.top, "left": this.style.left, "right": this.style.right});
        }
    }

    attachEvents() {
        var object = this,
            initX, initY, firstX, firstY, objectMoved = false, lastTouch, moveAction = false, dragEnabled = this.getAttribute("drag-enabled") === "true";

        try {
            if (dragEnabled === true && this.getAttribute('vertical-unit') === 'px') {
                if (window.orientation !== undefined) {
                    screen.orientation.addEventListener("change", resize);
                } else {
                    window.addEventListener('resize', resize, false);
                }
            }
        } catch (e) {
            console.log(e);
        }

        dragEnabled === true && this.getAttribute('vertical-unit') === 'px' && this.addEventListener('mousedown', function (e) {
            e.preventDefault();
            this.style.transitionDuration = "0ms";

            firstX = e.pageX;
            firstY = e.pageY;

            const style = window.getComputedStyle(this)
            const matrix = new DOMMatrixReadOnly(style.transform)
            initY = matrix.m42;
            initX = matrix.m41;

            document.addEventListener('mousemove', dragIt, false);
            document.addEventListener('mouseup', mouseUp, false);
        }, false);

        dragEnabled === true && this.getAttribute('vertical-unit') === 'px' && this.addEventListener('touchstart', function (e) {

            // If we uncomment click event will be ignored on our component
            // It has to stay commented out
            // e.preventDefault();
            this.style.transitionDuration = "0ms";

            var touch = e.touches;
            firstX = touch[0].pageX;
            firstY = touch[0].pageY;

            const style = window.getComputedStyle(this)
            const matrix = new DOMMatrixReadOnly(style.transform)
            initY = matrix.m42;
            initX = matrix.m41;

            this.addEventListener('touchmove', swipeIt, false);
            window.addEventListener('touchend', touchEnd, false);
        }, false);

        function resize() {
            object.resize();
        }

        function mouseUp(e) {

            if (objectMoved === true) {
                moveAction = true;
            }

            document.removeEventListener('mousemove', dragIt, false);
            document.removeEventListener('mouseup', mouseUp, false);

            if (objectMoved === true) {
                verifyPosition(e, false);
            }

            objectMoved = false;
        }

        function dragIt(e) {
            objectMoved = true;
            let XPos =  (initX + e.pageX - firstX), YPos = (initY + e.pageY - firstY) ;
            object.style.transform = "translate3d(" + XPos + "px, " + YPos + "px, 0px)";
            object.dispatchEventStatus('move', {"x" : XPos, "y": YPos, "bottom": object.style.bottom, "top": object.style.top, "left": object.style.left, "right": object.style.right});
        }

        function touchEnd(e) {

            this.removeEventListener('touchmove', swipeIt, false);
            window.removeEventListener('touchend', touchEnd, false);

            if (objectMoved === true) {
                verifyPosition(e, true);
            }

            objectMoved = false;
        }

        function verifyPosition(e, isMobile) {

            const rect = object.getBoundingClientRect();

            let resetVertical = false, switchVerticalTop = false, switchVerticalBottom = false;

            if (rect.top - parseInt(object.getAttribute('vertical-space')) < 0) {
                if (object.style.top === "auto") {
                    object.style.top = object.getAttribute('vertical-space')+object.getAttribute("vertical-unit");
                    object.style.bottom = "auto";
                    switchVerticalTop = true;
                }
                resetVertical = true;
            } else if (rect.bottom + parseInt(object.getAttribute('vertical-space')) > (window.innerHeight || document.documentElement.clientHeight)) {
                if (object.style.bottom === "auto") {
                    object.style.top = "auto";
                    object.style.bottom = object.getAttribute('vertical-space')+object.getAttribute("vertical-unit");
                    switchVerticalBottom = true;
                }
                resetVertical = true;
            }

            let posYHistory = initY + (isMobile === false ? e.pageY : lastTouch.pageY) - firstY;

            const style = window.getComputedStyle(object);
            const matrix = new DOMMatrixReadOnly(style.transform);
            initX = matrix.m41;

            if (switchVerticalTop === true) {
                posYHistory = rect.top - parseInt(object.getAttribute('vertical-space'));
            } else if (switchVerticalBottom === true) {
                posYHistory = ((window.innerHeight || document.documentElement.clientHeight) - rect.top - rect.height - parseInt(object.getAttribute('vertical-space'))) * -1;
            }

            let posY = resetVertical === true ? 0 : (initY + (isMobile === false ? e.pageY : lastTouch.pageY) - firstY);

            if (resetVertical === false) {
                if (rect.top + (rect.height / 2) > (window.innerHeight || document.documentElement.clientHeight) / 2) { // Bottom
                    // Need to switch to bottom position
                    if (object.style.bottom === "auto") {
                        object.style.top = "auto";
                        object.style.bottom = object.getAttribute('vertical-space')+object.getAttribute("vertical-unit");
                        posYHistory = posY = ((window.innerHeight || document.documentElement.clientHeight) - rect.top - rect.height - parseInt(object.getAttribute('vertical-space'))) * -1;
                    }
                } else { // Top
                    if (object.style.top === "auto") {
                        object.style.top = object.getAttribute('vertical-space')+object.getAttribute("vertical-unit");
                        object.style.bottom = "auto";
                        posYHistory = posY = rect.top - parseInt(object.getAttribute('vertical-space'));
                    }
                }
            }

            let is_right_side = object.style.left == "auto";
            let need_reposition;

            if (rect.left + (rect.width / 2) > (window.innerWidth || document.documentElement.clientWidth) / 2) {
                if (is_right_side === false) {
                    object.style.right = object.getAttribute('horizontal-space')+"px";
                    object.style.left = "auto";
                }
                need_reposition = !is_right_side;
            } else {
                if (is_right_side === true) {
                    object.style.left = object.getAttribute('horizontal-space')+"px";
                    object.style.right = "auto";
                }
                need_reposition = is_right_side;
            }

            if (need_reposition) {
                if (is_right_side) {
                    initX = rect.left - parseInt(object.getAttribute('horizontal-space'));
                } else {
                    initX = ((document.documentElement.clientWidth - rect.left - rect.width - parseInt(object.getAttribute('horizontal-space'))) * -1);
                }
            }

            object.style.transform = "translate3d(" + initX + "px, " + posYHistory + "px, 0px)";

            object.dispatchEventStatus('move', {"x" : initX, "y": posYHistory, "bottom": object.style.bottom, "top": object.style.top, "left": object.style.left, "right": object.style.right});

            // Does not work properly on apple Safari :(
            /*window.requestAnimationFrame(function () {*/
            /*object.style.transitionDuration = "800ms";
            object.style.transform = "translate3d(0px, " + posY + "px, 0px)";*/
            /*});*/

            setTimeout(function(){
                object.style.transitionDuration = "800ms";
                object.style.transform = "translate3d(0px, " + posY + "px, 0px)";
                object.dispatchEventStatus('move_finish', {"x" : 0, "y": posY, "bottom": object.style.bottom, "top": object.style.top, "left": object.style.left, "right": object.style.right});
            },20)
        }

        function swipeIt(e) {
            // This one has to be present otherwise while dragging
            // Page will refresh
            e.preventDefault();
            objectMoved = true;
            var contact = e.touches;
            let XPos = (initX + contact[0].pageX - firstX), YPos = (initY + contact[0].pageY - firstY);
            this.style.transform = "translate3d(" + XPos + "px, " + YPos + "px, 0px)";
            lastTouch = contact[0];
            object.dispatchEventStatus('move', {"x" : XPos, "y": YPos, "bottom": object.style.bottom, "top": object.style.top, "left": object.style.left, "right": object.style.right});
        }

        this.addEventListener('click', function (e) {
            // On desktop we should not trigger if widget location was changed as it was
            // Drag and drop action performed by visitor
            if (moveAction === true) {
                moveAction = false;
                this.dispatchEventStatus('click',{'status': false}, e);
            } else {
                this.dispatchEventStatus('click',{'status': true}, e);
            }
        });

        // this.getAttribute('vertical-unit') === 'px' && resize();
    }

    insertCssRemoteFile(attr) {

        var elm = null;

        if (attr.id && attr.href && (elm = this.shadowRoot.getElementById(attr.id)) !== null) {
            elm.href = attr.href
            return;
        }

        var d = this.shadowRoot,
            e = document.createElement('link');

        e.rel = "stylesheet";
        e.crossOrigin = "*";

        for (var b in attr) e[b] = attr[b];
        d.appendChild(e);
    }

}

export class statusWidget{
    constructor(prefix, version) {

        this.attributes = {};
        this.controlMode = false;
        this.showDelay = null;
        this.statusDelayProcessed = false;

        // Define the custom element
        let componentId = (prefix || 'lhc') + '-' + version + '-status-widget';

        if (!customElements.get(componentId)) {
            customElements.define(componentId, LHCStatusWidget);
        }

        this.cont = document.createElement(componentId);

        Object.entries({
            "id" : (prefix || 'lhc')+'_status_widget_v2',
            "vertical-placement" : "bottom",
            "horizontal-placement" : "right",
            "horizontal-space" : "0",
            "vertical-space" : "0",
            "vertical-unit" : "px",
            "vertical-y" : 0,
            "drag-enabled" : false,
            "role" : "presentation",
            "translate" : "no"
        }).forEach(([k, v]) => this.cont.setAttribute(k, v));

        this.cont.style = "display: none";

        this.loadStatus = {main : false, theme: false, font: true, widget : false, shidden: false};
        this.lload = false;
        this.unread_counter = 0;
    }

    toggleOfflineIcon(onlineStatus) {

        var icon = this.cont.shadowRoot.getElementById("status-icon");

        if (onlineStatus) {
            if (!this.attributes.leaveMessage) {
                this.show();
            }
            helperFunctions.removeClass(icon, "offline-status");
        } else {
            if (!this.attributes.leaveMessage) {
                this.hide();
            } else {
                helperFunctions.addClass(icon, "offline-status");
            }
        }
    }

    checkLoadStatus() {
        if (this.loadStatus['theme'] == true && this.loadStatus['main'] == true && this.loadStatus['font'] == true && this.loadStatus['widget'] == true && this.loadStatus['shidden'] == false) {
            var elm = this.cont.shadowRoot.getElementById('lhc_status_container');
            elm && (elm.style.display = "");
            this.attributes.sload.next(true);
        }
    }

    init(attributes, lload) {

        this.attributes = attributes;

        var placement = {top: "auto", left:"auto", bottom: (10+this.attributes.widgetDimesions.value.sbottom) + "px", right: (10+this.attributes.widgetDimesions.value.sright) + "px"};

        this.cont.setAttribute("vertical-space",(10+this.attributes.widgetDimesions.value.sbottom));
        this.cont.setAttribute("horizontal-space",(10+this.attributes.widgetDimesions.value.sright));

        if (attributes.position_placement == 'bottom_left' || attributes.position_placement == 'full_height_left') {
            placement = { right: "auto", top: "auto",bottom: (10+this.attributes.widgetDimesions.value.sbottom) + "px", left: (10+this.attributes.widgetDimesions.value.sright) + "px"};
        } else if (attributes.position_placement == 'middle_right') {
            placement = {left:"auto",top:"auto",bottom: "calc(50% - 45px)",right: (10+this.attributes.widgetDimesions.value.sright) + "px"};
            this.cont.setAttribute('vertical-unit','%');
        } else if (attributes.position_placement == 'middle_left') {
            placement = {right: "auto", top:"auto",bottom: "calc(50% - 45px)",left: (10+this.attributes.widgetDimesions.value.sright) + "px"};
            this.cont.setAttribute('vertical-unit','%');
        }

        this.cont.setAttribute("drag-enabled",attributes.drag_enabled);

        if (attributes.drag_enabled === true) {
            let positionPrevious = attributes.storageHandler.getSessionStorage(this.attributes['prefixStorage']+'_pos');

            if (positionPrevious !== null) {
                let placementRestored = JSON.parse(positionPrevious);
                placement["bottom"] = placementRestored["bottom"];
                placement["top"] = placementRestored["top"];
                placement["left"] = placementRestored["left"];
                placement["right"] = placementRestored["right"];
                placement["transform"] = "translateY("+placementRestored["y"]+"px)";
                attributes.status_position.next(placementRestored);
            }
        }

        this.cont.massRestyle(placement);

        this.cont.setContent('<div id="lhc_status_container" class="notranslate ' + (this.attributes.isMobile === true ? 'lhc-mobile' : 'lhc-desktop') + '" style="display: none;pointer-events: none;""><i style="display: none" title="New messages" id="unread-msg-number">!</i><a aria-label="Show or hide widget" href="#" tabindex="0" target="_blank" id="status-icon" class="offline-status"></a></div>');

        this.cont.className = this.attributes.isMobile === true ? 'notranslate lhc-mobile' : 'notranslate lhc-desktop';

        this.cont.attachEvents();

        var _inst = this;

        this.lload = !(!lload);

        // If it's lazy load we have always to consider widget as loaded
        if (this.lload === true) {
            this.loadStatus['widget'] = true;
        } else {
            // We wait untill widget content loads
            attributes.wloaded.subscribe((data) => { if (data){this.loadStatus['widget'] = true; this.checkLoadStatus()}});
        }

        attributes.shidden.subscribe((data) => {
            if (data) {
                const chatParams = this.attributes['userSession'].getSessionAttributes();
                if (!chatParams['id'] && this.attributes.widgetStatus.value != true) {
                    this.loadStatus['shidden'] = true;
                    this.hide();
                }
            } else {
                this.loadStatus['shidden'] = false;
                this.checkLoadStatus();
                this.show();
            }
        });

        this.cont.ee = function(event, data, e) {
            if (event == "click" && data.status == true) {
                attributes.onlineStatus.value === false && attributes.eventEmitter.emitEvent('offlineClickAction');

                if (attributes.onlineStatus.value === false && attributes.offline_redirect !== null){
                    document.location = attributes.offline_redirect;
                    e.preventDefault();
                } else {
                    if (_inst.controlMode == true) {
                        attributes.eventEmitter.emitEvent('closeWidget', [{'sender' : 'closeButton', 'mode' : 'control'}]);
                        e.preventDefault();
                    } else {
                        attributes.eventEmitter.emitEvent('showWidget', [{'event':e}]);
                        attributes.eventEmitter.emitEvent('clickAction');
                    }
                }
            } else if (event == "move_finish") {
                attributes.storageHandler.setSessionStorage(attributes['prefixStorage']+'_pos',JSON.stringify(data));
                attributes.status_position.next(data);
            }
        }

        if (this.attributes.staticJS['fontCSS']) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['fontCSS']});
        }

        if (this.attributes.staticJS['font_status']) {

            let d = document.getElementsByTagName("head")[0],
                k = document.createDocumentFragment(),
                e = helperFunctions.initElement(document, "style", {type: "text/css"}),
                f = document.createTextNode(`@font-face {
            font-family: 'MaterialIconsLHC';
            font-style: normal;
            font-weight: 400;
            src: url('`+this.attributes.staticJS['font_status'].replace('.woff2','.eot')+`');
            src: url('`+this.attributes.staticJS['font_status']+`') format('woff2'), url('`+this.attributes.staticJS['font_status'].replace('.woff2','.woff')+`') format('woff'), url('`+this.attributes.staticJS['font_status'].replace('.woff2','.ttf')+`') format('truetype');
            font-display: swap
            }`);
            k.appendChild(e);
            d.appendChild(k);
            e.styleSheet ? e.styleSheet.cssText = f.nodeValue : e.appendChild(f)

            helperFunctions.insertCssRemoteFile({onload: () => {this.loadStatus['font'] = true; this.checkLoadStatus()},"as":"font", rel:"preload", type: "font/woff", crossOrigin : "anonymous",  href : this.attributes.staticJS['font_status']});
        }

        if (this.attributes.theme) {
            this.loadStatus['theme'] = false;
            this.cont.insertCssRemoteFile({onload: ()=>{this.loadStatus['theme'] = true; this.checkLoadStatus()}, id: "lhc-theme-status", crossOrigin : "anonymous",  href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themestatus/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
        }

        this.cont.insertCssRemoteFile({onload: ()=>{this.loadStatus['main'] = true; this.checkLoadStatus()}, crossOrigin : "anonymous",  href : this.attributes.staticJS['status_css'] });

        if (this.attributes.staticJS['page_css']) {
            helperFunctions.insertCssRemoteFile({crossOrigin : "anonymous", id: "lhc-theme-page", href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themepage/' + this.attributes.theme + '?v=' + this.attributes.theme_v});
        }

        attributes.onlineStatus.subscribe((data) => this.toggleOfflineIcon(data));

        attributes.widgetStatus.subscribe((data) => {
            if (this.attributes.mode !== 'popup') {
                const chatParams = this.attributes['userSession'].getSessionAttributes();
                (data == true || (!this.attributes.leaveMessage && this.attributes.onlineStatus.value == false && !chatParams['id'])) ? this.hide() : this.show();
            }
        });

        this.attributes.mode === 'popup' && this.show();
        let unreadMessagesNumber = attributes.storageHandler.getSessionStorage(this.attributes['prefixStorage']+'_unr');

        attributes.eventEmitter.addListener('unread_message', (data) => {
            var unreadTotal = (data && data.otm);
            if (unreadTotal) {
                unreadTotal = parseInt(unreadTotal);
                unreadTotal += this.unread_counter;
            }
            this.attributes.unread_counter.next(unreadTotal);
            this.showUnreadIndicator(unreadTotal);
        });

        if (unreadMessagesNumber !== null) {
            attributes.eventEmitter.emitEvent('unread_message',[{otm:unreadMessagesNumber, init: true}]);
            if (unreadMessagesNumber !== null && !isNaN(unreadMessagesNumber)) {
                this.unread_counter = parseInt(unreadMessagesNumber);
            }
        }

        // Widget reload was called
        // We avoid cache by using timestamp because we do not call init call.
        // We also always insert themepage even if there is no css in it.
        attributes.eventEmitter.addListener('reloadWidget',() => {
            if (this.attributes.theme > 0) {
                this.cont.insertCssRemoteFile({crossOrigin : "anonymous", id: "lhc-theme-status", href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themestatus/' + this.attributes.theme + '?v=' + Date.now()}, true);
            }
            helperFunctions.insertCssRemoteFile({crossOrigin : "anonymous", id: "lhc-theme-page", href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themepage/' + this.attributes.theme + '?v=' + Date.now()});
        });
    }

    hide () {

        this.removeUnreadIndicator();

        if (this.attributes.clinst === true && this.attributes.isMobile == false) {
            const chatParams = this.attributes['userSession'].getSessionAttributes();
            if (this.attributes.leaveMessage == true || this.attributes.onlineStatus.value == true || chatParams['id']) {

                if (this.attributes['position'] != 'api' || (this.attributes['position'] == 'api' && this.attributes['hide_status'] !== true && ((chatParams['id'] && chatParams['hash']) || this.attributes.widgetStatus.value == true))) {
                    if (this.attributes['hide_status'] !== true || (chatParams['id'] && chatParams['hash'])) {
                        this.cont.show();
                    }
                }

                if (this.attributes['hide_status'] !== true || (chatParams['id'] && chatParams['hash']) || this.attributes.widgetStatus.value == true) {
                    if (this.attributes.widgetStatus.value == true){
                        this.controlMode = true;
                        var icon = this.cont.shadowRoot.getElementById("status-icon");
                        helperFunctions.addClass(icon, "close-status");
                    }
                    return ;
                }

            }
        }

        clearTimeout(this.showDelay);
        this.statusDelayProcessed = true;
        this.cont.hide();
    }

    showUnreadIndicator(number){
        var iconText = number || '!';
        var icon = this.cont.shadowRoot.getElementById("lhc_status_container");
        helperFunctions.addClass(icon, "has-uread-message");

        var iconValue = this.cont.shadowRoot.getElementById("unread-msg-number");
        if (iconValue) {
            iconValue.innerText = iconText;
        }

        if (this.attributes.storageHandler)
            this.attributes.storageHandler.setSessionStorage(this.attributes['prefixStorage']+'_unr',iconText);
    }

    removeUnreadIndicator() {
        var icon = this.cont.shadowRoot.getElementById("lhc_status_container");
        helperFunctions.removeClass(icon, "has-uread-message");
        if (this.attributes.storageHandler) {
            this.attributes.storageHandler.removeSessionStorage(this.attributes['prefixStorage']+'_unr');
        }
        this.attributes.eventEmitter.emitEvent('remove_unread_indicator', []);
        this.attributes.unread_counter.next(0);
        this.unread_counter = 0;
    }

    show () {

        if (this.attributes.hideOffline === false) {

            const chatParams = this.attributes['userSession'].getSessionAttributes();

            if (this.attributes.clinst === true && this.attributes.isMobile == false) {
                if (this.attributes.widgetStatus.value != true) {
                    var icon = this.cont.shadowRoot.getElementById("status-icon");
                    helperFunctions.removeClass(icon, "close-status");
                    this.controlMode = false;
                }
            }

            // show status icon only if we are not in api mode or chat is going now
            if (this.attributes['position'] != 'api' || (this.attributes['position'] == 'api' && this.attributes['hide_status'] !== true && chatParams['id'] && chatParams['hash'])) {

                clearTimeout(this.showDelay);
                
                const chatParams = this.attributes['userSession'].getSessionAttributes();

                this.showDelay = setTimeout(() => {
                    this.cont.show();
                    this.statusDelayProcessed = true;
                }, (this.statusDelayProcessed == true || (chatParams['id'] && chatParams['hash'])) ? 0 : this.attributes['status_delay']);

            } else if (this.attributes.clinst === true) {
                if (this.attributes.widgetStatus.value != true) {
                    this.cont.hide();
                }
            }

        } else {
            this.cont.hide();
        }
    }
}