import {UIConstructorIframe} from '../UIConstructorIframe';
import {policyStore} from '../../util/policyStore';
import {helperFunctions} from '../helperFunctions';

export class needhelpWidget{
    constructor(prefix) {

        this.attributes = {};
        this.hidden = false;
        this.widgetOpen = false;
        this.invitationOpen = false;
        this.nhOpen = false;
        this.delayProcessed = false;

        this.cont = new UIConstructorIframe((prefix || 'lhc')+'_needhelp_widget_v2', helperFunctions.getAbstractStyle({
            zindex: "2147483639",
            width: "320px",
            height: "135px",
            position: "fixed",
            display: "none",
            transition: "transform"
        }),  {"role":"presentation","translate":"no"}, "iframe");

        this.cleanups = [];
        this.loadStatus = {main : false, theme: false, status: false};
    }
    
    checkLoadStatus() {
        if (this.loadStatus['theme'] == true && this.loadStatus['main'] == true && this.loadStatus['status'] == true) {
            this.cont.elmDomDoc.body.style.display = "";
            if (this.styleFailed) {
                this.styleFailed = false;
                this.launcher.setAttribute('aria-expanded', 'false');
            }
            if (this.focusOnOpen && this.nhOpen) {
                this.focusOnOpen = false;
                const content = this.cont.elmDomDoc.body;
                const firstControl = content.querySelector('button:not(:disabled), a[href], input:not(:disabled), [tabindex="0"]');
                (firstControl || content).focus();
            }
        }
    }
    
    init(attributes, settings) {

        this.attributes = attributes;
        this.onClick = !!settings.on_click;
        this.offline = !!settings.offline;

        var placement = {bottom: (70 + this.attributes.widgetDimesions.value.wbottom) +"px", right: (65+this.attributes.widgetDimesions.value.wright) + "px"};

        var leftPosition = false;

        if (attributes.position_placement == 'bottom_left' || attributes.position_placement == 'full_height_left') {
            placement = {bottom: (70 + this.attributes.widgetDimesions.value.wbottom) +"px", left: (65+this.attributes.widgetDimesions.value.wright) + "px"};
            leftPosition = true;
        } else if (attributes.position_placement == 'middle_left') {
            placement = {bottom: "calc(50% + 35px)", left: (65+this.attributes.widgetDimesions.value.wright) + "px"};
            leftPosition = true;
        } else if (attributes.position_placement == 'middle_right') {
            placement = {bottom: "calc(50% + 35px)", right: (65+this.attributes.widgetDimesions.value.wright) + "px"};
        }

        this.cont.massRestyle(placement);

        this.cont.tmpl = settings['html'].replace('{dev_type}',(this.attributes.isMobile === true ? 'lhc-mobile' : 'lhc-desktop'));
        this.cont.bodyId = 'need-help';
        
        this.cont.trustedHtmlPolicy = policyStore.get(this.attributes);
        if (this.cont.constructUIIframe('', this.attributes.staticJS['dir']) === null){
            return null;
        }
        
        // Content invisible untill media loads
        this.cont.elmDomDoc.body.style.display = "none";
        
        this.cont.elmDom.className += this.attributes.isMobile === true ? ' notranslate lhc-mobile' : ' notranslate lhc-desktop';

        const startButton = this.cont.getElementById('start-chat-btn');
        if (startButton) startButton.addEventListener('click', function (e) {
            attributes.eventEmitter.emitEvent('nhClicked', [{'event': e, 'sender' : 'closeButton'}]);
            attributes.eventEmitter.emitEvent('showWidget', [{'event': e}]);
        });

        var _that = this;

        const closeButton = this.cont.getElementById('close-need-help-btn');
        if (closeButton) closeButton.addEventListener('click', function (a) {
            attributes.eventEmitter.emitEvent('nhClosed', [{'sender' : 'closeButton'}]);
            a.stopPropagation();
            _that.hide(true);
            if (_that.onClick && a.detail === 0) {
                _that.launcher.focus();
            }
        });

        if (settings.dimensions) {
            if (leftPosition == true && settings.dimensions['right']) {
                settings.dimensions['left'] = settings.dimensions['right'];
                delete settings.dimensions['right'];
            }
            this.cont.massRestyle(settings.dimensions);
        }

        const onStyleError = (resource) => {
            if (this.onClick && this.cont.elmDom.isConnected) {
                this.loadStatus[resource] = false;
                // A broken theme must not turn the only chat launcher into a blank panel.
                this.styleFailed = true;
                this.hide();
                this.launcher.removeAttribute('aria-expanded');
            }
        };
        this.cont.insertCssRemoteFile({onerror: () => onStyleError('main'), onload: () => {this.loadStatus['main'] = true; this.checkLoadStatus()},crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_css']}, true);

        if (this.attributes.isMobile == true) {
            this.cont.insertCssRemoteFile({crossOrigin : "anonymous",  href : this.attributes.staticJS['widget_mobile_css']});
        }

        if (this.attributes.theme) {
            this.cont.insertCssRemoteFile({onerror: () => onStyleError('theme'), onload: () => {this.loadStatus['theme'] = true; this.checkLoadStatus()}, id : "lhc-theme-needhelp", crossOrigin : "anonymous",  href : this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themeneedhelp/' + this.attributes.theme + '?v=' + this.attributes.theme_v}, true);
        } else {
            this.loadStatus['theme'] = true;
            this.checkLoadStatus();
        }

        // Show need help only if status widget is loaded
        this.watch(attributes.sload, (data) => {if(data){this.loadStatus['status'] = true; this.checkLoadStatus()}});

        if (this.onClick) {
            this.initLauncher();
        }

        if (!settings['ap'] || this.onClick) {

            if (attributes.animate_nh) {
                this.cont.massRestyle({"transition-duration" : "800ms"});
            }

            this.listen('showInvitation', () => {
                this.invitationOpen = true;
                this.hide();
            });

            this.listen('chatStarted', () => {
                this.hide(true);
            });

            this.listen('hideInvitation', () => {
                this.invitationOpen = false;
                this.show();
            });

            this.listen('cancelInvitation', () => {
                this.invitationOpen = false;
                this.show();
            });

            this.watch(attributes.msgsnippet_status, (data) => {
                data == true && this.hide(true);
            });

            this.watch(attributes.shidden, (data) => {
                data ? this.hide(false) : this.show();
            });

            this.watch(attributes.status_position, (data) => {

                if (!data) {
                    return;
                }

                let placementRestored = {};
                placementRestored["bottom"] = data["bottom"];
                placementRestored["top"] = data["top"];
                placementRestored["left"] = data["left"];
                placementRestored["right"] = data["right"];
                placementRestored["transform"] = "translate3d("+ (data["left"] == "auto" ? -55 : 55) +"px," + (data["bottom"] == "auto" ? data["y"] + 70 : data["y"] - 60) + "px, 0px)!important";
                this.cont.massRestyle(placementRestored);
            });
        }

        const watchStatus = () => {
            this.delayProcessed = true;

            this.watch(attributes.widgetStatus, (data) => {
                data == true ? (this.widgetOpen = true,this.hide()) : (this.widgetOpen = false,this.show());
            });

            this.watch(attributes.onlineStatus, (data) => {
                if (data == false && !this.offline) {
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

        };

        if (this.onClick) {
            watchStatus();
        } else {
            this.startTimer = setTimeout(watchStatus, settings.delay + this.attributes['status_delay']);
        }

        this.listen('nhClose',() => {
            attributes.eventEmitter.emitEvent('nhClosed', [{'sender' : 'api'}]);
            this.hide(true);
        });
    }

    listen(name, listener) {
        const emitter = this.attributes.eventEmitter;
        emitter.addListener(name, listener);
        this.cleanups.push(() => emitter.removeListener(name, listener));
    }

    watch(subject, listener) {
        subject.subscribe(listener);
        this.cleanups.push(() => subject.unsubscribe(listener));
    }

    reloadTheme() {
        if (this.onClick) this.hide();
        if (this.attributes.theme) {
            this.cont.insertCssRemoteFile({id: 'lhc-theme-needhelp', crossOrigin: 'anonymous', href: this.attributes.LHC_API.args.lhc_base_url + '/widgetrestapi/themeneedhelp/' + this.attributes.theme + '?v=' + Date.now()});
        }
    }

    destroy() {
        clearTimeout(this.startTimer);
        this.hide();
        this.cleanups.forEach(cleanup => cleanup());
        this.cont.elmDom.remove();
    }

    initLauncher() {
        const attributes = this.attributes;
        const host = attributes.viewHandler.cont;
        const frame = this.cont.elmDom;
        this.launcher = host.shadowRoot.getElementById('status-icon');
        const originalRole = this.launcher.getAttribute('role');
        this.launcher.setAttribute('role', 'button');
        this.launcher.setAttribute('aria-expanded', 'false');
        frame.removeAttribute('role');
        frame.title = 'Need help';
        this.cont.elmDomDoc.body.tabIndex = -1;

        const activate = (event) => {
            if (event.key === ' ') {
                event.preventDefault();
                if (!event.repeat) this.launcher.click();
            }
        };
        this.launcher.addEventListener('keydown', activate);
        this.cleanups.push(() => {
            this.launcher.removeEventListener('keydown', activate);
            this.launcher.removeAttribute('aria-expanded');
            if (originalRole === null) this.launcher.removeAttribute('role');
            else this.launcher.setAttribute('role', originalRole);
        });

        // The default template uses a clickable div. Custom HTML can supply
        // a native button or link with the same id.
        const start = this.cont.getElementById('start-chat-btn');
        if (start && !start.matches('button, a[href], input')) {
            start.tabIndex = 0;
            start.setAttribute('role', 'button');
            start.addEventListener('keydown', (event) => {
                if (event.target === start && (event.key === 'Enter' || event.key === ' ')) {
                    event.preventDefault();
                    if (!event.repeat) start.click();
                }
            });
        }

        this.dismissOutside = (event) => {
            if (!event.composedPath().includes(host)) this.hide();
        };
        this.dismissEscape = (event) => {
            if (this.nhOpen && event.key === 'Escape' && !event.defaultPrevented) {
                event.preventDefault();
                this.hide();
                this.launcher.focus();
            }
        };
        this.cont.elmDomDoc.addEventListener('keydown', this.dismissEscape);
        this.listen('showWidget', () => this.hide());
        this.watch(attributes.unread_counter, (count) => {
            if (count) this.hide();
        });
    }

    canToggle() {
        const attributes = this.attributes;
        const popup = attributes.popupWidget.cont.elementReferrerPopup;
        return this.onClick && !this.styleFailed && !attributes.terminated && !attributes.hideOffline && !attributes.shidden.value &&
            (this.offline || attributes.onlineStatus.value) && !attributes.userSession.id &&
            !attributes.widgetStatus.value && !attributes.viewHandler.controlMode &&
            !(popup && !popup.closed) && !attributes.mainWidget.is_invitation &&
            !attributes.msgsnippet_status.value && !attributes.unread_counter.value &&
            !attributes.viewHandler.cont.shadowRoot.getElementById('lhc_status_container').classList.contains('has-uread-message');
    }

    toggle(event) {
        if (!this.canToggle()) return false;

        event.preventDefault();
        // Dragging cancels the launcher's native mousedown focus handling.
        if (event.detail > 0) this.launcher.blur();
        if (this.nhOpen) {
            this.hide();
        } else {
            this.focusOnOpen = event.detail === 0;
            this.show(true);
            this.checkLoadStatus();
        }
        return true;
    }

    hide (persistent) {

        if (!this.onClick && persistent === true) {
            this.attributes.userSession.hnh = Math.round(Date.now() / 1000);
            this.attributes.storageHandler.storeSessionInformation(this.attributes.userSession.getSessionAttributes());
            this.hidden = true;
        }

        this.cont.hide();

        if (this.nhOpen) {
            this.nhOpen = false;
            if (this.onClick) {
                this.focusOnOpen = false;
                this.launcher.setAttribute('aria-expanded', 'false');
                document.removeEventListener('pointerdown', this.dismissOutside, true);
                document.removeEventListener('keydown', this.dismissEscape);
            }
            this.attributes.eventEmitter.emitEvent('nhHide', []);
        }
    }

    show (byLauncher) {

        if ((this.onClick && !byLauncher) || this.delayProcessed == false || this.hidden == true || this.widgetOpen == true || (!this.onClick && this.invitationOpen) || (!this.offline && this.attributes.onlineStatus.value == false)) {
            return;
        }

        if (this.attributes.hideOffline !== false) {
            this.hide();
            return;
        }

        this.cont.show();
        if (!this.nhOpen) {
            this.nhOpen = true;
            if (this.onClick) {
                this.launcher.setAttribute('aria-expanded', 'true');
                document.addEventListener('pointerdown', this.dismissOutside, true);
                document.addEventListener('keydown', this.dismissEscape);
            }
            this.attributes.eventEmitter.emitEvent('nhShow', []);
        }
    }
}