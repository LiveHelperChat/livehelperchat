import { SHOWN_WIDGET, CLOSED_WIDGET, IS_MOBILE, IS_ONLINE, OFFLINE_FIELDS_UPDATED, ONLINE_SUBMITTED, ENDED_CHAT, SOUND_ENABLED } from "../constants/action-types";
import {fromJS} from 'immutable';
import { helperFunctions } from "../lib/helperFunctions";

const initialState = fromJS({
    loadedCore: false, // Was the core loaded. IT's set after all initial attributes are loaded and app can proceed futher.
    shown: true,
    isMobile: false,
    isOnline: false,
    isChatting: false,
    newChat: true,
    theme: null,
    mode: 'widget',
    overrides: [], // we store here extensions flags. Like do we override typing monitoring so we send every request
    department: [],
    jsVars: [],
    // Are we syncing chat messages now
    syncStatus : {msg: false, status : false},
    offlineData: {},
    onlineData: {'fetched' : false},
    customData: {'fields' : []},
    attr_prefill: [],
    chat_ui : {}, // Settings from themes, UI
    chat_ui_state : {'confirm_close': 0, 'show_survey' : 0}, // Settings from themes, UI we store our present state here
    processStatus : 0,
    chatData : {}, // Stores only chat id and hash
    chatLiveData : {'uid' : 0, 'error' : '','lmsgid' : 0, 'operator' : '', 'messages' : [], 'closed' : false, 'ott' : '', 'status_sub' : 0, 'status' : 0}, // Stores live chat data
    chatStatusData : {},
    usersettings : {soundOn : false},
    vid: null,
    base_url: null,
    initClose : false,
    // Was initialized data loaded
    initLoaded : false,
    msgLoaded : false,
    proactive : {'pending' : false, 'has' : false, data : {}}, // Proactive invitation data holder
    lang : '',
    bot_id : '',
    ses_ref : null,
    captcha : {},
    validationErrors : {},
})
// Prrocess Status
// 0 Not submitted
// 1 Submitting
// 2 Submitted

const applyFn = (state, fn) => fn(state)
export const pipe = (fns, state) => state.withMutations(s => fns.reduce(applyFn, s))

const chatWidgetReducer = (state = initialState, action) => {

    switch (action.type) {

        case CLOSED_WIDGET : {
            if (state.get('isChatting') === false) {
                state = state.set('processStatus',0);
            }
            return state.set('shown',false);
        }

        case 'loadedCore': {
            return state.set('loadedCore',true);
        }

        case 'lang': {
            return state.set('lang',action.data);
        }

        case 'bot_id': {
            return state.set('bot_id',action.data);
        }

        case 'widgetStatus': {
            // Visitor clicked widget and it has invitation shown. We leave invitation mode
            if (action.data == true && state.getIn(['proactive','pending']) === true) {
                state = state.setIn(['proactive','pending'],false);
            }
            return state.set('shown',action.data);
        }

        case 'jsVars': {
            return state.set('jsVars',action.data);
        }

        // Proactive invitation has arrived
        case 'PROACTIVE': {
            return state.set('proactive',{'pending' : (state.get('shown') === false ? true : false), 'has': true, data : action.data});
        }

        // Visitor clicks hide invitation
        case 'HIDE_INVITATION': {
            return state.set('proactive',{'pending': false, 'has': false});
        }

        // Visitor was interested and clicked invitation tooltip itself.
        case 'FULL_INVITATION': {
            return state.setIn(['proactive','pending'],false);
        }

        case SOUND_ENABLED : {
            return state.setIn(['usersettings','soundOn'],action.data);
        }

        case ENDED_CHAT : {
            return state.set('shown',false)
                .set('processStatus', 0)
                .set('isChatting',false)
                .set('chatData',fromJS({}))
                .set('chatLiveData',fromJS({'uid':0, 'status' : 0, 'status_sub' : 0, 'uw' : false, 'ott' : '', 'closed' : false, 'lmsgid' : 0, 'operator' : '', 'messages' : []}))
                .set('chatStatusData',fromJS({}))
                .set('chat_ui_state',fromJS({'confirm_close': 0, 'show_survey' : 0}))
                .set('initClose',false)
                .set('initLoaded',false);
        }

        case 'chat_status_changed':
        {
            return state.setIn(['chatLiveData','ott'],action.data.text);
        }

        case IS_MOBILE : {
            return state.set('isMobile',action.data);
        }

        case IS_ONLINE : {
            return state.set('isOnline',action.data);
        }

        case OFFLINE_FIELDS_UPDATED : {
            return state.set('offlineData',fromJS(action.data));
        }

        case 'captcha': {
            return state.set('captcha',fromJS(action.data));
        }
        
        case 'CHAT_SESSION_REFFERER': {
            return state.set('ses_ref',action.data.ref);
        }

        case 'department' : {
            return state.set('department',fromJS(action.data));
        }

        case 'mode' : {
            return state.set('mode',fromJS(action.data));
        }

        case 'theme' : {
            return state.set('theme',action.data);
        }

        case 'CHAT_ADD_OVERRIDE' : {
            return state.update('overrides',list => list.push(action.data));
        }

        case 'CHAT_REMOVE_OVERRIDE': {
            return state.update('overrides',list => list.filter(item => item != action.data));
        }

        case 'base_url': {
            return state.set('base_url',action.data);
        }

        case ONLINE_SUBMITTED : {
            if (action.data.success === true) {
                helperFunctions.sendMessageParent('chatStarted',[action.data.chatData,state.get('mode')]);
                
                // If we are in popup mode and visitor refreshes page, remember chat
                if (state.get('mode') == 'popup') {
                    helperFunctions.setSessionStorage('lhc_chat',JSON.stringify(action.data.chatData))
                }

                return state.set('processStatus', 2).set('isChatting',true).set('chatData',fromJS(action.data.chatData)).set('validationErrors',fromJS({}));;
            } else {
                return state.set('validationErrors',fromJS(action.data.errors)).set('processStatus',0);
            }
        }

        case 'OFFLINE_SUBMITTED' : {
            if (action.data.success === true) {
                return state.set('processStatus', 2).set('validationErrors',fromJS({}));
            } else {
                return state.set('validationErrors',fromJS(action.data.errors)).set('processStatus',0);
            }
        }

        case 'INIT_CLOSE': {
            return state.set('initClose',true);
        }

        // If we receive chat id and hash from parent
        case 'CHAT_ALREADY_STARTED': {
            return state.set('processStatus', 2)
                .set('isChatting',true)
                .set('newChat',false)
                .set('chatData',fromJS(action.data));
        }

        case 'OFFLINE_SUBMITTING' : {
            return state.set('processStatus', 1);
        }

        case 'CHAT_SET_VID' : {
            return state.set('vid', action.data);
        }

        case 'ONLINE_SUBMITTING' : {
            return state.set('processStatus', 1);
        }

        case 'UI_STATE':{
            return state.setIn(['chat_ui_state',action.data.attr],action.data.val);
        }

        case 'INIT_CHAT_SUBMITTED' : {
            return state.setIn(['chatLiveData','operator'], action.data.operator)
                .set('chat_ui', state.get('chat_ui').merge(fromJS(action.data.chat_ui)))
                .setIn(['chatLiveData','status_sub'], action.data.status_sub)
                .setIn(['chatLiveData','status'], action.data.status)
                .set('initLoaded', true)
                .setIn(['chatLiveData','closed'], action.data.closed && action.data.closed === true);
        }

        case 'FETCH_MESSAGES_STARTED': {
            return state.setIn(['syncStatus','msg'],true);
        }

        case 'CHECK_CHAT_STATUS_STARTED': {
            return state.setIn(['syncStatus','status'],true);
        }

        case 'FETCH_MESSAGES_REJECTED': {
            return state.setIn(['syncStatus','msg'],false);
        }

        case 'CHECK_CHAT_STATUS_REJECTED': {
            return state.setIn(['syncStatus','status'],false);
        }

        case 'FETCH_MESSAGES_SUBMITTED' : {

            if (action.data.closed_arg && action.data.closed_arg.survey_id) {
                state = state.setIn(['chat_ui','survey_id'],action.data.closed_arg.survey_id);
            }

            if (action.data.messages.length > 0) {
                state = state.setIn(['chatLiveData','messages'], state.getIn(['chatLiveData','messages']).concat(fromJS(action.data.messages)))
                    .setIn(['chatLiveData','uw'], action.data.uw && action.data.uw === true)
                    .setIn(['chatLiveData','lmsgid'],action.data.message_id);
            }

            if (!state.get('overrides').contains('typing')) {
                state = state.setIn(['chatLiveData','ott'], action.data.ott);
            }

            return state.setIn(['chatLiveData','status_sub'], action.data.status_sub)
                .setIn(['chatLiveData','status'], action.data.status)
                .setIn(['syncStatus','msg'], false)
                .set('msgLoaded', true)
                .setIn(['chatLiveData','closed'], action.data.closed && action.data.closed === true)
        }

        case 'CHECK_CHAT_STATUS_FINISHED' : {
            return state.set('chatStatusData',fromJS(action.data))
                .setIn(['chatLiveData','closed'], action.data.closed && action.data.closed === true || state.getIn(['chatLiveData','closed']))
                .setIn(['chatLiveData','status'], action.data.status)
                .setIn(['chatLiveData','uid'], action.data.uid)
                .setIn(['syncStatus','status'], false)
                .setIn(['chatLiveData','ru'], action.data.ru ? action.data.ru : null)
                .setIn(['chatLiveData','status_sub'], action.data.status_sub);
        }

        case 'ONLINE_FIELDS_UPDATED' : {
            return state.set('onlineData', fromJS({'fetched' : true, 'fields_visible': action.data.fields_visible, 'fields' : action.data.fields, 'department' : action.data.department})).set('chat_ui', state.get('chat_ui').merge(fromJS(action.data.chat_ui)));
        }

        case 'CHAT_UI_UPDATE' : {
            return state.set('chat_ui',state.get('chat_ui').merge(fromJS(action.data)));
        }

        case 'CUSTOM_FIELDS': {
            return state.set('customData', fromJS({'fields' : action.data}));
        }

        case 'attr_prefill': {
            return state.set('attr_prefill', action.data);
        }

        case 'CUSTOM_FIELDS_ITEM': {
            return state.setIn(['customData','fields',action.data.id,'value'], action.data.value);
        }

        case 'ADD_MESSAGES_SUBMITTED': {
            return state.setIn(['chatLiveData','error'], action.data.r);
        }

        default:
            return state;
    }
};

export default chatWidgetReducer;