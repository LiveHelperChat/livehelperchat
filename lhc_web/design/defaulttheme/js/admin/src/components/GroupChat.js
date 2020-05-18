//https://medium.com/@MilkMan/read-this-before-refactoring-your-big-react-class-components-to-hooks-515437e9d96f
//https://reactjs.org/docs/hooks-reference.html#usereducer

import React, { useEffect, useState, useReducer, useRef } from "react";
import axios from "axios";
import parse, { domToReact } from 'html-react-parser';

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

function ChatMessage({message, index}) {

    var operatorChanged = false;

    console.log(message['msop']);
    console.log(message['lmsop']);

    return parse(message['msg'], {

        replace: domNode => {
            if (domNode.attribs) {

                var cloneAttr = Object.assign({}, domNode.attribs);

                if (domNode.attribs.class) {
                    domNode.attribs.className = domNode.attribs.class;

                    // Animate only if it's not first sync call
                    if (domNode.attribs.className.indexOf('message-row') !== -1 && index > 0) {
                        domNode.attribs.className += ' fade-in-fast';
                        if (message['msop'] > 0 && message['msop'] != message['lmsop'] && operatorChanged == false) {
                            domNode.attribs.className += ' operator-changes';
                            operatorChanged = true;
                        }
                    }

                    delete domNode.attribs.class;
                }

                if (domNode.attribs.onclick) {
                    delete domNode.attribs.onclick;
                }

                if (domNode.name && domNode.name === 'img') {
                    return <img {...domNode.attribs} />
                } else if (domNode.name && domNode.name === 'a') {
                    if (cloneAttr.onclick) {
                        return <a {...domNode.attribs}  >{domToReact(domNode.children)}</a>
                    }
                }
            }
        }
    });
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
    const messagesElement = useRef(null);
    const tabsContainer = useRef(null);

    const [state, dispatch] = useReducer(reducer, {
        messages: [],
        operators: [],
        last_message: '',
        last_message_id: 0,
        lmsop: 0,
    });

    const loadMainData() = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/loadgroupchat/" + props.chatId).then(result => {
            dispatch({
                type: 'update',
                value: {
                    'operators': result.data.operators
                }
            });
        });
    }

    const fetchMessages = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/sync/" + props.chatId,[props.chatId + ',' + state.last_message_id]).then(result => {
            result.data.result.forEach((chatData) => {
                if (chatData.chat_id == props.chatId) {
                    dispatch({
                        type: 'update_messages',
                        messages : {
                            'msg':chatData.content,
                            'lmsop': (state.lmsop || chatData.lmsop),
                            'msop': chatData.msop,
                        },
                        value: {
                            'last_message' : 'Just last message sample from sync'+Date.now(),
                            'last_message_id' : chatData.message_id,
                            'lmsop': chatData.lmsop
                        }
                    });
                }
            });
        });
    }

    useInterval(() => {
        fetchMessages();
    },2500)

    useEffect(() => {
        messagesElement.current.scrollTop = messagesElement.current.scrollHeight;
    },[state.messages.length]);

    const rememberChat = (chatId) => {
        if (localStorage) {
            try {
                var achat_id_array = [];
                var achat_id = localStorage.getItem('gachat_id');

                if (achat_id !== null && achat_id !== '') {
                    achat_id_array = achat_id.split(',');
                }

                if (achat_id_array.indexOf(chatId) === -1){
                    achat_id_array.push(chatId);
                    localStorage.setItem('gachat_id',achat_id_array.join(','));
                }

            } catch(e) {

            }
        }
    }

    const forgetChat = (chatId) => {
        if (localStorage) {
            try {
                var achat_id_array = [];
                var achat_id = localStorage.getItem('gachat_id');

                if (achat_id !== null && achat_id !== '') {
                    achat_id_array = achat_id.split(',');
                }

                if (achat_id_array.indexOf(chatId) !== -1) {
                    achat_id_array.splice(achat_id_array.indexOf(chatId),1);
                }

                localStorage.setItem('gachat_id',achat_id_array.join(','));
            } catch(e) {

            }
        }
    }

    useEffect(() => {
        fetchMessages();
        loadMainData();

        rememberChat(props.chatId);

        // Activate tabs
        var container = tabsContainer.current;
        var bsn = require("bootstrap.native/dist/bootstrap-native-v4");
        var tabs = container.querySelectorAll('[data-toggle="tab"]');

        if (tabs.length > 0) {
            Array.prototype.forEach.call(tabs, function(element){ new bsn.Tab( element) });
        }

        return function cleanup() {
            forgetChat(props.chatId)
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
                    <div className="message-block">
                        <div className="msgBlock msgBlock-admin" ref={messagesElement}>
                           {state.messages.map((message, index) => (
                                <ChatMessage key={'msg_' + props.chatId + '_' + index} index={index} message={message} />
                            ))}
                        </div>
                    </div>

                    <div className="message-container-admin mt-2">
                        <textarea ref={messageElement} placeholder="" onKeyDown={(e) => addMessage(e)} className="form-control form-control-sm form-group" rows="2"></textarea>
                    </div>

                </div>
                <div className="col-sm-5 chat-main-right-column">
                    <div role="tabpanel">
                        <ul className="nav nav-pills" role="tablist" ref={tabsContainer}>
                            <li role="presentation" className="nav-item"><a className="nav-link active" href={"#group-chat-"+props.chatId} aria-controls={"#group-chat-"+props.chatId} role="tab" data-toggle="tab" title="Operators"><i className="material-icons mr-0">face</i></a></li>
                            <li className="nav-item" role="presentation"><a className="nav-link " href={"#group-chat-info-"+props.chatId} aria-controls={"#group-chat-info-"+props.chatId} title="Information" role="tab" data-toggle="tab"><i className="material-icons mr-0">info_outline</i></a></li>
                        </ul>
                        <div className="tab-content">
                            <div role="tabpanel" className="tab-pane active" id={"group-chat-"+props.chatId}>

                                <h5>Operators</h5>
                                <ul className="list-group list-group-flush border-0">
                                    <li className="list-group-item pl-1 py-1">Cras justo odio</li>
                                    <li className="list-group-item pl-1 py-1">Dapibus ac facilisis in</li>
                                    <li className="list-group-item pl-1 py-1">Morbi leo risus</li>
                                    <li className="list-group-item pl-1 py-1">Porta ac consectetur ac</li>
                                    <li className="list-group-item pl-1 py-1">Vestibulum at eros</li>
                                </ul>

                            </div>
                            <div role="tabpanel" className="tab-pane" id={"group-chat-info-"+props.chatId}>
                                <p>Group chat information...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

export default GroupChat