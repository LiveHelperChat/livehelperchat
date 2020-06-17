import axios from "axios";
import { helperFunctions } from "../lib/helperFunctions";
import { STATUS_CLOSED_CHAT, STATUS_BOT_CHAT, STATUS_SUB_SURVEY_SHOW, STATUS_SUB_USER_CLOSED_CHAT, STATUS_SUB_CONTACT_FORM } from "../constants/chat-status";

window.lhcAxios = axios;

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
        axios.get(window.lhcChat['base_url'] + "chat/chatwidgetclosed/(vid)/" + state.chatwidget.get('vid')).then((response) => {
            dispatch({type: "HIDE_INVITATION"});
        })
        .catch((err) => {
            console.log(err);
        })
    }
}

export function minimizeWidget() {
    return function(dispatch, getState) {
        const state = getState();
        if (state.chatwidget.getIn(['proactive','has']) === true) {
            hideInvitation()(dispatch, getState);
        } else {
            helperFunctions.sendMessageParent('closeWidget', [{'sender' : 'closeButton'}]);
        }
    }
}

export function endChat(obj) {
    return function(dispatch, getState) {
        axios.get(window.lhcChat['base_url'] + "chat/chatwidgetclosed/(eclose)/t/(hash)/" + obj['chat']['id'] +'_'+ obj['chat']['hash'] + '/(vid)/' + obj['vid'])
        .then((response) => {
            if (!obj.noClose) {
                if (window.lhcChat['mode'] == 'popup') {
                    helperFunctions.removeSessionStorage('lhc_chat');
                    window.close();
                } else {
                    helperFunctions.sendMessageParent('endChat', [{'sender' : 'endButton'}]);
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
        axios.get(window.lhcChat['base_url'] + "widgetrestapi/getproducts/" + obj['dep_id'])
        .then((response) => {
            dispatch({type: "INIT_PRODUCTS", data: response.data})
        })
        .catch((err) => {
            console.log(err);
        })
    }
}

export function voteAction(obj) {
    return axios.post(window.lhcChat['base_url'] + "chat/voteaction/" + obj.id + '/' + obj.hash + '/' + obj.type)
}

export function transferToHumanAction(obj) {
    return axios.post(window.lhcChat['base_url'] + "chat/transfertohuman/" + obj.id + '/' + obj.hash)
}

export function initProactive(data) {
    return function(dispatch, getState) {
        const state = getState();

        let payload = {
            'invitation' : data.invitation,
            'vid_id' : data.vid_id
        };

        if (state.chatwidget.get('theme')) {
            payload['theme'] = state.chatwidget.get('theme');
        }

        if (state.chatwidget.get('vid')) {
            payload['vid'] = state.chatwidget.get('vid');
        }

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/getinvitation", payload).then((response) => {
            dispatch({type: "PROACTIVE", data: response.data})
        });
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

        axios.post(window.lhcChat['base_url'] + "notifications/subscribe" +args, {'data' : payload})
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
        return axios.post(window.lhcChat['base_url'] + "genericbot/"+(typeParams.mainType ? typeParams.mainType : "buttonclicked")+"/" + state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']) + typeParams.type, params)
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
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/onlinesettings", obj)
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
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/onlinesettings", obj)
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
    axios.post(window.lhcChat['base_url'] + "captcha/captchastring/fake/" + timestamp)
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
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/submitonline", obj)
        .then((response) => {

            // If validation contains invalid captcha update it instantly
            if (response.data.success === false && response.data.errors.captcha) {
                getCaptcha(dispatch, submitOnlineForm, obj);
                if (!obj.fields['tscaptcha_resubmit']) {
                    return;
                }
            }

            dispatch({type: "ONLINE_SUBMITTED", data: response.data});
        })
        .catch((err) => {
            dispatch({type: "ONLINE_SUBMITT_REJECTED", data: err})
        })
    }
}

export function submitOfflineForm(obj) {
    return function(dispatch) {
        dispatch({type: "OFFLINE_SUBMITTING"});
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/submitoffline", obj)
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
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/uisettings", obj)
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
    return function(dispatch, getState) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/initchat", obj)
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
            }
        });
    }
}

export function fetchMessages(obj) {
    return function(dispatch, getState) {

        const state = getState();

        if (state.chatwidget.getIn(['syncStatus','msg']) == true) {
            return;
        }

        dispatch({type: "FETCH_MESSAGES_STARTED"});

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/fetchmessages", obj)
        .then((response) => {
            dispatch({type: "FETCH_MESSAGES_SUBMITTED", data: response.data});

            processResponseCheckStatus(response.data, getState, dispatch);

            helperFunctions.emitEvent('chat.fetch_messages',[response.data, dispatch, getState]);

            if (response.data.cs || (response.data.closed && response.data.closed === true)) {
                axios.post(window.lhcChat['base_url'] + "widgetrestapi/checkchatstatus", obj)
                .then((response) => {
                    if (response.data.deleted) {
                        //window.lhcChat.eventEmitter.emitEvent('endChat', [{'sender' : 'endButton'}]);
                    } else {
                        dispatch({type: "CHECK_CHAT_STATUS_FINISHED", data: response.data});
                        helperFunctions.emitEvent('chat.check_status',[response.data, dispatch, getState]);
                    }
                })
                .catch((err) => {
                    dispatch({type: "CHECK_CHAT_STATUS_REJECTED", data: err})
                })
            }

        })
        .catch((err) => {
            dispatch({type: "FETCH_MESSAGES_REJECTED", data: err})
        })
    }
}

export function checkChatStatus(obj) {
    return function(dispatch, getState) {

        const state = getState();

        if (state.chatwidget.getIn(['syncStatus','status']) == true) {
            return;
        }

        dispatch({type: "CHECK_CHAT_STATUS_STARTED"});

        axios.post(window.lhcChat['base_url'] + "widgetrestapi/checkchatstatus", obj)
        .then((response) => {
            if (response.data.deleted) {
                helperFunctions.sendMessageParent('endChat',[{'sender' : 'endButton'}]);
            } else {
                dispatch({type: "CHECK_CHAT_STATUS_FINISHED", data: response.data});
                helperFunctions.emitEvent('chat.check_status',[response.data, dispatch, getState]);
            }
        })
        .catch((err) => {
            dispatch({type: "CHECK_CHAT_STATUS_REJECTED", data: err})
        })
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
            axios.get(window.lhcChat['base_url'] + "chat/userclosechat/" +  state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']));
        } else if (state.chatwidget.getIn(['proactive','has']) === true && window.lhcChat['mode'] == 'popup' && window.opener) {
            hideInvitation()(dispatch, getState);
        }
    }
}

export function addMessage(obj) {
    return function(dispatch, getState) {
        axios.post(window.lhcChat['base_url'] + "widgetrestapi/addmsguser", obj)
            .then((response) => {
                dispatch({type: "ADD_MESSAGES_SUBMITTED", data: response.data});
                fetchMessages({'theme' : obj.theme, 'chat_id' : obj.id, 'lmgsid' : obj.lmgsid, 'hash' : obj.hash})(dispatch, getState);
            })
            .catch((err) => {
                dispatch({type: "ADD_MESSAGES_REJECTED", data: err})
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
            axios.post(window.lhcChat['base_url'] + "chat/usertyping/" + state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']) + '/' + status, {'msg' : msg})
                .then((response) => {
            });
        }
    }
}
