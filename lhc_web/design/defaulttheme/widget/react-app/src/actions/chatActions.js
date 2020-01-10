import axios from "axios";
import { helperFunctions } from "../lib/helperFunctions";

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

export function endChat(obj) {
    return function(dispatch, getState) {
        axios.get(window.lhcChat['base_url'] + "/chat/chatwidgetclosed/(eclose)/t/(hash)/" + obj['chat']['id'] +'_'+ obj['chat']['hash'] + '/(vid)/' + obj['vid'])
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

export function voteAction(obj) {
    return axios.post(window.lhcChat['base_url'] + "/chat/voteaction/" + obj.id + '/' + obj.hash + '/' + obj.type)
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

        axios.post(window.lhcChat['base_url'] + "/notifications/subscribe" +args, {'data' : payload})
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
        return axios.post(window.lhcChat['base_url'] + "/genericbot/"+(typeParams.mainType ? typeParams.mainType : "buttonclicked")+"/" + state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']) + typeParams.type, params)
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
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/onlinesettings", obj)
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
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/onlinesettings", obj)
        .then((response) => {
            dispatch({type: "ONLINE_FIELDS_UPDATED", data: response.data})
        })
        .catch((err) => {
            dispatch({type: "ONLINE_FIELDS_REJECTED", data: err})
        })
    }
}

export function getCaptcha(dispatch) {
    var date = new Date();
    var timestamp = Math.round(date.getTime()/1000);
    axios.post(window.lhcChat['base_url'] + "/captcha/captchastring/fake/" + timestamp)
    .then((response) => {
        dispatch({type: "captcha", data: {'hash' : response.data.result, 'ts' : timestamp}})
    });
}

export function submitOnlineForm(obj) {
    return function(dispatch) {
        dispatch({type: "ONLINE_SUBMITTING"});
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/submitonline", obj)
        .then((response) => {

            // If validation contains invalid captcha update it instantly
            if (response.data.success === false && response.data.errors.captcha) {
                getCaptcha(dispatch);
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
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/submitoffline", obj)
        .then((response) => {
            dispatch({type: "OFFLINE_SUBMITTED", data: response.data})
        })
        .catch((err) => {
            dispatch({type: "OFFLINE_SUBMITT_REJECTED", data: err})
        })
    }
}

export function initChatUI(obj) {
    return function(dispatch) {
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/initchat", obj)
        .then((response) => {
            dispatch({type: "INIT_CHAT_SUBMITTED", data: response.data})
        })
        .catch((err) => {
            dispatch({type: "INIT_CHAT_REJECTED", data: err})
        })
    }
}

function processResponseCheckStatus(response, getState) {
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

                helperFunctions.sendMessageParent('screenshot',[window.lhcChat['base_url'] + '/file/storescreenshot' + append]);
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

        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/fetchmessages", obj)
        .then((response) => {
            dispatch({type: "FETCH_MESSAGES_SUBMITTED", data: response.data});

            processResponseCheckStatus(response.data, getState);

            if (response.data.cs || (response.data.closed && response.data.closed === true)) {
                axios.post(window.lhcChat['base_url'] + "/widgetrestapi/checkchatstatus", obj)
                .then((response) => {
                    if (response.data.deleted) {
                        //window.lhcChat.eventEmitter.emitEvent('endChat', [{'sender' : 'endButton'}]);
                    } else {
                        dispatch({type: "CHECK_CHAT_STATUS_FINISHED", data: response.data})
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
    return function(dispatch) {
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/checkchatstatus", obj)
        .then((response) => {
            if (response.data.deleted) {
                helperFunctions.sendMessageParent('endChat',[{'sender' : 'endButton'}]);
            } else {
                dispatch({type: "CHECK_CHAT_STATUS_FINISHED", data: response.data})
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
        if (state.chatwidget.hasIn(['chatData','id']) && state.chatwidget.hasIn(['chatData','hash'])) {
            axios.get(window.lhcChat['base_url'] + "/chat/userclosechat/" +  state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']));
        }
    }
}

export function addMessage(obj) {
    return function(dispatch, getState) {
        axios.post(window.lhcChat['base_url'] + "/widgetrestapi/addmsguser", obj)
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
        axios.post(window.lhcChat['base_url'] + "/chat/usertyping/" + state.chatwidget.getIn(['chatData','id']) + '/' + state.chatwidget.getIn(['chatData','hash']) + '/' + status, {'msg' : msg})
            .then((response) => {
            });
    }
}

export function focusChange(status) {
    return function(dispatch) {
        helperFunctions.sendMessageParent('focusChanged',[status]);
    }
}