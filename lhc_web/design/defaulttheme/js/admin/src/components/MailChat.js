//https://medium.com/@MilkMan/read-this-before-refactoring-your-big-react-class-components-to-hooks-515437e9d96f
//https://reactjs.org/docs/hooks-reference.html#usereducer

import React, { useEffect, useState, useReducer, useRef } from "react";
import axios from "axios";
import {useTranslation} from 'react-i18next';
import MailChatMessage from "./parts_mail/MailChatMessage";


function reducer(state, action) {
    switch (action.type) {
        case 'increment':
            return {count: state.count + 1};
        case 'decrement':
            return {count: state.count - 1};
        case 'update': {
            return { ...state, ...action.value }
        }

        case 'update_message': {
            var foundIndex = state.messages.findIndex(x => x.id == action.message.id);
            state.messages[foundIndex] = action.message;

            if (action.conv) {
                state.conv = action.conv;
            }

            state = { ... state};

            return state;
        }

        case 'update_messages': {

            // Set last operator from previous state
            action.messages['lmsop'] = state.lmsop || action.value.lmsop;

            // Update state
            state = { ...state, ...action.value };

            // Update message
            state.messages.push(action.messages);

            return state;
        }
        case 'update_history': {
            state = { ...state, ...action.value };
            if (action.history.msg != '') {
                state.messages.unshift(action.history);
            }
            return state;
        }
        case 'init':
            return {count: state.count - 1};
        default:
            throw new Error('Unknown action!');
    }
}

const MailChat = props => {

    const messageElement = useRef(null);
    const messagesElement = useRef(null);
    const tabsContainer = useRef(null);

    const [state, dispatch] = useReducer(reducer, {
        messages: [],
        operators: [],
        conv: null,
        loaded: false,
        saving_remarks: false,
        old_message_id: 0,
        last_message: '',
        remarks: '',
        last_message_id: 0,
        lmsop: 0,
        lgsync: 0
    });

    const rememberChat = (chatId) => {
        if (localStorage) {
            try {
                var achat_id_array = [];
                var achat_id = localStorage.getItem('machat_id');

                if (achat_id !== null && achat_id !== '') {
                    achat_id_array = achat_id.split(',');
                }

                if (achat_id_array.indexOf(chatId) === -1){
                    achat_id_array.push(chatId);
                    localStorage.setItem('machat_id',achat_id_array.join(','));
                }

            } catch(e) {

            }
        }
    }

    const deleteConversation = () => {
        if (confirm('Are you sure?')) {
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apideleteconversation/" + state.conv.id).then(result => {
                // If we are in the tab close tab also
                if (document.getElementById('chat-tab-link-mc'+state.conv.id)) {
                    lhinst.removeDialogTabMail('mc'+state.conv.id,$('#tabs'),true);
                } else {
                    document.location = WWW_DIR_JAVASCRIPT + "mailconv/conversations";
                }
            }).catch((error) => {

            });
        }
    }

    const closeConversation = () => {
        let hasUnrespondedMessages = false;
        state.messages.forEach((message) => {
            if (message.status != 2) {
                hasUnrespondedMessages = true;
            }
        });

        if (hasUnrespondedMessages == false || confirm('There is still unresponded messages, are you sure you want to close this conversation?')) {
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apicloseconversation/" + state.conv.id).then(result => {
                dispatch({
                    type: 'update',
                    value: {
                        'conv': result.data.conv,
                        'messages' : result.data.messages
                    }
                });

                // If we are in the tab close tab also
                if (document.getElementById('chat-tab-link-mc'+state.conv.id)) {
                    lhinst.removeDialogTabMail('mc'+state.conv.id,$('#tabs'),true);
                }

            }).catch((error) => {

            });
        }
    }

    const noReplyRequired = (message) => {
        axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apinoreplyrequired/" + message.id).then(result => {
            dispatch({
                type: 'update_message',
                message: result.data.message,
                conv: result.data.conv
            });
        }).catch((error) => {

        });
    }

    const loadMainData = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/loadmainconv/" + props.chatId + '/(mode)/' + (props.mode != '' ? props.mode : 'normal')).then(result => {
            dispatch({
                type: 'update',
                value: {
                    'conv': result.data.conv,
                    'messages' : result.data.messages,
                    'loaded' : true,
                }
            });

            if (props.mode !== 'preview') {
                rememberChat(props.chatId);
            }

        }).catch((error) => {

        });
    }

    const showModal = (params) => {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + params.url});
    }

    useEffect(() => {
        const timeout = setTimeout(() => {
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/saveremarks/" + props.chatId, {data: state.remarks}).then(result => {
                dispatch({
                    type: 'update',
                    value: {
                        'saving_remarks': false
                    }
                });
            });
        }, 500);

        return () => clearTimeout(timeout);
    },[state.remarks]);

    const saveRemarks = (params) => {
        dispatch({
            type: 'update',
            value: {
                'saving_remarks': true,
                'remarks': params
            }
        });
    }

    const forgetChat = (chatId) => {
        if (localStorage) {
            try {
                var achat_id_array = [];
                var achat_id = localStorage.getItem('machat_id');

                if (achat_id !== null && achat_id !== '') {
                    achat_id_array = achat_id.split(',');
                }

                if (achat_id_array.indexOf(chatId) !== -1) {
                    achat_id_array.splice(achat_id_array.indexOf(chatId),1);
                }

                localStorage.setItem('machat_id',achat_id_array.join(','));
            } catch(e) {

            }
        }
    }

    useEffect(() => {
        loadMainData();
        return function cleanup() {
           forgetChat(props.chatId)
        };
    },[]);

    useEffect(() => {
        if (state.loaded == true) {
            var container = tabsContainer.current;
        }

    },[state.loaded]);

    const { t, i18n } = useTranslation('mail_chat');

    if (state.loaded == false) {
        return <span>...</span>
    }

    return (
        <React.Fragment>
            <div className="row">
                <div className={"chat-main-left-column " + (props.mode == 'preview' ? 'col-12' : 'col-7')}>

                    {props.mode !== 'preview' && <h1 className="pb-2"><i className="material-icons">{state.conv.start_type == 1 ? 'call_made' : 'call_received'}</i>{state.conv.subject}</h1>}

                    <div>
                        {state.messages.map((message, index) => (
                            <MailChatMessage mode={props.mode} key={'msg_mail_' + props.chatId + '_' + index + '_' + message.id} totalMessages={state.messages.length} index={index} message={message} noReplyRequired={(e) => noReplyRequired(message)} />
                        ))}
                    </div>
                </div>
                <div className={"chat-main-right-column " + (props.mode == 'preview' ? 'd-none' : 'col-5')}>
                    <div role="tabpanel">
                        <ul className="nav nav-pills" role="tablist" ref={tabsContainer}>
                            <li role="presentation" className="nav-item"><a className="nav-link active" href={"#mail-chat-info-"+props.chatId} aria-controls={"#mail-chat-info-"+props.chatId} title="Information" role="tab" data-toggle="tab"><i className="material-icons mr-0">info_outline</i></a></li>
                            <li role="presentation" className="nav-item"><a className="nav-link" href={"#mail-chat-remarks-"+props.chatId} aria-controls={"#mail-chat-remarks-"+props.chatId} role="tab" data-toggle="tab" title="Remarks"><i className="material-icons mr-0">mode_edit</i></a></li>
                        </ul>
                        <div className="tab-content">
                            <div role="tabpanel" className="tab-pane" id={"mail-chat-remarks-"+props.chatId}>
                                <div className={"material-icons pb-1 text-success" + (state.saving_remarks ? ' text-warning' : '')}>mode_edit</div>
                                <div>
                                    {state.conv && <textarea placeholder="Enter your remarks here." onKeyUp={(e) => saveRemarks(e.target.value)} class="form-control mh150" defaultValue={state.conv.remarks}></textarea>}
                                </div>
                            </div>
                            <div role="tabpanel" className="tab-pane active" id={"mail-chat-info-"+props.chatId}>

                                <div className="pb-2">
                                    <a className="btn btn-outline-secondary btn-sm" onClick={() => closeConversation()}><i className="material-icons">close</i>Close</a>
                                </div>

                                {state.conv && <table className="table table-sm">
                                    <tr>
                                        <td colSpan="2">
                                            <i className="material-icons action-image" onClick={() => showModal({url: "mailconv/mailhistory/" + props.chatId})}>history</i>
                                            <a className="material-icons action-image" onClick={() => showModal({url: "mailconv/transfermail/" + props.chatId})} title="Transfer chat">supervisor_account</a>
                                            <a className="text-dark material-icons" target="_blank" href={WWW_DIR_JAVASCRIPT  + "mailconv/mailprintcovnersation/" + props.chatId} >print</a>
                                            <a className="material-icons mr-0" onClick={(e) => deleteConversation()} title="Delete chat">delete</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sender</td>
                                        <td>{state.conv.from_address} &lt;{state.conv.from_name}&gt;</td>
                                    </tr>
                                    <tr>
                                        <td>Status</td>
                                        <td>
                                            {!state.conv.status && <span><i className="material-icons chat-pending">mail_outline</i>Pending</span>}
                                            {state.conv.status == 1 && <span><i className="material-icons chat-active">mail_outline</i>Active</span>}
                                            {state.conv.status == 2 && <span><i className="material-icons chat-closed">mail_outline</i>Closed</span>}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Department</td>
                                        <td>{state.conv.department_name}</td>
                                    </tr>
                                    <tr>
                                        <td>Received</td>
                                        <td>{state.conv.udate_front}</td>
                                    </tr>
                                    <tr>
                                        <td>ID</td>
                                        <td>{state.conv.id}</td>
                                    </tr>

                                    {state.conv.accept_time && <tr>
                                        <td>Accepted at</td>
                                        <td>{state.conv.accept_time_front} | Wait time {state.conv.wait_time_pending}</td>
                                    </tr>}

                                    {state.conv.response_time && <tr>
                                        <td>Responded at</td>
                                        <td>{state.conv.lr_time_front} | Wait time {state.conv.wait_time_response}</td>
                                    </tr>}

                                    {state.conv.cls_time && <tr>
                                        <td>Closed at</td>
                                        <td>{state.conv.cls_time_front}</td>
                                    </tr>}

                                    {state.conv.interaction_time && <tr>
                                        <td>Interaction time</td>
                                        <td>{state.conv.interaction_time_duration}</td>
                                    </tr>}

                                    {state.conv.priority && <tr>
                                        <td>Priority</td>
                                        <td>{state.conv.priority}</td>
                                    </tr>}

                                    <tr>
                                        <td>Chat owner</td>
                                        <td>{state.conv.plain_user_name}</td>
                                    </tr>
                                </table>}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

export default MailChat