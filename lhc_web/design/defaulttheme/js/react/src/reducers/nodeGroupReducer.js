import { FETCH_NODE_GROUPS, FETCH_NODE_GROUPS_FULFILLED, FETCH_NODE_GROUPS_REJECTED, ADD_GROUP_FULFILLED, UPDATE_GROUP_FULFILLED } from "../constants/action-types";

// https://github.com/learncodeacademy/react-js-tutorials/blob/master/5-redux-react/src/js/components/Layout.js
// https://github.com/valentinogagliardi/minimal-react-redux-webpack/blob/master/src/js/components/Form.js

const nodeGroupReducer = (state = {
    nodegroups : [],
    fetching: false,
    fetched: false,
    error: null
    }, action) => {
    switch (action.type) {

        case FETCH_NODE_GROUPS : {
            return {
                ...state,
                fetching: true
            };
        }

        case FETCH_NODE_GROUPS_FULFILLED: {
             return {
                     ...state,
                     fetching: false,
                     fetched: true,
                     nodegroups: action.payload,
            }
        }

        case ADD_GROUP_FULFILLED: {
            return {
                ...state,
                fetching: false,
                fetched: true,
                nodegroups: [...state.nodegroups, action.payload],
            }
        }

        case UPDATE_GROUP_FULFILLED: {

            const { id, name } = action.payload;
            const newTweets = [...state.nodegroups]
            const tweetToUpdate = newTweets.findIndex(tweet => tweet.id === id)
            newTweets[tweetToUpdate] = action.payload;

            return {
                ...state,
                fetching: false,
                fetched: true,
                nodegroups: newTweets,
            }
        }

        case FETCH_NODE_GROUPS_REJECTED: {
            return {
                ...state,
                fetching: false,
                error: action.payload
            };
        }

        default:
            return state;
    }
};

export default nodeGroupReducer;