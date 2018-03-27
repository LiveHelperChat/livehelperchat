import axios from "axios";

export function fetchNodeGroups() {
    return function(dispatch) {
        dispatch({type: "FETCH_NODE_GROUPS"});

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/nodegroups")
            .then((response) => {
            dispatch({type: "FETCH_NODE_GROUPS_FULFILLED", payload: response.data})
        })
        .catch((err) => {
                dispatch({type: "FETCH_NODE_GROUPS_REJECTED", payload: err})
            })
        }
}

export function addNodeGroup() {
    return function(dispatch) {
        dispatch({type: "FETCH_NODE_GROUPS"});

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/addgroup")
            .then((response) => {
            dispatch({type: "ADD_GROUP_FULFILLED", payload: response.data})
        })
        .catch((err) => {
                dispatch({type: "ADD_GROUP_REJECTED", payload: err})
            })
        }
}

export function updateNodeGroup(obj) {
    return function(dispatch) {

        dispatch({type: "UPDATE_GROUP_FULFILLED", payload: obj})

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        /*axios.get(WWW_DIR_JAVASCRIPT + "genericbot/addgroup")
            .then((response) => {
            dispatch({type: "UPDATE_GROUP_FULFILLED", payload: response.data})
        })
        .catch((err) => {
                dispatch({type: "ADD_GROUP_REJECTED", payload: err})
            })*/
        }
}


