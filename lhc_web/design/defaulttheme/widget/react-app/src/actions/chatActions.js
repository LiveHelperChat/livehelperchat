import axios from "axios";
import { helperFunctions } from "../lib/helperFunctions";
import { STATUS_CLOSED_CHAT, STATUS_BOT_CHAT, STATUS_SUB_SURVEY_SHOW, STATUS_SUB_USER_CLOSED_CHAT, STATUS_SUB_CONTACT_FORM } from "../constants/chat-status";

window.lhcAxios = axios;

let syncStatus = {
    'msg' : false,
    'status' : false,
    'error_counter' : 0,
    'auto_close_timeout': null
};

const defaultHeaders = {headers : {'Content-Type': 'application/x-www-form-urlencoded'}};

export function closeWidget() {
    return function(dispatch) {
        dispatch({type: "closeWidget"});
    }
}

export function abtractAction(eventData) {
    return function(dispatch) {
        dispatch(eventData);
    }
}

export function hideInvitation() {
    return function(dispatch, getState) {
        const state = getState();
        helperFunctions.sendMessageParent('closeWidget', [{'sender' : 'closeButton'}]);
        helperFunctions.sendMessageParent('cancelInvitation', [{'name' :  state.chatwidget.getIn(['proactive','data','invitation_name'])}]);

        axios.post(window.lhcChat['base_url'] + "chat/chatwidgetclosed/(vid)/" + state.chatwidget.get('vid'), null, defaultHeaders).then((response) => {
            dispatch({type: "HIDE_INVITATION"});
        })
        .catch((err) => {
            console.log(err);
        })
    }
}

export function minimizeWidget(forceClose) {
    return function(dispatch, getState) {
        const state = getState();
        if (state.chatwidget.getIn(['proactive','has']) === true) {
            hideInvitation()(dispatch, getState);
        } else {
            helperFunctions.sendMessageParent('closeWidget', [{'sender' : 'closeButton'}]);
        }
        if (forceClose && (window.lhcChat['mode'] == 'popup' || window.lhcChat['mode'] == 'embed')) {
            helperFunctions.removeSessionStorage('_chat');
            window.close();
        }
    }
}

export function endChat(obj, action) {
    action = action || "t";
    return function(dispatch, getState) {
        clearTimeout(syncStatus.auto_close_timeout);
        axios.post(window.lhcChat['base_url'] + "chat/chatwidgetclosed/(eclose)/"+action+"/(hash)/" + obj['chat']['id'] +'_'+ obj['chat']['hash'] + '/(vid)/' + obj['vid'] + '/(close)/' + (!obj.noClose ? '1' : '0'), null, defaultHeaders)
        .then((response) => {
            if (!obj.noClose) {
                if (window.lhcChat['mode'] == 'popup') {
                    helperFunctions.removeSessionStorage('_chat');

                    // We try to close window at first place
                    window.close();

                    // If it's direct chat window we have to show start chat form
                    helperFunctions.eventEmitter.emitEvent('endedChat', [{'chat_id':obj['chat']['id'], 'hash': obj['chat']['hash']}]);
                } else {
                    helperFunctions.sendMessageParent('endChat', [{show_start: obj['show_start'], 'sender' : 'endButton'}]);
                }
            } else {
                dispatch({type: "INIT_CLOSE", data: obj})
            }
        })
        .catch((err) => {
            console.log(err);
        })
    }
}

export function getProducts(obj) {
    return function(dispatch) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/getproducts/" + obj['dep_id'], null, defaultHeaders)
        .then((response) => {
            dispatch({type: "INIT_PRODUCTS", data: response.data})
        })
        .catch((err) => {
            console.log(err);
        })
    }
}

export function voteAction(obj) {
    return axios.post(window.lhcChat['base_url'] + "chat/voteaction/" + obj.id + '/' + obj.hash + '/' + obj.type, null, defaultHeaders)
}

export function transferToHumanAction(obj) {
    return axios.post(window.lhcChat['base_url'] + "chat/transfertohuman/" + obj.id + '/' + obj.hash, null, defaultHeaders)
}

export function initProactive(data) {
    return function(dispatch, getState) {
        const state = getState();

        let payload = {
            'invitation' : data.invitation,
            'vid_id' : data.vid_id,
            'uts' : (new Date()).getTime()
        };

        if (state.chatwidget.get('theme')) {
            payload['theme'] = state.chatwidget.get('theme');
        }

        if (state.chatwidget.get('vid')) {
            payload['vid'] = state.chatwidget.get('vid');
        }

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/getinvitation", payload, defaultHeaders).then((response) => {
            if (response.data.chat_id && response.data.chat_hash) {
                dispatch({type: "ONLINE_SUBMITTED", data: {
                        success : true,
                        chatData : {
                            id : response.data.chat_id,
                            hash : response.data.chat_hash
                        }
                }});
                showMessageSnippet({'id' : response.data.chat_id, 'hash' : response.data.chat_hash})(dispatch, getState);
            } else {
                dispatch({type: "PROACTIVE", data: response.data})
            }
        });
    }
}

export function showMessageSnippet(obj) {
    return function(dispatch, getState) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/getmessagesnippet", obj, defaultHeaders)
        .then((response) => {
            helperFunctions.sendMessageParent('msgSnippet',[response.data]);
            const state = getState();
            helperFunctions.emitEvent('play_sound', [{'type' : 'new_chat','sound_on' : (state.chatwidget.getIn(['usersettings','soundOn']) === true), 'widget_open' : false}]);
        })
        .catch((err) => {
        })
    }
}

export function storeSubscriber(payload) {
    return function(dispatch, getState) {
        const state = getState();

        let args = '/(action)/sub';

        if (state.chatwidget.hasIn(['chatData','id']) && state.chatwidget.hasIn(['chatData','hash'])) {
            args = args + '/(hash)/' + state.chatwidget.getIn(['chatData','id']) + '_' + state.chatwidget.getIn(['chatData','hash']);
        }

        if (state.chatwidget.get('theme')) {
            args = args + '/(theme)/' + state.chatwidget.get('theme');
        }

        if (state.chatwidget.get('vid')) {
            args = args + '/(vid)/' + state.chatwidget.get('vid');
        }

        axios.post(window.lhcChat['base_url'] + "notifications/subscribe" +args, {'data' : payload}, defaultHeaders)
            .then((response) => {
                if (state.chatwidget.hasIn(['chatData','id']) && state.chatwidget.hasIn(['chatData','hash'])) {
                    dispatch(fetchMessages({
                        'chat_id': state.chatwidget.getIn(['chatData','id']),
                        'hash' : state.chatwidget.getIn(['chatData','hash']),
                        'lmgsid' : state.chatwidget.getIn(['chatLiveData','lmsgid']),
                        'theme' : state.chatwidget.get('theme')
                    }));
                }
        })
    }
}

export function updateTriggerClicked(typeParams, params) {
    return function(dispatch, getState) {
        const state = getState();
        return axios.post(window.lhcChat['base_url'] + "genericbot/"+(typeParams.mainType ? typeParams.mainType : "buttonclicked")+"/" + state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']) + typeParams.type, params, defaultHeaders)
    }
}

export function subscribeNotifications(params) {
    return function(dispatch, getState) {
        const state = getState();
        helperFunctions.sendMessageParent('subscribeEvent', [{'pk' : state.chatwidget.getIn(['chat_ui','notifications_pk'])}]);
    }
}

export function initOfflineForm(obj) {
    return function(dispatch) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/onlinesettings", obj, defaultHeaders)
        .then((response) => {
            dispatch({type: "OFFLINE_FIELDS_UPDATED", data: response.data})
        })
        .catch((err) => {
            dispatch({type: "OFFLINE_FIELDS_REJECTED", data: err})
        })
    }
}

export function initOnlineForm(obj) {
    return function(dispatch) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/onlinesettings", obj, defaultHeaders)
        .then((response) => {
            if (response.data.paid.continue && response.data.paid.continue === true) {
                dispatch({type: "ONLINE_SUBMITTED", data: {
                        success : true,
                        chatData : {
                            id : response.data.paid.id,
                            hash : response.data.paid.hash
                        }
               }});
            } else {
                dispatch({type: "ONLINE_FIELDS_UPDATED", data: response.data})
            }
        })
        .catch((err) => {
            dispatch({type: "ONLINE_FIELDS_REJECTED", data: err})
        })
    }
}

export function getCaptcha(dispatch, form, obj) {
    var date = new Date();
    var timestamp = Math.round(date.getTime()/1000);
    axios.post(window.lhcChat['base_url'] + "captcha/captchastring/fake/" + timestamp, null, defaultHeaders)
    .then((response) => {
        dispatch({type: "captcha", data: {'hash' : response.data.result, 'ts' : timestamp}});

        // Update submit object instantly
        obj.fields['captcha_' + response.data.result] = timestamp;
        obj.fields['tscaptcha'] = timestamp;

        // We auto resubmit only one time
        if (!obj.fields['tscaptcha_resubmit']) {
            obj.fields['tscaptcha_resubmit'] = 1;
            form(obj)(dispatch);
        } else {
            delete obj.fields['tscaptcha_resubmit'];
        }

    });
}

export function submitOnlineForm(obj) {
    return function(dispatch) {
        dispatch({type: "ONLINE_SUBMITTING"});
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/submitonline", obj, defaultHeaders)
        .then((response) => {

            // If validation contains invalid captcha update it instantly
            if (response.data.success === false && response.data.errors.captcha) {
                getCaptcha(dispatch, submitOnlineForm, obj);
                if (!obj.fields['tscaptcha_resubmit']) {
                    return;
                }
            }

            dispatch({type: "ONLINE_SUBMITTED", data: response.data});

            if (response.data.t) {
                helperFunctions.sendMessageParent('botTrigger',[{'trigger' : response.data.t}]);
            }

        })
        .catch((err) => {
            dispatch({type: "ONLINE_SUBMITT_REJECTED", data: err})
        })
    }
}

export function submitOfflineForm(obj) {
    return function(dispatch) {
        dispatch({type: "OFFLINE_SUBMITTING"});
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/submitoffline", obj, {headers: { 'Content-Type': 'multipart/form-data'}})
        .then((response) => {

            // If validation contains invalid captcha update it instantly
            if (response.data.success === false && response.data.errors.captcha) {
                getCaptcha(dispatch, submitOfflineForm, obj);
                if (!obj.fields['tscaptcha_resubmit']) {
                    return;
                }
            }

            dispatch({type: "OFFLINE_SUBMITTED", data: response.data})
        })
        .catch((err) => {
            dispatch({type: "OFFLINE_SUBMITT_REJECTED", data: err})
        })
    }
}

export function updateUISettings(obj) {
    return function(dispatch, getState) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/uisettings", obj, defaultHeaders)
            .then((response) => {
                dispatch({type: "REFRESH_UI_COMPLETED", data: response.data})
            })
            .catch((err) => {
                console.log(err);
                dispatch({type: "REFRESH_UI_REJECTED", data: err})
            })
    }
}

export function initChatUI(obj) {

    // We should always sync chat status
    // As this value can be true if visitor starts another chat just
    syncStatus.status = false;

    return function(dispatch, getState) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/initchat", obj, defaultHeaders)
        .then((response) => {
            dispatch({type: "INIT_CHAT_SUBMITTED", data: response.data})

            if (response.data.init_calls) {
                response.data.init_calls.forEach((callExtension) => {
                    if (callExtension.extension === 'nodeJSChat') {
                        import('../extensions/nodejs/nodeJSChat').then((module) => {
                            module.nodeJSChat.bootstrap(callExtension.params, dispatch, getState);
                        });

                    } else if (callExtension.extension === 'dummy_extensions') {
                        // Import your extension here
                    }
                });
            }
        })
        .catch((err) => {
            console.log(err);
            dispatch({type: "INIT_CHAT_REJECTED", data: err})
        })
    }
}

function processResponseCheckStatus(response, getState, dispatch) {
    if (response.op) {

        response.op.forEach((op) => {
            var action = op.split(':')[0];
            if (action == 'lhc_chat_redirect') {
                helperFunctions.sendMessageParent('location',[op.split(':')[1].replace(new RegExp('__SPLIT__','g'),':')]);
            } else if (action == 'lhc_screenshot') {

                const state = getState();

                var append = '';

                if ( state.chatwidget.hasIn(['chatData','id'])) {
                    append = append + '/(hash)/' + state.chatwidget.getIn(['chatData','id']) + '_' + state.chatwidget.getIn(['chatData','hash']);
                }

                if ( state.chatwidget.get('vid')) {
                    append = append + '/(vid)/' + state.chatwidget.get('vid');
                }

                helperFunctions.sendMessageParent('screenshot',[window.lhcChat['base_url'] + 'file/storescreenshot' + append]);
            } else if (action == 'lhc_cobrowse') {
                helperFunctions.sendMessageParent('screenshare',[]);
            } else if (action == 'lhc_cobrowse_cmd') {
                helperFunctions.sendMessageParent('screenshareCommand',[op]);
            } else if (action == 'lhc_ui_refresh') {
                const state = getState();
                updateUISettings({'id' : state.chatwidget.getIn(['chatData','id']), 'hash' : state.chatwidget.getIn(['chatData','hash'])})(dispatch, getState);
            } else if (action.indexOf('lhinst.updateMessageRow') !== -1) {
                const state = getState();
                updateMessage({'msg_id' : action.replace('lhinst.updateMessageRow(','').replace(')','') ,'id' : state.chatwidget.getIn(['chatData','id']), 'hash' : state.chatwidget.getIn(['chatData','hash'])})(dispatch, getState);
            }
        });
    }
}

export function updateMessage(obj) {
    return function(dispatch, getState) {
        const state = getState();

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/fetchmessage", obj, defaultHeaders)
        .then((response) => {
            let elm = document.getElementById('msg-'+response.data.id);
            if (elm === null) {
                return;
            }
            const classNameRow = elm.className;
            elm.outerHTML = response.data.msg;
            elm.className = classNameRow;

            // Just adjust a scroll
            let elmScroll = document.getElementById('messages-scroll');
            if (elmScroll !== null) {
                elmScroll.scrollTop = elmScroll.scrollHeight + 1000;
            }

            let elmUpdated = document.getElementById('msg-'+response.data.id);
            let collection = elmUpdated.getElementsByTagName('script');

            for (let item of collection) {
                var attribs = {};
                if (item.hasAttributes()) {
                    var attrs = item.attributes;
                    for (var i = attrs.length - 1; i >= 0; i--) {
                        attribs[attrs[i].name] = attrs[i].value;
                    }
                }
                item.attribs = attribs;
                parseScript(item, this);
            }

        })
        .catch((err) => {
            console.log(err);
        })
    }
}

export function parseScript(domNode, inst) {
    const attr = domNode.attribs;

    if (attr['data-bot-action'] == 'lhinst.disableVisitorEditor') {
        inst.disableEditor = true;
    } else if (attr['data-bot-action'] == 'lhinst.setDelay') {
        inst.delayData.push(JSON.parse(attr['data-bot-args']));
    } else if (attr['data-bot-action'] == 'execute-js') {
        if (attr['data-bot-extension']) {
            var args = {};
            if (typeof attr['data-bot-args'] !== 'undefined') {
                args = JSON.parse(attr['data-bot-args']);
            }
            helperFunctions.emitEvent('extensionExecute',[attr['data-bot-extension'],[args]]);
        } else if (attr['data-bot-emit']) {
            var args = {};
            if (typeof attr['data-bot-args'] !== 'undefined') {
                args = JSON.parse(attr['data-bot-args']);
            }
            helperFunctions.emitEvent(attr['data-bot-emit'],[args]);
        } else if (attr['data-bot-event']) {
            inst.props[attr['data-bot-event']]();
        } else {
            if (attr.src) {
                var th = document.getElementsByTagName('head')[0];
                var s = document.createElement('script');
                s.setAttribute('type','text/javascript');
                s.setAttribute('src', attr.src);
                th.appendChild(s);
            } else if (typeof domNode.children[0] !== 'undefined' && typeof domNode.children[0]['data'] !== 'undefined') {
                eval(domNode.children[0]['data']);
            }
        }
    }
}

function isNetworkError(err) {
    return !!err.isAxiosError && !err.response;
}

export function fetchMessages(obj) {
    return function(dispatch, getState) {

        if (syncStatus.msg == true) {
            return;
        }

        syncStatus.msg = true;

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/fetchmessages", obj, defaultHeaders)
        .then((response) => {

            syncStatus.msg = false;

            dispatch({type: "FETCH_MESSAGES_SUBMITTED", data: response.data});

            processResponseCheckStatus(response.data, getState, dispatch);

            helperFunctions.emitEvent('chat.fetch_messages',[response.data, dispatch, getState]);

            if (response.data.cs || (response.data.closed && response.data.closed === true)) {
                axios.post(window.lhcChat['base_url'] + "widgetrestapi/checkchatstatus", obj, defaultHeaders)
                .then((response) => {
                    if (response.data.deleted) {
                        helperFunctions.sendMessageParent('endChat',[{'sender' : 'endButton'}]);
                        clearTimeout(syncStatus.auto_close_timeout);
                    } else {
                        dispatch({type: "CHECK_CHAT_STATUS_FINISHED", data: response.data});
                        helperFunctions.emitEvent('chat.check_status',[response.data, dispatch, getState]);
                    }
                    if (response.data.closed && response.data.closed === true && !response.data.deleted) {
                        setAutoClose(getState);
                    }
                })
                .catch((err) => {
                    dispatch({type: "CHECK_CHAT_STATUS_REJECTED", data: err})
                })
            }

        })
        .catch((err) => {

            if (isNetworkError(err)) {
                dispatch({type: "NO_CONNECTION", data: true});
            }

            syncStatus.msg = false;
        })
    }
}

export function checkChatStatus(obj) {
    return function(dispatch, getState) {

        if (syncStatus.status == true) {
            return;
        }

        syncStatus.status = true;

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/checkchatstatus", obj, defaultHeaders)
        .then((response) => {
            if (response.data.deleted) {
                helperFunctions.sendMessageParent('endChat',[{'sender' : 'endButton'}]);
                clearTimeout(syncStatus.auto_close_timeout);
            } else {
                syncStatus.status = false;
                dispatch({type: "CHECK_CHAT_STATUS_FINISHED", data: response.data});
                helperFunctions.emitEvent('chat.check_status',[response.data, dispatch, getState]);
            }
            if (response.data.closed && response.data.closed === true && !response.data.deleted) {
                setAutoClose(getState);
            }
        })
        .catch((err) => {
            syncStatus.status = false;
        })
    }
}

function setAutoClose(getState) {
    const state = getState();
    if (state.chatwidget.hasIn(['chat_ui','open_timeout'])) {
        clearTimeout(syncStatus.auto_close_timeout);
        syncStatus.auto_close_timeout = setTimeout(function(){
            helperFunctions.sendMessageParent('endChat',[{'sender' : 'endButton'}]);
            clearTimeout(syncStatus.auto_close_timeout);
        },state.chatwidget.getIn(['chat_ui','open_timeout']) * 1000);
    }
}

export function pageUnload() {
    return function(dispatch, getState) {
        const state = getState();

        /**
         * Unload always if we have this options in theme and chat is in survey mode on mobile or is unloading in general desktop application
         * */
        if (state.chatwidget.hasIn(['chat_ui','close_on_unload']) && state.chatwidget.get('mode') == 'embed') {

            let surveyMode = false
            let surveyByVisitor = (state.chatwidget.hasIn(['chatLiveData','status_sub']) && (state.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_CONTACT_FORM || state.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_SURVEY_SHOW || (state.chatwidget.getIn(['chatLiveData','status_sub']) == STATUS_SUB_USER_CLOSED_CHAT && (state.chatwidget.getIn(['chatLiveData','uid']) > 0 || state.chatwidget.getIn(['chatLiveData','status']) === STATUS_BOT_CHAT))));
            let surveyByOperator = (state.chatwidget.getIn(['chatLiveData','status']) == STATUS_CLOSED_CHAT && state.chatwidget.getIn(['chatLiveData','uid']) > 0);

            if ((surveyByVisitor == true || surveyByOperator) && state.chatwidget.hasIn(['chat_ui','survey_id'])) {
                // If survey button is required and we have not went to survey yet
                if ((!state.chatwidget.hasIn(['chat_ui','survey_button']) || state.chatwidget.getIn(['chat_ui_state','show_survey']) === 1) || surveyByVisitor == true) {
                    surveyMode = true;
                }
            }

            if (state.chatwidget.get('isMobile') === false || surveyMode === true) {
                helperFunctions.sendMessageParent('endChat',[{'sender' : 'endButton'}]);
            }
        }

        if (state.chatwidget.hasIn(['chatData','id']) && state.chatwidget.hasIn(['chatData','hash'])) {
            axios.post(window.lhcChat['base_url'] + "chat/userclosechat/" +  state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']), null, defaultHeaders);
        } else if (state.chatwidget.getIn(['proactive','has']) === true && window.lhcChat['mode'] == 'popup' && window.opener) {
            hideInvitation()(dispatch, getState);
        }
    }
}

function checkErrorCounter() {
   if (syncStatus.error_counter == 2) {
       // Restart widget on second error
       helperFunctions.sendMessageParent('reloadWidget',[]);
   }
}

export function addMessage(obj) {
    return function(dispatch, getState) {

        try {
            helperFunctions.eventEmitter.emitEvent('messageSend', [{'chat_id':obj.id, 'hash': obj.hash, msg: obj.msg}]);
        } catch (error) {
            helperFunctions.logJSError({
                'stack' : JSON.stringify(JSON.stringify(error))
            });
        }

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/addmsguser", obj, defaultHeaders)
            .then((response) => {

                // Update error state if it changed
                if (response.data.error || getState().chatwidget.getIn(['chatLiveData','error'])) {
                    dispatch({type: "ADD_MESSAGES_SUBMITTED", data: {r: response.data.r, msg: obj.msg}});
                }

                fetchMessages({'theme' : obj.theme, 'chat_id' : obj.id, 'lmgsid' : obj.lmgsid, 'hash' : obj.hash})(dispatch, getState);

                if (response.data.t) {
                    helperFunctions.sendMessageParent('botTrigger',[{'trigger' : response.data.t}]);
                }

                if (typeof response.data.r === 'undefined' || (response.data.error === true && response.data.system === true)) {

                    syncStatus.error_counter++;

                    // Log error only if it happens two times in a row
                    if (syncStatus.error_counter == 2) {
                        helperFunctions.logJSError({
                            'stack' :  JSON.stringify(JSON.stringify(response) + "\nRD:"+JSON.stringify(response.data) +"\nRH:"+ JSON.stringify(response.headers) +"\nRS:"+ JSON.stringify(response.status))
                        });

                        checkErrorCounter();
                    }

                    helperFunctions.eventEmitter.emitEvent('messageSendError', [{'chat_id':obj.id, 'hash': obj.hash, msg: JSON.stringify(response.data)}]);
                } else {
                    syncStatus.error_counter = 0;
                }

            })
            .catch((error) => {
                if (isNetworkError(error)) {
                    dispatch({type: "ADD_MESSAGES_SUBMITTED", data: {r: "SEND_CONNECTION", "msg" : obj.msg}});
                    dispatch({type: "NO_CONNECTION", data: true});
                } else {
                    syncStatus.error_counter++;

                    var stack = null;

                    // Error
                    if (error.response) {
                        stack = JSON.stringify(JSON.stringify(error) + "\nRD:"+JSON.stringify(error.response.data) +"\nRH:"+ JSON.stringify(error.response.headers) +"\nRS:"+ JSON.stringify(error.response.status));
                    } else if (error.request) {
                        stack = JSON.stringify(JSON.stringify(error));
                    } else {
                        stack = JSON.stringify(JSON.stringify(error));
                    }

                    // Log error only if it happens two times in a row
                    if (syncStatus.error_counter == 2) {

                        helperFunctions.logJSError({
                            'stack': stack
                        });

                        helperFunctions.eventEmitter.emitEvent('messageSendError', [{'chat_id':obj.id, 'hash': obj.hash, msg: stack}]);

                        checkErrorCounter();
                    } else {

                        dispatch({type: "ADD_MESSAGES_SUBMITTED", data: {r: "SEND_FAILED", "msg" : obj.msg}});

                        // Try to send message again
                        addMessage(obj)(dispatch, getState);
                    }
                }
            })
    }
}

export function userTyping(status, msg) {
    return function(dispatch, getState) {
        const state = getState();

        if (status === 'true') {
            helperFunctions.eventEmitter.emitEvent('visitorTyping', [{'chat_id':state.chatwidget.getIn(['chatData','id']), 'hash': state.chatwidget.getIn(['chatData','hash']),'status': true, msg: msg}]);
        } else {
            helperFunctions.eventEmitter.emitEvent('visitorTyping', [{'chat_id':state.chatwidget.getIn(['chatData','id']), 'hash': state.chatwidget.getIn(['chatData','hash']),'status': false}]);
        }

        if (!state.chatwidget.get('overrides').contains('typing')) {
            axios.post(window.lhcChat['base_url'] + "chat/usertyping/" + state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']) + '/' + status, {'msg' : msg}, defaultHeaders)
                .then((response) => {
            }).catch((err) => {
                console.log(err);
            });
        }
    }
}
