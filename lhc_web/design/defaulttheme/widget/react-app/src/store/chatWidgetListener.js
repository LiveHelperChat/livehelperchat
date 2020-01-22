import { endChat, initChatUI, pageUnload, focusChange, storeSubscriber, initProactive } from "../actions/chatActions"
import { helperFunctions } from "../lib/helperFunctions";

export default function (dispatch, getState) {

    const events = [
        {id : 'closedWidget', cb : (data) => {dispatch({type: 'closedWidget', data: data})}},
        {id : 'endedChat', cb : (data) => {
            dispatch({type: 'endedChat', data: data});
            if (window.lhcChat['mode'] == 'popup') {
                window.close();
            }
        }},
        {id : 'reopenNotification', cb : (data) => {dispatch({type: 'CHAT_ALREADY_STARTED', data: {'id' : data.id, 'hash' : data.hash}})}},
        {id : 'subcribedEvent', cb : (e) => {dispatch(storeSubscriber(e.payload))}},
        {id : 'onlineStatus',cb : (data) => {dispatch({type: 'onlineStatus', data: data})}},
        {id : 'toggleSound',cb : (data) => {dispatch({type: 'toggleSound', data: data})}},
        {id : 'widgetStatus',cb : (data) => {dispatch({type: 'widgetStatus', data: data})}},
        {id : 'jsVars',cb : (data) => {dispatch({type: 'jsVars', data: data})}},
        {id : 'proactive', cb : (data) => {dispatch(initProactive(data))}}
    ];

    // Event listeners
    events.forEach((evt) => {
       helperFunctions.eventEmitter.addListener(evt.id, evt.cb);
    });

    let readyReceived = false;

    function handleParentMessage(e) {
        if (typeof e.data !== 'string') { return; }

        var action = e.data.split(':')[0];

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

        } else if (action == 'lhc_event') {
            const parts = e.data.replace('lhc_event:','').split('::');
            helperFunctions.emitEvent(parts[0],JSON.parse(parts[1]));
        } else if (action == 'lhc_sizing_chat') {
            helperFunctions.sendMessageParent('widgetHeight', [{'height' : (parseInt(e.data.split(':')[1]) + 50)}]);
        } else if (action == 'lhc_init') {

            if (readyReceived === true) {
                return;
            }

            readyReceived = true;

            var paramsInit = JSON.parse(e.data.replace('lhc_init:',''));

            window.lhcChat = {};
            window.lhcChat['base_url'] = paramsInit['base_url'] + (paramsInit['lang'] && paramsInit['lang'] != '' ? paramsInit['lang'] + '/' : '');
            window.lhcChat['staticJS'] = paramsInit['staticJS'];
            window.lhcChat['mode'] = paramsInit['mode'];

            for (const [key, value] of Object.entries(paramsInit)) {
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
                    dispatch(initProactive(value))
                } else {
                    dispatch({
                        type: key,
                        data: value
                    });
                }
            }

            if (paramsInit['mode'] == 'popup') {
                const focusChangeCb = (e) => {
                    const focused = e.type === "focus";
                    dispatch(focusChange(focused));
                };

                window.onpageshow = window.onfocus = focusChangeCb;
                window.onpagehide = window.onblur = focusChangeCb;
            }

            dispatch({
                type: 'loadedCore'
            })

            if (paramsInit['mode'] == 'popup') {
                helperFunctions.sendMessageParent('endChatCookies');

                const sessionChat = helperFunctions.getSessionStorage('lhc_chat');

                if (sessionChat !== null && !paramsInit['static_chat']['id']) {
                    dispatch({
                        type: 'CHAT_ALREADY_STARTED',
                        data: JSON.parse(sessionChat)
                    })
                } else if (paramsInit['static_chat']['id']) {
                    helperFunctions.setSessionStorage('lhc_chat',JSON.stringify(paramsInit['static_chat']));
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

    // Iframe is ready to receive updates
    helperFunctions.sendMessageParent('ready', window.opener ? true : false);

    if (!window.opener && window.initializeLHC) {
        handleParentMessage({data : window.initializeLHC});
    }

}