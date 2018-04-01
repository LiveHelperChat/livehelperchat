import { FETCH_NODE_GROUP_TRIGGERS, FETCH_NODE_GROUP_TRIGGERS_FULFILLED, FETCH_NODE_GROUP_TRIGGERS_REJECTED, UPDATE_TRIGGER_NAME } from "../constants/action-types";
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

        case UPDATE_TRIGGER_NAME: {

            const indexOfListingToUpdate = state.get('nodegrouptriggers').get( action.payload.get('group_id') ).findIndex(listing => {
                    return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegrouptriggers', action.payload.get('group_id'), indexOfListingToUpdate, 'name'], action.payload.get('name'));
        }

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;