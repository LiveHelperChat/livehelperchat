//https://medium.com/@MilkMan/read-this-before-refactoring-your-big-react-class-components-to-hooks-515437e9d96f
//https://reactjs.org/docs/hooks-reference.html#usereducer
import React, { useEffect, useState, useReducer, useRef } from "react";
import axios from "axios";


function reducer(state, action) {
    switch (action.type) {
        case 'increment':
            return {count: state.count + 1};
        case 'decrement':
            return {count: state.count - 1};
        case 'update': {
            console.log('update');
            return { ...state, ...action.value }
        }
        case 'update_messages': {
            state = { ...state, ...action.value };
            state.messages.push(action.messages);
            return state;
        }
        case 'init':
            return {count: state.count - 1};
        default:
            throw new Error('Unknown action!');
    }
}

function useInterval(callback, delay) {
    const savedCallback = useRef();

    // Remember the latest callback.
    useEffect(() => {
        savedCallback.current = callback;
    }, [callback]);

    // Set up the interval.
    useEffect(() => {
        function tick() {
            savedCallback.current();
        }
        if (delay !== null) {
            let id = setInterval(tick, delay);
            return () => clearInterval(id);
        }
    }, [delay]);
}

const GroupChat = props => {

    const messageElement = useRef(null);

    const [state, dispatch] = useReducer(reducer, {
        messages: [],
        operators: [],
        last_message: '',
        last_message_id: 0,
    });

    const fetchMessages = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/sync/" + props.chatId,[props.chatId + ',' + state.last_message_id]).then(result => {
            result.data.result.forEach((chatData) => {
                if (chatData.chat_id == props.chatId) {
                    dispatch({
                        type: 'update_messages',
                        messages : chatData.content,
                        value: {
                            'last_message' : 'Just last message sample from sync'+Date.now(),
                            'last_message_id' : chatData.message_id
                        }
                    });
                }
            });
        });
    }

    useInterval(() => {
        fetchMessages();
    },3000)

    useEffect(() => {
        return function cleanup() {

        };
    },[]);

    const addMessage = (e, doSearch) => {
        if (e.keyCode == 13) {

            axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/addmessage/" + props.chatId,{msg: messageElement.current.value}).then(result => {
                fetchMessages();
            });

            messageElement.current.value = '';

            e.preventDefault();
            e.stopPropagation();
            return;
        }
    }

    return (
        <React.Fragment>
            <div className="row">
                <div className="col-sm-7 chat-main-left-column">

                    {state.last_message}
                    --{state.last_message_id}--
                    {state.messages.length}

                    <div className="message-block">
                        <div className="msgBlock msgBlock-admin">

                        </div>
                    </div>

                    <div className="message-container-admin">
                        <textarea ref={messageElement} placeholder="" onKeyDown={(e) => addMessage(e)} className="form-control form-control-sm form-group" rows="2"></textarea>
                    </div>

                </div>
                <div className="col-sm-5 chat-main-right-column">
                    <div role="tabpanel">
                        <ul className="nav nav-pills" role="tablist">
                            <li role="presentation" className="nav-item"><a className="nav-link active" href="#main-user-info-tab-11194" aria-controls="main-user-info-tab-11194" role="tab" data-toggle="tab" title="Visitor"><i className="material-icons mr-0">face</i></a></li>
                            <li className="nav-item" role="presentation"><a className="nav-link " href="#main-user-info-translation-11194" aria-controls="main-user-info-translation-11194" title="Automatic translation" role="tab" data-toggle="tab"><i className="material-icons mr-0">info_outline</i></a></li>
                        </ul>
                        <div className="tab-content">
                            <div role="tabpanel" className="tab-pane active" id="main-user-info-tab-11194">
                            </div>
                            <div role="tabpanel" className="tab-pane" id="main-user-info-remarks-11194">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

export default GroupChat