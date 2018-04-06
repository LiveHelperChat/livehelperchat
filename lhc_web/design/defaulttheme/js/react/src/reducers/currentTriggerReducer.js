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
    DELETE_TRIGGER_EVENT
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
            return state.updateIn(['currenttrigger','actions'], actions => actions.push(fromJS({'type' : 'text', content : {'text' : ''}})));
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

            console.log(action.payload.get('id'));

            return state.deleteIn(['currenttrigger','events',indexOfListingToUpdate]);
        }

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;