import {
    FETCH_TRIGGER_RESPONSE,
    FETCH_TRIGGER_RESPONSE_FULFILLED,
    FETCH_TRIGGER_RESPONSE_REJECTED,
    UPDATE_TRIGGER_NAME,
    UPDATE_TRIGGER_TYPE,
    ADD_TRIGGER_RESPONSE,
    HANDLE_CONTENT_CHANGE,
    REMOVE_TRIGGER
} from "../constants/action-types";
import {fromJS} from 'immutable';

const initialState = fromJS({
    currenttrigger : {},
    fetching: false,
    fetched: false,
    error: null
})

const nodeGroupTriggerReducer = (state = initialState, action) => {
    switch (action.type) {

        case FETCH_TRIGGER_RESPONSE : {
            return state.set('fetching', true).setIn(['currenttrigger','id'], action.payload.triggerid);
        }

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
            return state.updateIn(['currenttrigger','actions'], actions => actions.push(fromJS({'type' : 'text', content : {'text' : 'Write your response here!'}})));
        }

        case HANDLE_CONTENT_CHANGE: {
            return state.setIn(['currenttrigger','actions',action.payload.id].concat(action.payload.path),action.payload.value);
        }

        case REMOVE_TRIGGER: {
            if (state.getIn(['currenttrigger','id']) == action.payload.get('id')){
                return state.set('currenttrigger',fromJS({}));
            }

            return state;
        }

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;