import {
    FETCH_TRIGGER_RESPONSE,
    FETCH_TRIGGER_RESPONSE_FULFILLED,
    FETCH_TRIGGER_RESPONSE_REJECTED,
    UPDATE_TRIGGER_NAME,
    UPDATE_TRIGGER_TYPE,
    ADD_TRIGGER_RESPONSE,
    HANDLE_CONTENT_CHANGE,
    REMOVE_TRIGGER,
    CANCEL_TRIGGER,
    UPDATE_TRIGGER_EVENT,
    ADD_TRIGGER_EVENT_FULFILLED,
    DELETE_TRIGGER_EVENT,
    HANDLE_ADD_QUICK_REPLY,
    HANDLE_ADD_QUICK_REPLY_REMOVE,
    REMOVE_TRIGGER_RESPONSE,
    INIT_BOT_FULFILLED,
    INIT_BOT_ARGUMENTS_FULFILLED,
    ADD_PAYLOAD_TRIGGERS_FULFILLED,
    UPDATE_PAYLOADS_FULFILLED,
    ADD_SUBELEMENT,
    REMOVE_SUBELEMENT,
    MOVE_UP_SUBELEMENT,
    MOVE_DOWN_SUBELEMENT,
    MOVE_UP,
    MOVE_DOWN,
    LOAD_USE_CASES_TRIGGER_FULFILLED,
    INIT_BOT_REST_API_METHODS
} from "../constants/action-types";

import {fromJS} from 'immutable';
import shortid from 'shortid';

const initialState = fromJS({
    currenttrigger : {},
    payloads : [],
    rest_api_calls : [],
    fetching: false,
    fetched: false,
    error: null
})

const nodeGroupTriggerReducer = (state = initialState, action) => {
    switch (action.type) {

        case FETCH_TRIGGER_RESPONSE_FULFILLED: {
            return state.set('currenttrigger', fromJS(action.payload));
        }

        case FETCH_TRIGGER_RESPONSE_REJECTED: {
            return state.set('fetching', false).set('error',fromJS(action.payload));
        }

        case UPDATE_TRIGGER_NAME: {
            return state.setIn(['currenttrigger','name'], action.payload.get('name'));
        }

        case UPDATE_TRIGGER_TYPE: {
            return state.setIn(['currenttrigger','actions',action.payload['id'],'type'], action.payload['type']);
        }

        case ADD_TRIGGER_RESPONSE: {
            return state.updateIn(['currenttrigger','actions'], actions => actions.push(fromJS({'_id' : shortid.generate(), 'type' : 'text', content : {'text' : ''}})));
        }

        case REMOVE_TRIGGER_RESPONSE: {
            return state.deleteIn(['currenttrigger','actions',action.payload.id]);
        }

        case HANDLE_CONTENT_CHANGE: {
            return state.setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path),action.payload.value);
        }

        case ADD_SUBELEMENT:{

            if (!state.getIn(['currenttrigger','actions',action.payload.id]).hasIn(action.payload.path)) {
                return state.setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path),fromJS([action.payload.default]));
            }

            return state.updateIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path), elements => elements.push(fromJS(action.payload.default)));
         }

         case REMOVE_SUBELEMENT:{
            return state.deleteIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path));
         }

         case MOVE_UP_SUBELEMENT: {

             let source = state.getIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path)).get(action.payload.index);
             let destination = state.getIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path)).get(action.payload.index-1);

             return state.setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path).concat([action.payload.index]),destination).setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path).concat([action.payload.index-1]),source);
         }

         case MOVE_DOWN_SUBELEMENT:{
             let source = state.getIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path)).get(action.payload.index);
             let destination = state.getIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path)).get(action.payload.index+1);

             return state.setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path).concat([action.payload.index]),destination).setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path).concat([action.payload.index+1]),source);
         }

        case MOVE_UP: {

            let source = state.getIn(['currenttrigger','actions']).get(action.payload.index);
            let destination = state.getIn(['currenttrigger','actions']).get(action.payload.index-1);

            return state.setIn(['currenttrigger','actions'].concat([action.payload.index]),destination).setIn(['currenttrigger','actions'].concat([action.payload.index-1]),source);
        }

        case MOVE_DOWN:{
            let source = state.getIn(['currenttrigger','actions']).get(action.payload.index);
            let destination = state.getIn(['currenttrigger','actions']).get(action.payload.index+1);

            return state.setIn(['currenttrigger','actions'].concat([action.payload.index]),destination).setIn(['currenttrigger','actions'].concat([action.payload.index+1]),source);
        }

        case LOAD_USE_CASES_TRIGGER_FULFILLED:{
            return state.setIn(['currenttrigger','use_cases'],fromJS(action.payload));
        }

        case HANDLE_ADD_QUICK_REPLY: {

            if (!state.getIn(['currenttrigger','actions',action.payload.id,'content']).has('quick_replies')) {
                return state.setIn(['currenttrigger','actions',action.payload.id,'content','quick_replies'],fromJS([{'_id': shortid.generate(), 'type' : 'button', content : {'name' : '','payload' : ''}}]));
            }

            return state.updateIn(['currenttrigger','actions',action.payload.id,'content','quick_replies'], quick_replies => quick_replies.push(fromJS({'_id': shortid.generate(), 'type' : 'button', content : {'name' : '','payload' : ''}})));
        }

        case HANDLE_ADD_QUICK_REPLY_REMOVE: {
            const actions = ['currenttrigger','actions',action.payload.id];
            return state.deleteIn([...actions, ...action.payload.path]);
        }

        case REMOVE_TRIGGER: {
            if (state.getIn(['currenttrigger','id']) == action.payload.get('id')){
                return state.set('currenttrigger',fromJS({}));
            }

            return state;
        }

        case CANCEL_TRIGGER: {
            if (state.getIn(['currenttrigger','id']) == action.payload.get('id')){
                return state.set('currenttrigger',fromJS({}));
            }

            return state;
        }

        case UPDATE_TRIGGER_EVENT: {
            const indexOfListingToUpdate = state.getIn(['currenttrigger','events']).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['currenttrigger','events',indexOfListingToUpdate], action.payload);
        }

        case ADD_TRIGGER_EVENT_FULFILLED: {
            return state.updateIn(['currenttrigger','events'], events => events.push(fromJS(action.payload)));
        }

        case DELETE_TRIGGER_EVENT: {
            const indexOfListingToUpdate = state.getIn(['currenttrigger','events']).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.deleteIn(['currenttrigger','events',indexOfListingToUpdate]);
        }

        case INIT_BOT_FULFILLED : {
            return state.set('payloads',fromJS(action.payload['payloads'])).set('rest_api_calls',fromJS(action.payload['rest_api_calls']));
        }

        case INIT_BOT_REST_API_METHODS: {
            // Find Rest API Index we will be updating
            const indexOfListingToUpdate = state.getIn(['rest_api_calls']).findIndex(listing => {
                return listing.get('id') === action.payload['id'];
            });

            return state.setIn(['rest_api_calls',indexOfListingToUpdate,'methods'],fromJS(action.payload['methods']));
        }
        
        case INIT_BOT_ARGUMENTS_FULFILLED : {
            return state.set('arguments',fromJS(action.payload['arguments']));
        }

        case ADD_PAYLOAD_TRIGGERS_FULFILLED: {
            return state.set('payloads',fromJS(action.payload['payloads']));
        }

        case UPDATE_PAYLOADS_FULFILLED: {
            return state.set('payloads',fromJS(action.payload['payloads']));
        }

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;