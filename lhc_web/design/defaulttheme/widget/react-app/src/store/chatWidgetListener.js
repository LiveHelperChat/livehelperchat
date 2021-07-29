import { endChat, initChatUI, pageUnload, storeSubscriber, initProactive, checkChatStatus, fetchMessages, addMessage } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";
import i18n from "../i18n";

export default function (dispatch, getState) {

    // Holds extensions
    let extensions = {};
    let readyReceived = false;

    function insertJS(extension, src, args) {
        if (document.getElementById('ext-' + extension) === null) {
            var th = document.getElementsByTagName('head')[0];
            var s = document.createElement('script');
            s.setAttribute('type','text/javascript');
            s.setAttribute('src',src);
            s.setAttribute('id','ext-' + extension);
            th.appendChild(s);
            s.onreadystatechange = s.onload = function() {
                helperFunctions.emitEvent(extension + '.init', args);
            };
        } else {
            helperFunctions.emitEvent(extension + '.init', args);
        }
    }

    function executeExtension(extension, args) {
        if (Array.isArray(args)) {
            args.push(dispatch);
            args.push(getState);
        }

        if (typeof extensions[extension] !== 'undefined') {
            insertJS(extension, extensions[extension], args);
        } else if (extension == 'modal_ext') {
            var date = new Date();
            insertJS(extension, __webpack_public_path__.replace('/widgetv2/','') + '/modal.ext.min.js?'+(""+date.getFullYear() + date.getMonth() + date.getDate()), args);
        } else {

            var url = "/(ext)/" + extension;

            const state = getState();

            if (state.chatwidget.hasIn(['chatData', 'id'])) {
                url += "/(id)/" + state.chatwidget.getIn(['chatData', 'id']);
                url += "/(hash)/" + state.chatwidget.getIn(['chatData', 'hash']);
            }

            var dep = state.chatwidget.get('department').join("/");

            if (dep != "") {
                url += "/(dep)/"+dep;
            }

            var date = new Date();

            insertJS(extension, window.lhcChat['base_url'] + "widgetrestapi/executejs" + url + ("?" + date.getFullYear() + date.getMonth() + date.getDate()), args);
        }
    }

    const events = [
        {id : 'closedWidget', cb : (data) => {dispatch({type: 'closedWidget', data: data})}},
        {id : 'endedChat', cb : (data) => {
            dispatch({type: 'endedChat', data: data});
            if (window.lhcChat['mode'] == 'popup') {
                window.close();
            }
        }},
        {id : 'endCookies', cb : (data) => {
                helperFunctions.sendMessageParent('endChatCookies', [{force: true}]);
                if (window.lhcChat['mode'] == 'popup') {
                    helperFunctions.removeSessionStorage('_chat');
                }
        }},
        {id : 'reopenNotification', cb : (data) => {dispatch({type: 'CHAT_ALREADY_STARTED', data: {'id' : data.id, 'hash' : data.hash}})}},
        {id : 'subcribedEvent', cb : (e) => {dispatch(storeSubscriber(e.payload))}},
        {id : 'dispatch_direct', cb : (data) => {dispatch({type: data.type, data : data.data})}},
        {id : 'attr_set', cb : (data) => {dispatch({type: 'attr_set', attr : data.attr, data : data.data})}},
        {id : 'attr_rem', cb : (data) => {dispatch({type: 'attr_rem', attr : data.attr})}},
        {id : 'dispatch_event', cb : (data) => {

                const state = getState();

                let attributesCall = {};

                data.attr && Object.keys(data.attr).forEach(key => {
                    attributesCall[key] = state.chatwidget.getIn(data.attr[key]);
                })

                data.attr_params && Object.keys(data.attr_params).forEach(key => {
                    attributesCall[key] = data.attr_params[key];
                })

                const operations = {fetchMessages, addMessage};

                dispatch(operations[data.func](attributesCall));
        }},
        {id : 'onlineStatus',cb : (data) => {dispatch({type: 'onlineStatus', data: data})}},
        {id : 'toggleSound',cb : (data) => {dispatch({type: 'toggleSound', data: data})}},
        {id : 'widgetStatus',cb : (data) => {dispatch({type: 'widgetStatus', data: data})}},
        {id : 'jsVars',cb : (data) => {dispatch({type: 'jsVars', data: data})}},
        {id : 'ext_modules',cb : (data) => {
                extensions = data;
        }},
        {id : 'extensionExecute',cb : (extension, args) => {
                executeExtension(extension, args);
        }},
        {id : 'chat_check_messages', cb : () => {
                const state = getState();
                if (state.chatwidget.hasIn(['chatData','id'])){
                    dispatch(fetchMessages({
                        'chat_id': state.chatwidget.getIn(['chatData','id']),
                        'hash' : state.chatwidget.getIn(['chatData','hash']),
                        'lmgsid' : state.chatwidget.getIn(['chatLiveData','lmsgid']),
                        'theme' : state.chatwidget.get('theme')
                    }));
                }
        }},
        {id : 'chat_check_status',cb : () => {
                const state = getState();
                if (state.chatwidget.hasIn(['chatData','id'])){
                    dispatch(checkChatStatus({
                        'chat_id': state.chatwidget.getIn(['chatData','id']),
                        'hash' : state.chatwidget.getIn(['chatData','hash']),
                        'mode' : state.chatwidget.get('mode'),
                        'theme' : state.chatwidget.get('theme')
                    }));
                }
        }},
        {id : 'proactive', cb : (data) => {
            setTimeout(() => {
                dispatch(initProactive(data))
            }, readyReceived === true ? 0 : 700);
        }},
        {id : 'focus_changed', cb : (data) => {
                var newValue = data.status || document.hasFocus();
                if (newValue != window.lhcChat['is_focused']){
                    window.lhcChat['is_focused'] = newValue;
                    if (newValue == true) {
                        helperFunctions.sendMessageParent('unread_message_title',[{'status':true}]);
                    }
                }
        }}
    ];

    // Event listeners
    events.forEach((evt) => {
       helperFunctions.eventEmitter.addListener(evt.id, evt.cb);
    });

    function handleParentMessage(e) {

        if (typeof e.data !== 'string') { return; }

        var action = e.data.split(':')[0];

        if (typeof e.origin !== 'undefined') {
            
            var originDomain = e.origin.replace("http://", "").replace("https://", "").replace(/:(\d+)$/,'');

            // We allow to send events only from chat installation or page where script is embeded.
            if (originDomain !== document.domain && (typeof window.lhcChat['domain_lhc'] === 'undefined' || window.lhcChat['domain_lhc'] !== originDomain)) {
                // Third party domains can send only these two events
                if (action != 'lhc_chat_closed_explicit' && action != 'lhc_survey_completed') {
                    return;
                }
            }
        }

        if (action == 'lhc_chat_closed_explicit') {
            const state = getState();

            if (state.chatwidget.hasIn(['chatData','hash']) && state.chatwidget.hasIn(['chatData','id'])) {
                dispatch(
                    endChat({
                        'vid' : state.chatwidget.get('vid'),
                        'chat': {
                            id : state.chatwidget.getIn(['chatData','id']),
                            hash : state.chatwidget.getIn(['chatData','hash'])
                        }
                    }));
            } else {
                if (state.chatwidget.get('mode') == 'popup') {
                    helperFunctions.sendMessageParent('endChat', [{'sender' : 'endButton'}]);
                    window.close();
                } else {
                    helperFunctions.sendMessageParent('closeWidget', [{'sender' : 'closeButton'}]);
                }
            }

        } else if (action == 'lhc_survey_completed') {
            const state = getState();
            dispatch(
                endChat({
                'vid' : state.chatwidget.get('vid'),
                'chat': {
                    id : state.chatwidget.getIn(['chatData','id']),
                    hash : state.chatwidget.getIn(['chatData','hash'])
                }
            },'survey'));
        } else if (action == 'lhc_load_ext') {
            const parts = e.data.replace('lhc_load_ext:','').split('::');
            executeExtension(parts[0],JSON.parse(parts[1]));
        } else if (action == 'lhc_event') {
            const parts = e.data.replace('lhc_event:','').split('::');
            let args = JSON.parse(parts[1]);
            if (Array.isArray(args)) {
                args.push(dispatch);
                args.push(getState);
            }
            helperFunctions.emitEvent(parts[0],args);
        } else if (action == 'lhc_sizing_chat') {
            helperFunctions.sendMessageParent('widgetHeight', [{'height' : (parseInt(e.data.split(':')[1]) + 50)}]);
        } else if (action == 'lhc_init') {

            if (readyReceived === true) {
                return;
            }

            readyReceived = true;

            var paramsInit = JSON.parse(e.data.replace('lhc_init:',''));

            window.lhcChat = {};
            window.lhcChat['base_url'] = paramsInit['base_url'] + (paramsInit['lang'] && paramsInit['lang'] != '' ? paramsInit['lang'].replace('/','') + '/' : '');
            window.lhcChat['staticJS'] = paramsInit['staticJS'];
            window.lhcChat['mode'] = paramsInit['mode'];
            window.lhcChat['is_focused'] = true;
            window.lhcChat['domain_lhc'] = paramsInit['domain_lhc'] || null;

            __webpack_public_path__ = window.lhcChat['staticJS']['chunk_js'] + "/";

            var date = new Date();

            i18n.init({
                backend: {
                    loadPath: paramsInit['base_url']+'{{lng}}/widgetrestapi/lang/{{ns}}?v=4'+(""+date.getFullYear() + date.getMonth() + date.getDate())
                },
                lng: ((paramsInit['lang'] && paramsInit['lang'] != '') ?  paramsInit['lang'].replace('/','') : 'eng'),
                fallbackLng: 'eng',
                debug: false,
                interpolation: {
                    escapeValue: false, // not needed for react as it escapes by default
                }
            }, () => {
                dispatch({
                    type: 'loadedCore'
                })
            });

            paramsInit['base_url'] = window.lhcChat['base_url'];

            Object.keys(paramsInit).forEach(key => {
                
                let value = paramsInit[key];

                if (key === 'static_chat') {
                    if (value.id && value.hash) {
                        dispatch({
                            type: 'CHAT_ALREADY_STARTED',
                            data: {'id' : value.id, 'hash' : value.hash}
                        })
                    };

                    if (value.vid) {
                        dispatch({
                            type: 'CHAT_SET_VID',
                            data: value.vid
                        })
                    }

                } else if (key === 'ses_ref') {
                    dispatch({
                        type: 'CHAT_SESSION_REFFERER',
                        data: {'ref' : value}
                    })
                } else if (key === 'proactive') {
                    setTimeout(() => {
                        dispatch(initProactive(value))
                    }, readyReceived === true ? 0 : 700);
                } else {
                    dispatch({
                        type: key,
                        data: value
                    });
                }
            });

            const focusChangeCb = (e) => {
                const focused = e.type === "focus";
                if (focused == true) {
                    helperFunctions.sendMessageParent('unread_message_title',[{'status':true}]);
                }
                window.lhcChat['is_focused'] = focused;
            };

            window.addEventListener('focus', focusChangeCb);
            window.addEventListener('blur', focusChangeCb);
            window.addEventListener('pageshow', focusChangeCb);
            window.addEventListener('pagehide', focusChangeCb);

            if (paramsInit['mode'] == 'popup') {
                helperFunctions.sendMessageParent('endChatCookies');

                const sessionChat = helperFunctions.getSessionStorage('_chat');

                if (sessionChat !== null && !paramsInit['static_chat']['id']) {
                    dispatch({
                        type: 'CHAT_ALREADY_STARTED',
                        data: JSON.parse(sessionChat)
                    })
                } else if (paramsInit['static_chat']['id']) {
                    helperFunctions.setSessionStorage('_chat',JSON.stringify(paramsInit['static_chat']));
                }
            }

        } else if (action == 'lhc_continue_chat') {
            const state = getState();
            dispatch(initChatUI({
                'id': state.chatwidget.getIn(['chatData','id']),
                'hash' : state.chatwidget.getIn(['chatData','hash']),
                'theme' :  state.chatwidget.get('theme')
            }));
        }
    }

    if ( window.addEventListener ) {
        // FF
        window.addEventListener("message", handleParentMessage, false);
        window.addEventListener("beforeunload", () => {
            dispatch(pageUnload());
        }, false);

    } else if ( window.attachEvent ) {
        // IE
        window.attachEvent("onmessage", handleParentMessage);
        window.attachEvent("beforeunload", () => {
            dispatch(pageUnload());
        });
    } else if ( document.attachEvent ) {
        // IE
        document.attachEvent("onmessage", handleParentMessage);
        document.attachEvent("beforeunload", () => {
            dispatch(pageUnload());
        });
    };

    // We are not listening online event, because we want that this attribute would be changed by xhr call so we will be sure there is an internet.
    // window.addEventListener('online', () => dispatch({type: "NO_CONNECTION", data: false}));
    window.addEventListener('offline', () => dispatch({type: "NO_CONNECTION", data: true}));

    // Iframe is ready to receive updates
    // But we do not want to receive any updates as popup
    if (!window.opener && !window.initializeLHC) {
        helperFunctions.sendMessageParent('ready', window.opener ? true : false);
    } else if (window.initializeLHC) {
        handleParentMessage({data : window.initializeLHC});
        // Send message that popup is ready
        // Parent window will send additional form data in a secure way
        // Without exposing parameters in URL
        helperFunctions.sendMessageParent('ready_popup', window.opener ? true : false);
    }

}