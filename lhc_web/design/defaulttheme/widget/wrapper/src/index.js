(function (global) {

    var currentScript = document.currentScript || (function() {
        var scripts = document.getElementsByTagName('script');
        return scripts[scripts.length - 1];
    })();

    var scopeScript = currentScript.getAttribute('scope') || 'LHC';

    if (!global[scopeScript+'_API'] || /google|baidu|bing|msn|duckduckbot|teoma|slurp|yandex|Chrome-Lighthouse/i.test(navigator.userAgent)) {
        return;
    }

    global['$_'+scopeScript+'_Instance'] = null;
    global['$_'+scopeScript+'_Debug'] = false;
    global['$_'+scopeScript] = global['$_'+scopeScript] || {};

    (function (lhc, LHC_API) {

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

            if (!global.Promise) {
                global.Promise = require('promise');
            }

            var BehaviorSubject = require('./util/monitoredVariable').monitoredVariable;
            var EventEmitter = require('wolfy87-eventemitter');

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

            const prefixLowercase = scopeScript.toLowerCase();
            const prefixStorage = (prefixLowercase && LHC_API.args.scope_storage ? prefixLowercase : 'lhc');

            var storageHandler = new storageHandler(global, LHC_API.args.domain || null, prefixStorage);

            if (LHC_API.args.cookie_per_page) {
                storageHandler.setCookiePerPage(LHC_API.args.cookie_per_page);
            }

            var referrer = (document.referrer) ? document.referrer.substr(document.referrer.indexOf('://')+1) : '';
            var location  = (document.location) ? encodeURIComponent(window.location.href.substring(window.location.protocol.length)) : '';

            storageHandler.setSessionReferer(referrer);

            referrer = referrer ? encodeURIComponent(referrer) : '';

            if (LHC_API.args.lang) {
                LHC_API.args.lang = LHC_API.args.lang.replace('/','') + '/';
            }

            // Main attributes
            var attributesWidget = {
                prefixLowercase : prefixLowercase,
                prefixStorage : prefixStorage,
                prefixScope : scopeScript,
                LHC_API : LHC_API,
                viewHandler : null,
                mainWidget : new mainWidget(prefixLowercase),
                popupWidget : new mainWidgetPopup(),
                chatNotifications : chatNotifications,
                jsVars :  new BehaviorSubject(true),
                onlineStatus :  new BehaviorSubject(true),
                wloaded :  new BehaviorSubject(false),
                sload :  new BehaviorSubject(false),
                widgetStatus : new BehaviorSubject((storageHandler.getSessionStorage(prefixStorage+'_ws') === 'true' || (LHC_API.args.mode && LHC_API.args.mode == 'embed'))),
                eventEmitter : new EventEmitter(),
                toggleSound : new BehaviorSubject(storageHandler.getSessionStorage(prefixStorage+'_sound') === 'true',{'ignore_sub':true}),
                hideOffline : false,
                fscreen : LHC_API.args.fscreen || false,
                isMobile : isMobile,
                isIE : (navigator.userAgent.toUpperCase().indexOf("TRIDENT/") != -1 || navigator.userAgent.toUpperCase().indexOf("MSIE") != -1),
                fresh : LHC_API.args.fresh || false,
                popupDimesnions : {pheight: (LHC_API.args.pheight || 520), pwidth:(LHC_API.args.pwidth || 500)},
                leaveMessage : LHC_API.args.leaveamessage || null,
                department : LHC_API.args.department || [],
                product : LHC_API.args.product || [],
                theme : LHC_API.args.theme || null,
                theme_v : null,
                domain: LHC_API.args.domain || null,
                domain_lhc: null,
                position: LHC_API.args.position || 'bottom_right',
                position_placement:  LHC_API.args.position_placement || 'bottom_right',
                base_url : LHC_API.args.lhc_base_url,
                mode: LHC_API.args.mode || 'widget',
                tag: LHC_API.args.tag || '',
                proactive: {},
                captcha : null,
                focused : true,
                clinst : false,
                offline_redirect : LHC_API.args.offline_redirect || null,
                identifier : LHC_API.args.identifier || '',
                proactive_interval : null,
                lang : LHC_API.args.lang || '',
                bot_id : LHC_API.args.bot_id || '',
                priority : LHC_API.args.priority || null,
                events : LHC_API.args.events || [],
                hhtml : LHC_API.args.hhtml || '',
                survey : LHC_API.args.survey || null,
                operator : LHC_API.args.operator || null,
                phash : LHC_API.args.phash || null,
                pvhash : LHC_API.args.pvhash || null,
                // Login Objects
                userSession : new userSession(),
                storageHandler : storageHandler,
                staticJS : {},
                init_calls : [],
                childCommands : [],
                childExtCommands : [],
                lhc_var : LHC_API.args.lhc_var || lhc_var || null,
                loadcb : LHC_API.args.loadcb || null,
                LHCChatOptions : global[scopeScript + 'ChatOptions'] || {}
            };

            attributesWidget.widgetDimesions = new BehaviorSubject({sright:(LHC_API.args.sright || 0), sbottom:(LHC_API.args.sbottom || 0), wright_inv: 0, wbottom:0, wright:0, width: ((isMobile || attributesWidget.fscreen) ? 100 : (LHC_API.args.wwidth || 350)), height: ((isMobile || attributesWidget.fscreen) ? 100 : (LHC_API.args.wheight || 520)), units : ((isMobile|| attributesWidget.fscreen) ? '%' : 'px')});

            var chatEvents = new chatEventsHandler(attributesWidget);

            lhc.eventListener = attributesWidget.eventEmitter;
            lhc.attributes = attributesWidget;

            attributesWidget.userSession.setAttributes(attributesWidget);
            attributesWidget.userSession.setSessionInformation(attributesWidget.storageHandler.getSessionInformation());
            attributesWidget.userSession.setSessionReferrer(storageHandler.getSessionReferrer());

            if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {

                var containerChatObj = new containerChat(attributesWidget.prefixLowercase);

                attributesWidget.viewHandler = new statusWidget(attributesWidget.prefixLowercase);
                containerChatObj.cont.elmDom.appendChild(attributesWidget.viewHandler.cont.constructUI(),!0);

                if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {
                    containerChatObj.cont.elmDom.appendChild(attributesWidget.mainWidget.cont.constructUI(),!0);
                }

            } else {
                var embedWrapper = document.getElementById(attributesWidget.prefixLowercase + '_status_container_page');
                if (embedWrapper !== null) {
                    embedWrapper.appendChild(attributesWidget.mainWidget.cont.constructUI());
                    embedWrapper.style.height = (LHC_API.args.wheight || 520)+'px';
                } else {
                    attributesWidget.position = 'api';
                }
            }

            helperFunctions.makeRequest(LHC_API.args.lhc_base_url+ attributesWidget.lang + 'widgetrestapi/settings',{params:{
                'vid' : (LHC_API.args.UUID || attributesWidget.userSession.getVID()),
                'hnh': attributesWidget.userSession.hnh,
                'tz' : helperFunctions.getTzOffset(),
                'r' : referrer,
                'l' : location,
                'dt' : encodeURIComponent(document.title),
                'ie' : attributesWidget.isIE,
                'dep' : attributesWidget.department.join(','),
                'idnt' : attributesWidget.identifier,
                'tag' : attributesWidget.tag,
                'theme' : attributesWidget.theme
            }}, (data) => {

                if (data.terminate || ((!attributesWidget.leaveMessage && data.chat_ui.leaveamessage === false) && data.isOnline === false)) {

                    if (LHC_API.args.offline_redirect && attributesWidget.mode == 'embed') {
                        document.location = LHC_API.args.offline_redirect;
                    }

                    if (data.terminate) {
                        return;
                    }
                }

                attributesWidget.leaveMessage = attributesWidget.leaveMessage || data.chat_ui.leaveamessage;

                if (data.department) {
                    attributesWidget.department = data.department;
                }

                __webpack_public_path__ = data.chunks_location + "/";

                if (data.secure_cookie) {
                    attributesWidget.storageHandler.setSecureCookie(true);
                }

                if (data.domain) {
                    attributesWidget.storageHandler.setCookieDomain(data.domain);
                }

                if (data.siteaccess) {
                    attributesWidget.lang = data.siteaccess;
                }

                if (data.static) {
                    attributesWidget.staticJS = data.static;
                }

                if (data.pdim) {
                    attributesWidget.popupDimesnions = data.pdim;
                }

                if (data.survey_id) {
                    attributesWidget.survey = data.survey_id;
                }

                if (data.domain_lhc) {
                    attributesWidget.domain_lhc = data.domain_lhc;
                }

                if (data.cont_css) {
                    attributesWidget.cont_ss = data.cont_css;
                }
                
                if (data.wposition) {
                    attributesWidget.position_placement = data.wposition;
                }

                attributesWidget.captcha = {hash : data.hash, ts : data.hash_ts};

                attributesWidget.userSession.setVID(data.vid);

                // Store session
                attributesWidget.storageHandler.storeSessionInformation(attributesWidget.userSession.getSessionAttributes());

                attributesWidget.hideOffline = data.hideOffline;
                attributesWidget.onlineStatus.next(data.isOnline);

                if (data.theme) {
                    attributesWidget.theme = data.theme;
                    attributesWidget.theme_v = data.theme_v;
                }
                
                if (data.chat_ui) {

                    if ((data.chat_ui.fscreen && attributesWidget.mode == 'embed') || attributesWidget.fscreen) {
                        attributesWidget.widgetDimesions.nextProperty('width',100);
                        attributesWidget.widgetDimesions.nextProperty('height',100);
                        attributesWidget.widgetDimesions.nextProperty('units','%');
                        attributesWidget.fscreen = isMobile = attributesWidget.isMobile = true;
                    }

                    if (data.chat_ui.wheight && !isMobile) {
                        attributesWidget.widgetDimesions.nextProperty('height',data.chat_ui.wheight);
                    }

                    if (data.chat_ui.wwidth && !isMobile) {
                        attributesWidget.widgetDimesions.nextProperty('width',data.chat_ui.wwidth);
                    }

                    if (data.chat_ui.hhtml) {
                        attributesWidget.hhtml = data.chat_ui.hhtml;
                    }

                    if (data.chat_ui.clinst) {
                        attributesWidget.clinst = true;
                    }

                    if (data.chat_ui.wbottom && !isMobile) {
                         attributesWidget.widgetDimesions.nextProperty('wbottom',data.chat_ui.wbottom);
                    }

                    if (data.chat_ui.sbottom) {
                         attributesWidget.widgetDimesions.nextProperty('sbottom',data.chat_ui.sbottom);
                    }

                    if (data.chat_ui.sright) {
                         attributesWidget.widgetDimesions.nextProperty('sright',data.chat_ui.sright);
                    }

                    if (data.chat_ui.wright && !isMobile) {
                         attributesWidget.widgetDimesions.nextProperty('wright',data.chat_ui.wright);
                    }
                    
                    if (data.chat_ui.wright_inv && !isMobile) {
                         attributesWidget.widgetDimesions.nextProperty('wright_inv',data.chat_ui.wright_inv);
                    }

                    if (data.chat_ui.mobile_popup && isMobile) {
                        attributesWidget.mode = 'popup';
                    }

                    if (data.chat_ui.sound_enabled && storageHandler.getSessionStorage(prefixStorage+'_sound') === null) {
                        attributesWidget.toggleSound.next(true);
                    }

                    if (data.chat_ui.check_status) {
                        import('./util/activityMonitoring').then((module) => {
                            module.activityMonitoring.setParams({
                                'timeout' : data.chat_ui.check_status,
                                'track_mouse' : data.chat_ui.track_mouse,
                                'track_activity' : data.chat_ui.track_activity
                            }, attributesWidget);
                        });
                    }

                    if (data.ga) {
                        import('./util/analyticEvents').then((module) => {
                            module.analyticEvents.setParams({
                                'ga' : data.ga
                            }, attributesWidget);
                        });
                    }

                }

                if (attributesWidget.mode == 'widget' && data.nh && attributesWidget.fresh === false && attributesWidget['position'] != 'api' && attributesWidget.userSession.id === null) {
                    import('./lib/widgets/needhelpWidget').then((module) => {
                        var needhelpWidget = new module.needhelpWidget(attributesWidget.prefixLowercase);
                        containerChatObj.cont.elmDom.appendChild(needhelpWidget.cont.constructUI(),!0);
                        needhelpWidget.init(attributesWidget,data.nh);
                    });
                }

                if (data.js_vars) {
                    // Javascript custom variables init
                    // Extensions can listen for these
                    attributesWidget.jsVars.next(data.js_vars);

                    // Monitor js vars if required
                    if (data.js_vars.length > 0) {
                        attributesWidget.userSession.setupVarsMonitoring(data.js_vars, (vars) => {
                            chatEvents.sendChildEvent('jsVars', [vars]);
                        });
                    }
                }

                // Init main widgets
                if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {
                        attributesWidget.viewHandler.init(attributesWidget, data.ll);
                }

                if (!(attributesWidget.position == 'api' && attributesWidget.mode == 'embed')) {
                    attributesWidget.mainWidget.init(attributesWidget, data.ll);
                }

                // Show status widget
                if (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') {
                    containerChatObj.cont.show();
                }

                if (attributesWidget.loadcb) {
                    attributesWidget.loadcb(attributesWidget);
                }

                if (data.init_calls) {
                    attributesWidget.init_calls = data.init_calls;
                }

                attributesWidget.proactive_interval = data.chat_ui.proactive_interval;

                if ( (attributesWidget.mode == 'widget' || attributesWidget.mode == 'popup') && (typeof LHC_API.args.proactive === 'undefined' || LHC_API.args.proactive === true) && attributesWidget.storageHandler.getSessionStorage(prefixStorage+'_invt') === null) {
                    import('./util/proactiveChat').then((module) => {
                        module.proactiveChat.setParams({
                            'interval' : attributesWidget.proactive_interval
                        }, attributesWidget, chatEvents);
                    });
                }

                if (attributesWidget.init_calls.length > 0) {
                    attributesWidget.init_calls.forEach((item) => {
                        if (item.extension == 'nodeJSChat') {
                            import('./util/nodeJSChat').then((module) => {
                                module.nodeJSChat.setParams(item.params, attributesWidget, chatEvents);
                            });
                        }
                    });
                }

            })

            // Widget Hide event
            attributesWidget.eventEmitter.addListener('closeWidget',function () {
                attributesWidget.widgetStatus.next(false);
                chatEvents.sendChildEvent('closedWidget', [{'sender' : 'closeButton'}]);
            });

            // Send event to the child instantly
            attributesWidget.eventEmitter.addListener('sendChildEvent',function (params) {
                if (typeof params['boot'] !== 'undefined') {
                    attributesWidget.mainWidget.bootstrap();
                } else {
                    if (attributesWidget.mainWidget.isLoaded == true && lhc.loaded == true) {
                        chatEvents.sendChildEvent(params['cmd'], [params['arg']]);
                    } else {
                        attributesWidget.childCommands.push(params);
                    }
                }
            });

            // Send smart event to the child
            attributesWidget.eventEmitter.addListener('sendChildExtEvent',function (params) {
                if (typeof params['boot'] !== 'undefined') {
                    attributesWidget.mainWidget.bootstrap();
                } else {
                    if (attributesWidget.mainWidget.isLoaded == true && lhc.loaded == true) {
                        chatEvents.sendChildEvent(params['cmd'], [params['arg']], 'lhc_load_ext');
                    } else {
                        attributesWidget.childExtCommands.push(params);
                    }
                }
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
                attributesWidget.proactive = {};
            });

            attributesWidget.eventEmitter.addListener('endChat',function (params) {

                attributesWidget.userSession.setChatInformation({'id':null,'hash':null});
                attributesWidget.storageHandler.storeSessionInformation(attributesWidget.userSession.getSessionAttributes());

                attributesWidget.proactive = {};

                if (attributesWidget.mode != 'popup' && !params['show_start']) {
                    attributesWidget.widgetStatus.next(false);
                }

                attributesWidget.widgetDimesions.nextProperty('height_override',null);

                chatEvents.sendChildEvent('endedChat', [{'sender' : 'endButton'}]);

                if (attributesWidget.mode == 'embed' || params['show_start']) {
                    attributesWidget.eventEmitter.emitEvent('showWidget', [{'sender' : 'closeButton'}]);
                }

                if (attributesWidget.mode == 'popup') {
                    attributesWidget.popupWidget.freeup();
                }
            });

            // Widget show event
            attributesWidget.eventEmitter.addListener('showWidget',function () {

                // Just to restyle if needed
                attributesWidget.mainWidget.hideInvitation();

                attributesWidget.widgetStatus.next(true);

                if (attributesWidget.mode == 'popup') {
                    attributesWidget.popupWidget.init(attributesWidget);

                    attributesWidget.viewHandler.removeUnreadIndicator();

                    attributesWidget.mainWidget.hide();
                }

                chatEvents.sendChildEvent('shownWidget', [{'sender' : 'closeButton'}]);
            });

            // Add tag listener
            attributesWidget.eventEmitter.addListener('addTag',function (tag) {
                attributesWidget.tag = attributesWidget.tag != '' ? attributesWidget.tag + ',' + tag : tag;
                attributesWidget.eventEmitter.emitEvent('tagAdded');
            });

            // Events
            attributesWidget.eventEmitter.addListener('addEvent',function (events) {
                attributesWidget.events = events ;
                attributesWidget.eventEmitter.emitEvent('eventAdded');
            });

            // Popup open event
            attributesWidget.eventEmitter.addListener('openPopup',function () {

                chatEvents.sendChildEvent('endedChat', [{'sender' : 'endButton'}]);

                attributesWidget.popupWidget.init(attributesWidget);

                attributesWidget.viewHandler.removeUnreadIndicator();

                chatEvents.sendChildEvent('shownWidget', [{'sender' : 'closeButton'}]);

                attributesWidget.widgetStatus.next(false);
            });

            // Chat started event received
            // Store chat information if it's not popup mode.
            attributesWidget.eventEmitter.addListener('chatStarted',function (data, mode) {

                attributesWidget.widgetDimesions.nextProperty('height_override',null);

                if (mode !== 'popup') {
                    attributesWidget.userSession.setChatInformation(data);
                } else if (mode == 'popup') {
                    attributesWidget.mainWidget.hide();
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
                if (attributesWidget.mode !== 'popup') {
                    attributesWidget.storageHandler.setSessionStorage(prefixStorage+'_ws',data);
                    chatEvents.sendChildEvent('widgetStatus', [data]);
                }
            });

            // Store sound settings
            attributesWidget.toggleSound.subscribe((data) => {
                attributesWidget.storageHandler.setSessionStorage(prefixStorage+'_sound',data);
            });

            attributesWidget.onlineStatus.subscribe((data) => {
                chatEvents.sendChildEvent('onlineStatus', [data]);
            });
            
            attributesWidget.eventEmitter.addListener('screenshot',(data) => {
                helperFunctions.makeScreenshot(attributesWidget.staticJS['screenshot'],data);
            });

            attributesWidget.eventEmitter.addListener('screenshare',(data) => {
                import('./util/screenShare').then((module) => {
                    module.screenShare.setParams((data || {}), attributesWidget, chatEvents);
                });
            });

            attributesWidget.eventEmitter.addListener('location',(data) => {
                document.location = data;
            });

            attributesWidget.eventEmitter.addListener('showInvitation',(data) => {
                attributesWidget.widgetDimesions.nextProperty('bottom_override',75);
                attributesWidget.widgetDimesions.nextProperty('right_override',75);
                attributesWidget.mainWidget.showInvitation();
            });

            attributesWidget.eventEmitter.addListener('hideInvitation',(data) => {
                attributesWidget.mainWidget.hideInvitation();
                if (data.full) {
                    attributesWidget.eventEmitter.emitEvent('showWidget', [{'sender' : 'closeButton'}]);
                    attributesWidget.eventEmitter.emitEvent('fullInvitation', [data]);
                } else {
                    attributesWidget.eventEmitter.emitEvent('cancelInvitation', []);
                }
            });

            attributesWidget.originalTitle = document.title;
            attributesWidget.blinkInterval = null;

            attributesWidget.eventEmitter.addListener('unread_message_title',(data) => {
                clearInterval(attributesWidget.blinkInterval);
                if (data.status == false) {
                    attributesWidget.blinkInterval = setInterval(() => {
                        document.title = (Math.round(new Date().getTime() / 1000) % 2) ? '💬 ' + attributesWidget.originalTitle : attributesWidget.originalTitle;
                    },1000);
                } else {
                    attributesWidget.focused = true;
                    document.title = attributesWidget.originalTitle;
                }
            });

            attributesWidget.eventEmitter.addListener('widgetHeight',(data) => {

                if (data.reset_height) {
                    attributesWidget.widgetDimesions.nextProperty('height_override',null);
                    attributesWidget.widgetDimesions.nextProperty('bottom_override',null);
                    attributesWidget.widgetDimesions.nextProperty('right_override',null);
                    attributesWidget.widgetDimesions.nextProperty('width_override',null);
                    return;
                }

                if (data.force_height || data.force_width) {
                    data.force_height && attributesWidget.widgetDimesions.nextProperty('height_override',data.force_height);
                    data.force_width && attributesWidget.widgetDimesions.nextProperty('width_override',data.force_width);
                    return;
                }

                if (attributesWidget.mode == 'widget' && attributesWidget.isMobile == false) {
                        var d=document,
                        e=d.documentElement,
                        g=d.getElementsByTagName('body')[0],
                        y=global.innerHeight||e.clientHeight||g.clientHeight;
                    if (parseInt(data.height) > attributesWidget.widgetDimesions.value['height'] && y > parseInt(data.height)) {
                        attributesWidget.widgetDimesions.nextProperty('height_override',parseInt(data.height));
                    } else if (attributesWidget.widgetDimesions.value['height_override'] && attributesWidget.widgetDimesions.value['height_override'] > y) {
                        attributesWidget.widgetDimesions.nextProperty('height_override',null);
                    }
                }
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
                if (typeof e.data !== 'string' || e.data.indexOf(attributesWidget.prefixLowercase +'::')) { return; }

                const parts = e.data.split('::');

                if (typeof e.origin !== 'undefined') {
                    var originDomain = e.origin.replace("http://", "").replace("https://", "").replace(/:(\d+)$/,'');

                    // We allow to send events only from chat installation or page where script is embeded.
                    if (originDomain !== document.domain && attributesWidget.domain_lhc !== originDomain) {
                        return;
                    }
                }

                if (parts[1] == 'ready') {
                    chatEvents.sendReadyEvent(parts[2] == 'true');

                    if (attributesWidget.storageHandler.getSessionStorage(prefixStorage+'_screenshare')){
                        attributesWidget.eventEmitter.emitEvent('screenshare',[{'auto_start' : true}]);
                    }

                    const focusChangeCb = (e) => {
                        const focused = e.type === "focus";
                        attributesWidget.focused = focused;
                        chatEvents.sendChildEvent('focus_changed', [{'status' : focused}]);
                    };

                    window.addEventListener('focus',focusChangeCb);
                    window.addEventListener('blur',focusChangeCb);
                    window.addEventListener('pageshow',focusChangeCb);
                    window.addEventListener('pagehide',focusChangeCb);

                    // App is fully loaded
                    lhc.loaded = true;

                    chatEvents.sendChildEvent('ext_modules', [attributesWidget.staticJS['ex_cb_js']]);
                    
                    // send child commands if there is any
                    attributesWidget.childExtCommands.forEach((params) => {
                        chatEvents.sendChildEvent(params['cmd'], [params['arg']], 'lhc_load_ext');
                    });

                    // send child commands if there is any
                    attributesWidget.childCommands.forEach((params) => {
                        chatEvents.sendChildEvent(params['cmd'], [params['arg']]);
                    });

                } else if (parts[1] == 'ready_popup') {
                    attributesWidget.popupWidget.sendParameters(chatEvents);
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

    }).call(this, global['$_'+scopeScript], global[scopeScript+'_API']);

})(window);