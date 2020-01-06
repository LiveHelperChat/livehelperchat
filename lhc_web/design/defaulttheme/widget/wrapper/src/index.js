(function (global) {

    if (!global.LHC_API) {
        return;
    }

    global.$_LHC_Instance = null;
    global.$_LHC_Debug = false;
    global.$_LHC = global.$_LHC || {};

    (function (lhc) {

        lhc.loaded = false;
        lhc.connected = false;
        lhc.ready = false;
        lhc.version = 4;

        var init = () => {

            // Avoid multiple times execution
            if (lhc.ready === true) {
                return;
            }

            // we have found document body so we can continue
            if (document.body) {
                lhc.ready = true;
            }

            var BehaviorSubject = require('./util/monitoredVariable').monitoredVariable;
            var EventEmitter = require('wolfy87-eventemitter');
            const axios = require('axios');

            var statusWidget = require('./lib/widgets/statusWidget').statusWidget;
            var mainWidget = require('./lib/widgets/mainWidget').mainWidget;
            var mainWidgetPopup = require('./lib/widgets/mainWidgetPopup').mainWidgetPopup;
            var containerChat = require('./lib/widgets/containerChat').containerChat;
            var helperFunctions = require('./lib/helperFunctions').helperFunctions;
            var userSession =  require('./util/userSession').userSession;
            var storageHandler =  require('./util/storageHandler').storageHandler;
            var chatNotifications = require('./lib/chatNotifications').chatNotifications;
            var chatEventsHandler = require('./util/chatEventsHandler').chatEventsHandler;

            const isMobileItem = require('ismobilejs');

            var isMobile = isMobileItem.default(global.navigator.userAgent).any;

            LHC_API.args = LHC_API.args || {};
            var storageHandler = new storageHandler(global, LHC_API.args.domain || null);

            var referrer = (document.referrer) ? document.referrer.substr(document.referrer.indexOf('://')+1) : '';
            var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';

            storageHandler.setSessionReferer(referrer);

            referrer = referrer ? encodeURIComponent(referrer) : '';

            // Main attributes
            var attributesWidget = {
                viewHandler : null,
                mainWidget : new mainWidget(),
                popupWidget : new mainWidgetPopup(),
                chatNotifications : chatNotifications,
                jsVars :  new BehaviorSubject(true),
                onlineStatus :  new BehaviorSubject(true),
                widgetStatus : new BehaviorSubject((storageHandler.getSessionStorage('LHC_WS') === 'true' || (LHC_API.args.mode && LHC_API.args.mode == 'embed' || LHC_API.args.mode == 'popup'))),
                eventEmitter : new EventEmitter(),
                toggleSound : new BehaviorSubject( storageHandler.getSessionStorage('LHC_SOUND') === 'true' || storageHandler.getSessionStorage('LHC_SOUND') === null),
                hideOffline : false,
                isMobile : isMobile,
                fresh : LHC_API.args.fresh || false,
                widgetDimesions : new BehaviorSubject({width: (isMobile ? 100 : (LHC_API.args.wwidth || 350)), height: (isMobile ? 100 : (LHC_API.args.wheight || 520)), units : (isMobile ? '%' : 'px')}),
                popupDimesnions : {pheight: (LHC_API.args.pheight || 520), pwidth:(LHC_API.args.pwidth || 500)},
                leaveMessage : LHC_API.args.leaveamessage || null,
                department : LHC_API.args.department || [],
                theme : LHC_API.args.theme || null,
                domain: LHC_API.args.domain || null,
                position: LHC_API.args.position || 'bottom_right',
                base_url : LHC_API.args.lhc_base_url,
                mode: LHC_API.args.mode || 'widget',
                captcha : null,
                // Login Objects
                userSession : new userSession(),
                storageHandler : storageHandler,
                staticJS : {},
                loadcb : LHC_API.args.loadcb || null,
                LHCChatOptions : global.LHCChatOptions ? global.LHCChatOptions : {}
            };

            var chatEvents = new chatEventsHandler(attributesWidget);

            lhc.eventListener = attributesWidget.eventEmitter;

            attributesWidget.userSession.setSessionInformation(attributesWidget.storageHandler.getSessionInformation());
            attributesWidget.userSession.setSessionReferrer(storageHandler.getSessionReferrer());

            if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {

                var containerChatObj = new containerChat();

                if (attributesWidget.position != 'api') {
                    attributesWidget.viewHandler = new statusWidget();
                    containerChatObj.cont.elmDom.appendChild(attributesWidget.viewHandler.cont.constructUI(),!0);
                }

                if (attributesWidget.mode == 'widget') {
                    containerChatObj.cont.elmDom.appendChild(attributesWidget.mainWidget.cont.constructUI(),!0);
                }

            } else {
                document.getElementById('lhc_status_container_page').appendChild(attributesWidget.mainWidget.cont.constructUI());
            }

            axios.get(LHC_API.args.lhc_base_url + '/widgetrestapi/settings',{params : {
                    'vid' : attributesWidget.userSession.getVID(),
                    'tz' : helperFunctions.getTzOffset(),
                    'r' : referrer,
                    'l' : location,
                }}).then(function(data){

                if (!attributesWidget.leaveMessage && data.data.isOnline === false) {
                    return;
                }

                if (data.data.secure_cookie) {
                    attributesWidget.storageHandler.setSecureCookie(true);
                }

                if (data.data.domain) {
                    attributesWidget.storageHandler.setCookieDomain(data.data.domain);
                }

                if (data.data.static) {
                    attributesWidget.staticJS = data.data.static;
                }

                attributesWidget.captcha = {hash : data.data.hash, ts : data.data.hash_ts};

                attributesWidget.userSession.setVID(data.data.vid);

                // Store session
                attributesWidget.storageHandler.storeSessionInformation(attributesWidget.userSession.getSessionAttributes());

                attributesWidget.hideOffline = data.data.hideOffline;
                attributesWidget.onlineStatus.next(data.data.isOnline);

                if (data.data.theme) {
                    attributesWidget.theme = data.data.theme;
                }

                // Javascript custom variables init
                // Extensions can listen for these
                attributesWidget.jsVars.next(data.data.js_vars);

                // Monitor js vars if required
                if (data.data.js_vars.length > 0) {
                    attributesWidget.userSession.setupVarsMonitoring(data.data.js_vars);
                }

                // Init main widgets
                if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {
                    if (attributesWidget.position != 'api') {
                        attributesWidget.viewHandler.init(attributesWidget);
                    }
                }

                if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'embed') {
                    attributesWidget.mainWidget.init(attributesWidget);
                }

                // Show status widget
                if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {
                    containerChatObj.cont.show();
                }

                if (attributesWidget.loadcb) {
                    attributesWidget.loadcb(attributesWidget);
                }

                if (lhc.version != data.data.v) {
                    /*var iframe1 = document.createElement("iframe");
                    iframe1.style.display = "none";
                    iframe1.src = LHC_API.args.lhc_base_url + '/widgetrestapi/updatejs';
                    document.body.appendChild(iframe1);*/
                }

            });

            // Widget Hide event
            attributesWidget.eventEmitter.addListener('closeWidget',function () {
                attributesWidget.widgetStatus.next(false);
                chatEvents.sendChildEvent('closedWidget', [{'sender' : 'closeButton'}]);
            });

            // Toggle sound user
            attributesWidget.eventEmitter.addListener('toggleSound',function () {
                var newValue = !attributesWidget.toggleSound.value;
                attributesWidget.toggleSound.next(newValue);
            });


            // Clear chat cookies if there is any
            // Then popup finishes loading it calls this to clean up chat cookies. So visitor can start new chat.
            attributesWidget.eventEmitter.addListener('endChatCookies',function () {
                attributesWidget.userSession.setChatInformation({'id':null,'hash':null});
                attributesWidget.storageHandler.storeSessionInformation(attributesWidget.userSession.getSessionAttributes());
            });

            attributesWidget.eventEmitter.addListener('endChat',function () {

                if (attributesWidget.mode != 'popup') {
                    attributesWidget.widgetStatus.next(false);
                }

                attributesWidget.userSession.setChatInformation({'id':null,'hash':null});
                attributesWidget.storageHandler.storeSessionInformation(attributesWidget.userSession.getSessionAttributes());

                chatEvents.sendChildEvent('endedChat', [{'sender' : 'endButton'}]);

                if (attributesWidget.mode == 'embed') {
                    attributesWidget.eventEmitter.emitEvent('showWidget', [{'sender' : 'closeButton'}]);
                }

                if (attributesWidget.mode == 'popup') {
                    attributesWidget.popupWidget.freeup();
                }
            });

            // Widget show event
            attributesWidget.eventEmitter.addListener('showWidget',function () {

                attributesWidget.widgetStatus.next(true);

                if (attributesWidget.mode == 'popup') {
                    attributesWidget.popupWidget.init(attributesWidget);

                    if (attributesWidget.position != 'api') {
                        attributesWidget.viewHandler.removeUnreadIndicator();
                    }
                }

                chatEvents.sendChildEvent('shownWidget', [{'sender' : 'closeButton'}]);
            });

            // Popup open event
            attributesWidget.eventEmitter.addListener('openPopup',function () {

                chatEvents.sendChildEvent('endedChat', [{'sender' : 'endButton'}]);

                attributesWidget.popupWidget.init(attributesWidget);

                if (attributesWidget.position != 'api') {
                    attributesWidget.viewHandler.removeUnreadIndicator();
                }

                chatEvents.sendChildEvent('shownWidget', [{'sender' : 'closeButton'}]);

                attributesWidget.widgetStatus.next(false);
            });

            // Chat started event received
            // Store chat information if it's not popup mode.
            attributesWidget.eventEmitter.addListener('chatStarted',function (data, mode) {

                if (mode !== 'popup') {
                    attributesWidget.userSession.setChatInformation(data);
                }

                // Store information permanently
                if (attributesWidget.fresh === false && mode !== 'popup') {
                    attributesWidget.storageHandler.storeSessionInformation(attributesWidget.userSession.getSessionAttributes());
                }
            });

            // Subscribe event
            attributesWidget.eventEmitter.addListener('subscribeEvent',function (data) {
                attributesWidget.chatNotifications.setPublicKey(data.pk,attributesWidget.eventEmitter);
                attributesWidget.chatNotifications.sendNotification();
            });

            // User has subscribed to notifications
            // Send back child subscription information
            attributesWidget.eventEmitter.addListener('subcribedEvent',function (data){
                chatEvents.sendChildEvent('subcribedEvent', [data]);
            });

            // Track widget status changes
            attributesWidget.widgetStatus.subscribe((data) => {
                attributesWidget.storageHandler.setSessionStorage('LHC_WS',data);
                chatEvents.sendChildEvent('widgetStatus', [data]);
            });

            // Store sound settings
            attributesWidget.toggleSound.subscribe((data) => {
                attributesWidget.storageHandler.setSessionStorage('LHC_SOUND',data);
            });

            attributesWidget.onlineStatus.subscribe((data) => {
                chatEvents.sendChildEvent('onlineStatus', [data]);
            });
            
            attributesWidget.eventEmitter.addListener('screenshot',(data) => {
                helperFunctions.makeScreenshot(attributesWidget.staticJS['screenshot'],data);
            });

            attributesWidget.eventEmitter.addListener('location',(data) => {
                document.location = data;
            });
            
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.addEventListener('message', function(event) {
                    if (typeof event.data.lhc_ch !== 'undefined' && typeof event.data.lhc_cid !== 'undefined') {
                        attributesWidget.widgetStatus.next(true);
                        if ( attributesWidget.mode == 'popup') {
                            attributesWidget.userSession.setChatInformation({'id' : event.data.lhc_cid, 'hash' : event.data.lhc_ch });
                            attributesWidget.eventEmitter.emitEvent('unread_message');
                        } else {
                            chatEvents.sendChildEvent('shownWidget', [{'sender' : 'closeButton'}]);
                            chatEvents.sendChildEvent('reopenNotification', [{'id' : event.data.lhc_cid, 'hash' : event.data.lhc_ch }]);
                        }
                    }
                });
            }

            // Listed for post messages
            const handleMessages = (e) => {
                if (typeof e.data !== 'string' || e.data.indexOf('lhc::')) { return; }

                const parts = e.data.split('::');

                if (parts[1] == 'ready') {
                    chatEvents.sendReadyEvent(parts[2] == 'true');
                } else {
                     attributesWidget.eventEmitter.emitEvent(parts[1], JSON.parse(parts[2]));
                }
            };


            if ( window.addEventListener ) {
                window.addEventListener("message", handleMessages, false);
            } else if ( window.attachEvent ) {
                window.attachEvent("onmessage", handleMessages);
            } else if ( document.attachEvent ) {
                document.attachEvent("onmessage", handleMessages);
            };
        };

        const eventsHandler = require('./util/domEventsHandler').domEventsHandler;

        (init(), !lhc.ready) || (eventsHandler.listen(document, "DOMContentLoaded", function () {init();}, "domloaded"),
        eventsHandler.listen(document, "readystatechange", function () {
            ("complete" === document.readyState || "interactive" === document.readyState && document.body) && init();
        }, "domstatechange"),
        eventsHandler.listen(global, "load", function () {
            init();
        }, "windowload"));

    }).call(this, window.$_LHC);

})(window);