import axios from "axios";

axios.defaults.headers.common['X-CSRFToken'] = confLH.csrf_token;

export function fetchNodeGroups(botId) {
    return function(dispatch) {
        dispatch({type: "FETCH_NODE_GROUPS"});

        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/nodegroups/" + botId)
            .then((response) => {
            dispatch({type: "FETCH_NODE_GROUPS_FULFILLED", payload: response.data})
        })
        .catch((err) => {
                dispatch({type: "FETCH_NODE_GROUPS_REJECTED", payload: err})
            })
        }
}

export function addNodeGroup(botId) {
    return function(dispatch) {
        dispatch({type: "FETCH_NODE_GROUPS"});

        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/addgroup/" + botId)
            .then((response) => {
            dispatch({type: "ADD_GROUP_FULFILLED", payload: response.data})
        })
        .catch((err) => {
                dispatch({type: "ADD_GROUP_REJECTED", payload: err})
            })
        }
}

var timeout = null;

export function updateNodeGroup(obj) {

    return function(dispatch) {

        dispatch({type: "UPDATE_GROUP_FULFILLED", payload: obj})

        clearTimeout(timeout);

        timeout = setTimeout(function(){
            axios.post(WWW_DIR_JAVASCRIPT + "genericbot/updategroup", obj.toJS())
                .then((response) => {
                    dispatch({type: "UPDATE_GROUP_UPDATED", payload: response.data})
                })
                .catch((err) => {
                        dispatch({type: "ADD_GROUP_REJECTED", payload: err})
                    })
        },200);
    }
}

export function searchNodeTriggers(botId, keyword, includeTranslations = null) {
    let url = WWW_DIR_JAVASCRIPT + `/genericbot/triggersearch/${botId}/?keyword=${encodeURIComponent(keyword)}`;
    if (includeTranslations) {
        url += `&include_translations=1`;
    }
    return axios.get(url)
        .then((response) => {
            return response.data;
        })
        .catch((err) => {
            throw err;
        });
}


