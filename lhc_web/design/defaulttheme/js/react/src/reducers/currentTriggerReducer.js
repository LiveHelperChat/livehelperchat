import { FETCH_TRIGGER_RESPONSE, FETCH_TRIGGER_RESPONSE_FULFILLED, FETCH_TRIGGER_RESPONSE_REJECTED, UPDATE_TRIGGER_NAME } from "../constants/action-types";
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

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;