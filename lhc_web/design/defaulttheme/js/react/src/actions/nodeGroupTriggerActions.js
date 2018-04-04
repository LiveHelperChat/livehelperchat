import axios from "axios";

export function fetchNodeGroupTriggers(groupId) {
    return function(dispatch) {
        dispatch({type: "FETCH_NODE_GROUP_TRIGGERS"});

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/nodegrouptriggers/" + groupId)
            .then((response) => {
            dispatch({type: "FETCH_NODE_GROUP_TRIGGERS_FULFILLED", payload: response.data, group_id: groupId})
    })
    .catch((err) => {
            dispatch({type: "FETCH_NODE_GROUP_TRIGGERS_REJECTED", payload: err})
        })
    }
}

export function fetchNodeGroupTriggerAction(triggerId) {
    return function(dispatch) {
        dispatch({type: "FETCH_TRIGGER_RESPONSE", payload : {triggerid : triggerId}});

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/nodetriggeractions/" + triggerId)
        .then((response) => {
                dispatch({type: "FETCH_TRIGGER_RESPONSE_FULFILLED", payload: response.data})
        })
        .catch((err) => {
                dispatch({type: "FETCH_TRIGGER_RESPONSE_REJECTED", payload: err})
            })
        }
}

export function updateTriggerType(obj) {
    return function(dispatch) {
        dispatch({type: "UPDATE_TRIGGER_TYPE", payload : obj});

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        /*axios.get(WWW_DIR_JAVASCRIPT + "genericbot/nodetriggeractions/" + triggerId)
            .then((response) => {
            dispatch({type: "FETCH_TRIGGER_RESPONSE_FULFILLED", payload: response.data})
    })
    .catch((err) => {
            dispatch({type: "FETCH_TRIGGER_RESPONSE_REJECTED", payload: err})
        })*/
    }
}

export function updateTriggerName(obj) {
    return function(dispatch) {
        dispatch({type: "UPDATE_TRIGGER_NAME", payload : obj});

        /*
          http://rest.learncode.academy is a public test server, so another user's experimentation can break your tests
          If you get console errors due to bad data:
          - change "reacttest" below to any other username
          - post some tweets to http://rest.learncode.academy/api/yourusername/tweets
        */
        /*axios.get(WWW_DIR_JAVASCRIPT + "genericbot/nodetriggeractions/" + triggerId)
            .then((response) => {
            dispatch({type: "FETCH_TRIGGER_RESPONSE_FULFILLED", payload: response.data})
    })
    .catch((err) => {
            dispatch({type: "FETCH_TRIGGER_RESPONSE_REJECTED", payload: err})
        })*/
    }
}

export function addTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "ADD_TRIGGER", payload : obj});
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/addtrigger/" + obj.id)
                .then((response) => {
                dispatch({type: "ADD_TRIGGER_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "ADD_TRIGGER_REJECTED", payload: err})
            })
        }
}

export function saveTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SAVE_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/actions", obj.toJS())
                .then((response) => {
                dispatch({type: "SAVE_TRIGGER_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SAVE_TRIGGER_REJECTED", payload: err})
            })
        }
}

export function removeTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "REMOVE_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/removetrigger/" + obj.get('id'))
                .then((response) => {
                dispatch({type: "REMOVE_TRIGGER_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "REMOVE_TRIGGER_REJECTED", payload: err})
        })
    }
}

