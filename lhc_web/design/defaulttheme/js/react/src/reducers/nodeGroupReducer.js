import { DELETE_TRIGGER_GROUP, FETCH_NODE_GROUPS, FETCH_NODE_GROUPS_FULFILLED, FETCH_NODE_GROUPS_REJECTED, ADD_GROUP_FULFILLED, UPDATE_GROUP_FULFILLED } from "../constants/action-types";
import {fromJS} from 'immutable';

// https://github.com/learncodeacademy/react-js-tutorials/blob/master/5-redux-react/src/js/components/Layout.js
// https://github.com/valentinogagliardi/minimal-react-redux-webpack/blob/master/src/js/components/Form.js

const initialState = fromJS({
    nodegroups : [],
    fetching: false,
    fetched: false,
    error: null
})

const applyFn = (state, fn) => fn(state)
export const pipe = (fns, state) => state.withMutations(s => fns.reduce(applyFn, s))

const nodeGroupReducer = (state = initialState, action) => {
    switch (action.type) {

        case FETCH_NODE_GROUPS : {
            return state.set('fetching',true);
        }

        case FETCH_NODE_GROUPS_FULFILLED: {
            return state
                .set('fetching',false)
                .set('fetched',true)
                .set('nodegroups',fromJS(action.payload));
        }

        case ADD_GROUP_FULFILLED: {
            return state
                .set('fetched',true)
                .set('fetching',false)
                .update('nodegroups', nodegroups => nodegroups.push(fromJS(action.payload)));
        }

        case DELETE_TRIGGER_GROUP: {
            const indexOfListingToUpdate = state.get('nodegroups').findIndex(listing => {
                return listing.get('id') === action.payload.groupId.id;
            });

            return state.deleteIn(['nodegroups', indexOfListingToUpdate]);
        }

        case UPDATE_GROUP_FULFILLED: {
            const indexOfListingToUpdate = state.get('nodegroups').findIndex(listing => {
                  return listing.get('id') === action.payload.get('id');
            });

            return state.setIn(['nodegroups', indexOfListingToUpdate], action.payload);
        }

        case FETCH_NODE_GROUPS_REJECTED: {
            return state.set('fetching',false).set('error',action.payload);
        }

        default:
            return state;
    }
};

export default nodeGroupReducer;