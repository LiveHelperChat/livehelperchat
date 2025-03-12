import { SET_TRIGGER_POSITON, SET_IN_PROGRESS_TRIGGER, SET_DEFAULT_TRIGGER, SET_GROUP_TRIGGER_EVENT, SET_DEFAULT_ALWAYS_TRIGGER, REMOVE_TRIGGER, SET_DEFAULT_UNKNOWN_TRIGGER, SET_DEFAULT_UNKNOWN_BTN_TRIGGER, SET_AS_ARGUMENT_TRIGGER, FETCH_NODE_GROUP_TRIGGERS, FETCH_NODE_GROUP_TRIGGERS_FULFILLED, FETCH_NODE_GROUP_TRIGGERS_REJECTED, UPDATE_TRIGGER_NAME, ADD_TRIGGER_FULFILLED, SAVE_TRIGGER } from "../constants/action-types";
import {fromJS} from 'immutable';

// https://github.com/learncodeacademy/react-js-tutorials/blob/master/5-redux-react/src/js/components/Layout.js
// https://github.com/valentinogagliardi/minimal-react-redux-webpack/blob/master/src/js/components/Form.js

const initialState = fromJS({
    nodegrouptriggers : {},
    fetching: false,
    fetched: false,
    error: null
})

const nodeGroupTriggerReducer = (state = initialState, action) => {
    switch (action.type) {

        case FETCH_NODE_GROUP_TRIGGERS : {
            return state.set('fetching', false);
        }

        case FETCH_NODE_GROUP_TRIGGERS_FULFILLED: {
            return state.setIn(['nodegrouptriggers',action.group_id], fromJS(action.payload));
        }

        case FETCH_NODE_GROUP_TRIGGERS_REJECTED: {
            return state.set('fetching', false).set('error',fromJS(action.payload));
        }

        case SAVE_TRIGGER: {

            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                    return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'name'], action.payload.get('name'));
        }

        case REMOVE_TRIGGER: {

            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.deleteIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate]);
        }

        case SET_GROUP_TRIGGER_EVENT: {

            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.group_id ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            let newTrigger = state.getIn(['nodegrouptriggers', action.group_id, indexOfListingToUpdate]).set('group_id',action.payload.get('group_id'));
            
            return state.deleteIn(['nodegrouptriggers', action.group_id, indexOfListingToUpdate]).
                   updateIn(['nodegrouptriggers',action.payload.get('group_id')], triggers => triggers.push(newTrigger));
        }

        case SET_DEFAULT_TRIGGER: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'default'], action.payload.get('default'));
        }

        case SET_DEFAULT_UNKNOWN_TRIGGER: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'default_unknown'], action.payload.get('default_unknown'));
        }

        case SET_IN_PROGRESS_TRIGGER: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'in_progress'], action.payload.get('in_progress'));
        }

        case SET_AS_ARGUMENT_TRIGGER: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'as_argument'], action.payload.get('as_argument'));
        }

        case SET_DEFAULT_UNKNOWN_BTN_TRIGGER: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'default_unknown_btn'], action.payload.get('default_unknown_btn'));
        }

        case SET_TRIGGER_POSITON: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });
            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'pos'], action.payload.get('pos'));
        }

        case SET_DEFAULT_ALWAYS_TRIGGER: {
            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'default_always'], action.payload.get('default_always'));
        }

        case ADD_TRIGGER_FULFILLED: {
            return state.updateIn(['nodegrouptriggers',action.payload.group_id], triggers => triggers.push(fromJS(action.payload)));
        }

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;