import { FETCH_NODE_GROUP_TRIGGERS, FETCH_NODE_GROUP_TRIGGERS_FULFILLED, FETCH_NODE_GROUP_TRIGGERS_REJECTED } from "../constants/action-types";

// https://github.com/learncodeacademy/react-js-tutorials/blob/master/5-redux-react/src/js/components/Layout.js
// https://github.com/valentinogagliardi/minimal-react-redux-webpack/blob/master/src/js/components/Form.js

const nodeGroupTriggerReducer = (state = {
    nodegrouptriggers : {},
    fetching: false,
    fetched: false,
    error: null
}, action) => {
    switch (action.type) {

        case FETCH_NODE_GROUP_TRIGGERS : {
            return {
                ...state,
                fetching: true
        };
        }

        case FETCH_NODE_GROUP_TRIGGERS_FULFILLED: {

            const newTweets = [...state.nodegrouptriggers]
            newTweets[action.group_id] = action.payload;

            return {
                ...state,
                fetching: false,
                fetched: true,
                nodegrouptriggers: newTweets,
            }
        }

        case FETCH_NODE_GROUP_TRIGGERS_REJECTED: {
            return {
                ...state,
                fetching: false,
                error: action.payload
            }
        }

        default:
            return state;
    }
};

export default nodeGroupTriggerReducer;