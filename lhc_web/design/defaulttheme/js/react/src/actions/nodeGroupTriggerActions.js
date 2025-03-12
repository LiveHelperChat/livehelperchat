import axios from "axios";

axios.defaults.headers.common['X-CSRFToken'] = confLH.csrf_token;

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


export function deleteGroup(groupId) {
    return function(dispatch) {
        dispatch({type: "DELETE_TRIGGER_GROUP", payload : {groupId : groupId}});

        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/deletegroup/" + groupId.id)
        .then((response) => {
            dispatch({type: "DELETE_TRIGGER_GROUP_FULFILLED", payload: response.data})
        })
        .catch((err) => {
            dispatch({type: "DELETE_TRIGGER_GROUP_REJECTED", payload: err})
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
                document.location.hash = '#!#%2Ftrigger-'+triggerId;
        })
        .catch((err) => {
                dispatch({type: "FETCH_TRIGGER_RESPONSE_REJECTED", payload: err})
            })
        }
}

export function updateTriggerType(obj) {
    return function(dispatch) {
        dispatch({type: "UPDATE_TRIGGER_TYPE", payload : obj});
    }
}

export function updateTriggerName(obj) {
    return function(dispatch) {
        dispatch({type: "UPDATE_TRIGGER_NAME", payload : obj});
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

export function loadUseCases(obj) {
    return function(dispatch) {
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/loadusecases/" + obj.get('id'))
        .then((response) => {
            dispatch({type: "LOAD_USE_CASES_TRIGGER_FULFILLED", payload: response.data})
        }).catch((err) => {
            dispatch({type: "LOAD_USE_CASES_TRIGGER_FULFILLED", payload: err})
        })
    }
}

export function addTriggerEvent(obj) {
    return function(dispatch) {
        dispatch({type: "ADD_TRIGGER_EVENT", payload : obj});
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/addtriggerevent/" + obj.id)
                .then((response) => {
                dispatch({type: "ADD_TRIGGER_EVENT_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "ADD_TRIGGER_EVENT_FULFILLED", payload: err})
            })
        }
}

export function deleteTriggerEvent(obj) {
    return function(dispatch) {
        dispatch({type: "DELETE_TRIGGER_EVENT", payload : obj});
        axios.get(WWW_DIR_JAVASCRIPT + "genericbot/deletetriggerevent/" + obj.get('id'))
                .then((response) => {
                dispatch({type: "DELETE_TRIGGER_EVENT_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "DELETE_TRIGGER_EVENT_FULFILLED", payload: err})
            })
        }
}

export function setTriggerGroup(obj, group_id) {
    return function(dispatch) {
        dispatch({type: "SET_GROUP_TRIGGER_EVENT", payload : obj, group_id: group_id});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/settriggergroup/" + obj.get('id') + '/' + obj.get('group_id'))
                .then((response) => {
                dispatch({type: "SET_GROUP_TRIGGER_EVENT_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_GROUP_TRIGGER_EVENT_FULFILLED", payload: err})
            })
        }
}

export function setTriggerPosition(obj) {
    return function(dispatch) {
        dispatch({type: "SET_TRIGGER_POSITON", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/settriggerposition/" + obj.get('id') + '/' +  obj.get('pos'))
            .then((response) => {
                dispatch({type: "SET_DEFAULT_UNKNOWN_BTN_FULFILLED", payload: response.data})
            }).catch((err) => {
            dispatch({type: "SET_DEFAULT_UNKNOWN_BTN_REJECTED", payload: err})
        })
    }
}


var timeoutEvent = null;

export function updateTriggerEvent(obj) {
    return function(dispatch) {
        dispatch({type: "UPDATE_TRIGGER_EVENT", payload : obj});

        clearTimeout(timeoutEvent);

        timeoutEvent = setTimeout( function() { axios.post(WWW_DIR_JAVASCRIPT + "genericbot/updatetriggerevent",obj.toJS())
                .then((response) => {
                dispatch({type: "UPDATE_TRIGGER_EVENT_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "UPDATE_TRIGGER_EVENT_FULFILLED", payload: err})
            })
        }, 1000);
    }
}

export function saveTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SAVE_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/actions", obj.toJS())
                .then((response) => {
                dispatch({type: "SAVE_TRIGGER_FULFILLED", payload: response.data});
                updatePayload(dispatch, obj);
        }).catch((err) => {
                dispatch({type: "SAVE_TRIGGER_REJECTED", payload: err})
            })
        }
}

export function saveEventTemplate(obj) {
    return function(dispatch) {
        dispatch({type: "SAVE_EVENT_TEMPLATE", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/eventtemplate", obj)
                .then((response) => {
                dispatch({type: "SAVE_EVENT_TEMPLATE_FULFILLED", payload: response.data});
        }).catch((err) => {
                dispatch({type: "SAVE_EVENT_TEMPLATE_REJECTED", payload: err})
            })
        }
}

export function deleteEventTemplate(obj) {
    return function(dispatch) {
        dispatch({type: "DELETE_EVENT_TEMPLATE", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/deleteeventtemplate", obj)
            .then((response) => {
                dispatch({type: "SAVE_EVENT_TEMPLATE_FULFILLED", payload: response.data});
            }).catch((err) => {
            dispatch({type: "DELETE_EVENT_TEMPLATE_REJECTED", payload: err})
        })
    }
}

export function saveTemplate(obj) {
    return function(dispatch) {
        dispatch({type: "SAVE_TEMPLATE", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/template", obj)
                .then((response) => {
                dispatch({type: "SAVE_TEMPLATE_FULFILLED", payload: response.data});
        }).catch((err) => {
                dispatch({type: "SAVE_TEMPLATE_REJECTED", payload: err})
            })
        }
}

export function loadTemplate(obj) {
    return function(dispatch) {
        dispatch({type: "LOAD_TEMPLATE", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/loadtemplate", obj)
                .then((response) => {
                dispatch({type: "LOAD_TEMPLATE_FULFILLED", payload: response.data});
        }).catch((err) => {
                dispatch({type: "LOAD_TEMPLATE_REJECTED", payload: err})
            })
        }
}

export function loadEventTemplate(obj) {
    return function(dispatch) {
        dispatch({type: "LOAD_EVENT_TEMPLATE", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/loadeventtemplate", obj)
            .then((response) => {
                dispatch({type: "LOAD_EVENT_TEMPLATE_FULFILLED", payload: response.data});
            }).catch((err) => {
            dispatch({type: "LOAD_EVENT_TEMPLATE_REJECTED", payload: err})
        })
    }
}

export function deleteTemplate(obj) {
    return function(dispatch) {
        dispatch({type: "DELETE_TEMPLATE", payload : obj});
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/savetrigger/(method)/deletetemplate", obj)
                .then((response) => {
                dispatch({type: "SAVE_TEMPLATE_FULFILLED", payload: response.data});
        }).catch((err) => {
                dispatch({type: "DELETE_TEMPLATE_REJECTED", payload: err})
            })
        }
}

export function updatePayload(dispatch, obj) {
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/getpayloads/" + obj.get('id'))
            .then((response) => {
                dispatch({type: "UPDATE_PAYLOADS_FULFILLED", payload: response.data})
            }).catch((err) => {
            dispatch({type: "UPDATE_PAYLOADS_REJECTED", payload: err})
        })
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

export function makeTriggerCopy(obj) {
    return function(dispatch) {
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/maketriggercopy/" + obj.get('id'))
                .then((response) => {
                fetchNodeGroupTriggers(obj.get('group_id'))(dispatch);
                fetchNodeGroupTriggerAction(response.data.id)(dispatch);
        }).catch((err) => {
                dispatch({type: "COPY_TRIGGER_REJECTED", payload: err})
        })
    }
}

export function setDefaultTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SET_DEFAULT_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/setdefaulttrigger/" + obj.get('id') + '/' +  obj.get('default'))
                .then((response) => {
                dispatch({type: "SET_DEFAULT_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_DEFAULT_REJECTED", payload: err})
        })
    }
}

export function setInProgressTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SET_IN_PROGRESS_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/setinprogresstrigger/" + obj.get('id') + '/' +  obj.get('in_progress'))
                .then((response) => {
                dispatch({type: "SET_IN_PROGRESS_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_IN_PROGRESS_REJECTED", payload: err})
        })
    }
}

export function setDefaultUnknownTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SET_DEFAULT_UNKNOWN_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/setdefaultunknowntrigger/" + obj.get('id') + '/' +  obj.get('default_unknown'))
                .then((response) => {
                dispatch({type: "SET_DEFAULT_UNKNOWN_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_DEFAULT_UNKNOWN_REJECTED", payload: err})
        })
    }
}

export function setAsArgumentTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SET_AS_ARGUMENT_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/setasargument/" + obj.get('id') + '/' +  obj.get('as_argument'))
                .then((response) => {
                dispatch({type: "SET_AS_ARGUMENT_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_AS_ARGUMENT__REJECTED", payload: err})
        })
    }
}

export function setDefaultUnknownBtnTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SET_DEFAULT_UNKNOWN_BTN_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/setdefaultunknownbtntrigger/" + obj.get('id') + '/' +  obj.get('default_unknown_btn'))
                .then((response) => {
                dispatch({type: "SET_DEFAULT_UNKNOWN_BTN_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_DEFAULT_UNKNOWN_BTN_REJECTED", payload: err})
        })
    }
}

export function setDefaultAlwaysTrigger(obj) {
    return function(dispatch) {
        dispatch({type: "SET_DEFAULT_ALWAYS_TRIGGER", payload : obj});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/setdefaultalwaystrigger/" + obj.get('id') + '/' +  obj.get('default_always'))
                .then((response) => {
                dispatch({type: "SET_DEFAULT_ALWAYS_FULFILLED", payload: response.data})
        }).catch((err) => {
                dispatch({type: "SET_DEFAULT_ALWAYS_REJECTED", payload: err})
        })
    }
}

export function initBot(botId) {
    return function(dispatch) {
        dispatch({type: "INIT_BOT", payload : botId});

        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/initbot/" + botId)
            .then((response) => {
            dispatch({type: "INIT_BOT_FULFILLED", payload: response.data})
        }).catch((err) => {
            dispatch({type: "INIT_BOT_REJECTED", payload: err})
        })
    }
}

export function initRestMethods(RestAPIID) {
    return function(dispatch) {
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/restapimethods/" + RestAPIID)
            .then((response) => {
            dispatch({type: "INIT_BOT_REST_API_METHODS", payload: response.data})
        }).catch((err) => {
            //dispatch({type: "INIT_BOT_REJECTED", payload: err})
        })
    }
}


export function initArgumentTemplates() {
    return function(dispatch) {
        axios.post(WWW_DIR_JAVASCRIPT + "genericbot/argumenttemplates")
            .then((response) => {                
            dispatch({type: "INIT_BOT_ARGUMENTS_FULFILLED", payload: response.data})
        }).catch((err) => {
            dispatch({type: "INIT_BOT_ARGUMENTS_REJECTED", payload: err})
        })
    }
}

