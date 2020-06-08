//https://medium.com/@MilkMan/read-this-before-refactoring-your-big-react-class-components-to-hooks-515437e9d96f
//https://reactjs.org/docs/hooks-reference.html#usereducer

import React, { useEffect, useState, useReducer, useRef } from "react";
import axios from "axios";
import GroupChatMessage from "./parts/GroupChatMessage";
import useInterval from "./lib/useInterval";
import {groupChatSync} from "./lib/groupChatSync";
import {useTranslation} from 'react-i18next';

function reducer(state, action) {
    switch (action.type) {
        case 'increment':
            return {count: state.count + 1};
        case 'decrement':
            return {count: state.count - 1};
        case 'update': {
            return { ...state, ...action.value }
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

const GroupChat = props => {

    const messageElement = useRef(null);
    const messagesElement = useRef(null);
    const tabsContainer = useRef(null);
    const searchOperatorElement = useRef(null);

    const [state, dispatch] = useReducer(reducer, {
        messages: [],
        operators: [],
        operators_invite: [],
        chat: {},
        has_more_messages: false,
        old_message_id: 0,
        last_message: '',
        last_message_id: 0,
        lmsop: 0,
        lgsync: 0
    });

    const loadMainData = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/loadgroupchat/" + props.chatId).then(result => {
            dispatch({
                type: 'update',
                value: {
                    'chat': result.data.chat
                }
            });
            rememberChat(props.chatId);
            groupChatSync.sync();
        }).catch((error) => {
            lhinst.removeDialogTabGroup('gc'+props.chatId,$('#tabs'),true);
        });
    }

    const loadPrevious = () => {
        axios.get(WWW_DIR_JAVASCRIPT  + "groupchat/loadpreviousmessages/" + props.chatId+'/'+state.old_message_id).then(result => {
            dispatch({
                type: 'update_history',
                value: {
                    'has_more_messages' : result.data.has_messages,
                    'old_message_id' : result.data.message_id
                },
                history: {
                    "msg" : result.data.result,
                    "msop" : result.data.msop,
                    "lmsop" : result.data.lmsop
                }
            });
        });
    }

    const startChatWithOperator = (operator) => {
        var lhcController = angular.element('body').scope();
        lhcController.startChatOperatorPublic(operator.user_id);
    }

    useEffect(() => {
        messagesElement.current.scrollTop = messagesElement.current.scrollHeight;

        var tab = document.getElementById('chat-tab-link-gc'+props.chatId);

        if (state.messages.length > 1 && !tab.classList.contains('active')) {
            tab.querySelector('.whatshot').classList.remove("d-none");
        }

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

    const leaveGroup = () => {
        axios.get(WWW_DIR_JAVASCRIPT  + "groupchat/leave/" + props.chatId).then(result => {
            lhinst.removeDialogTabGroup('gc'+props.chatId,$('#tabs'),true)
        });
    }

    var searchTimeout = null
    const searchOpeartors = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            axios.get(WWW_DIR_JAVASCRIPT  + "groupchat/searchoperator/"+props.chatId+"?q=" + escape(searchOperatorElement.current.value)).then(result => {
                dispatch({
                    type: 'update',
                    value: {
                        "operators_invite" : result.data
                    }
                });
            });
        },200);
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
        loadMainData();
        // Activate tabs
        var container = tabsContainer.current;
        var bsn = require("bootstrap.native/dist/bootstrap-native-v4");
        var tabs = container.querySelectorAll('[data-toggle="tab"]');

        if (tabs.length > 0) {
            Array.prototype.forEach.call(tabs, function(element){ new bsn.Tab( element) });
        }

        const tabClicked = (e) => {
            if (e == props.chatId) {
                setTimeout(() => {
                    messageElement.current.focus();
                    if (messagesElement.current.scrollHeight - (messagesElement.current.scrollTop + messagesElement.current.offsetHeight) < (messagesElement.current.offsetHeight - 50)) {
                        messagesElement.current.scrollTop = messagesElement.current.scrollHeight;
                    }
                },2);

                var tab = document.getElementById('chat-tab-link-gc'+props.chatId);
                if (tab !== null) {
                    var tabHot = tab.querySelector('.whatshot');
                    if (!tabHot.classList.contains("d-none")) {
                        tabHot.classList.add("d-none");
                    }
                }
            }
        }

        ee.addListener('groupChatTabClicked',tabClicked)

        messageElement.current.focus();

        const chatSynced = (e) => {
            if (e.msg) {
                dispatch({
                    type: 'update_messages',
                    messages : {
                        'msg':e.msg.content,
                        'msop': e.msg.msop,
                    },
                    value: {
                        'last_message_id' : e.msg.message_id,
                        'lmsop': e.msg.lmsop
                    }
                });
            }

            if (e.status) {
                let valueUpdate = {
                    'operators': e.status.operators,
                    'lgsync': e.status.lgsync
                };

                if (e.status.old_message_id) {
                    valueUpdate['has_more_messages'] = e.status.has_more_messages;
                    valueUpdate['old_message_id'] = e.status.old_message_id;
                }

                dispatch({
                    type: 'update',
                    value: valueUpdate
                });
            }
        }

        groupChatSync.addSubscriber(props.chatId, chatSynced);

        return function cleanup() {
            forgetChat(props.chatId)
            ee.removeListener('groupChatTabClicked',tabClicked);
            groupChatSync.removeSubscriber(props.chatId, chatSynced);
        };
    },[]);

    const addMessage = (e, doSearch) => {
        if (e.keyCode == 13) {

            axios.post(WWW_DIR_JAVASCRIPT  + "groupchat/addmessage/" + props.chatId,{msg: messageElement.current.value}).then(result => {
                groupChatSync.sync();
            });

            messageElement.current.value = '';

            e.preventDefault();
            e.stopPropagation();
            return;
        }
    }

    const inviteOperator = (e) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "groupchat/inviteoperator/" + props.chatId + "/" + e.id).then(result => {
            e.invited = true;
            dispatch({
                type: 'update',
                value: {
                    "operators_invite" : state.operators_invite
                }
            });
        });
    }

    const cancelInvite = (e) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "groupchat/cancelinvite/" + props.chatId + "/" + e.id).then(result => {
            e.invited = false;
            dispatch({
                type: 'update',
                value: {
                    "operators_invite" : state.operators_invite
                }
            });
        });
    }

    const { t, i18n } = useTranslation('group_chat');

    return (
        <React.Fragment>
            <div className="row">
                <div className="col-7 chat-main-left-column">
                    <div className="message-block">

                        {state.has_more_messages && <a className="load-prev-btn"  title="Load more..." onClick={(e) => loadPrevious()}><i className="material-icons">&#xE889;</i></a>}

                        <div className="msgBlock msgBlock-admin msgBlock-group-admin" ref={messagesElement}>
                           {state.messages.map((message, index) => (
                                <GroupChatMessage key={'msg_' + props.chatId + '_' + index} index={index} message={message} />
                            ))}
                        </div>
                    </div>
                    <div className="message-container-admin mt-2">
                        <textarea ref={messageElement} placeholder={t('message.enter_message')} onKeyDown={(e) => addMessage(e)} className="form-control form-control-sm form-group" rows="2"></textarea>
                    </div>
                </div>
                <div className="chat-main-right-column col-5">
                    <div role="tabpanel">
                        <ul className="nav nav-pills" role="tablist" ref={tabsContainer}>
                            <li role="presentation" className="nav-item"><a className="nav-link active" href={"#group-chat-"+props.chatId} aria-controls={"#group-chat-"+props.chatId} role="tab" data-toggle="tab" title="Operators"><i className="material-icons mr-0">face</i></a></li>
                            <li className="nav-item" role="presentation"><a className="nav-link " href={"#group-chat-info-"+props.chatId} aria-controls={"#group-chat-info-"+props.chatId} title="Information" role="tab" data-toggle="tab"><i className="material-icons mr-0">info_outline</i></a></li>
                        </ul>
                        <div className="tab-content">
                            <div role="tabpanel" className="tab-pane active" id={"group-chat-"+props.chatId}>

                                <ul className="list-group list-group-flush border-0 mw-100 mx275">
                                    {state.operators.map((operator, index) => (
                                        <li className="list-group-item pl-1 py-1">{props.userId != operator.user_id && <i title="Start chat with an operator directly" onClick={(e) => startChatWithOperator(operator)} className="material-icons action-image">chat</i>} {state.chat.user_id == operator.user_id && <i title="Group owner" className="material-icons">account_balance</i>} {operator.n_off_full}<span className="float-right fs11">
                                            {!operator.jtime && <span className="badge badge-info fs11">{t('operator.pending_join')}</span>} {operator.last_activity_ago} <i className="material-icons">{operator.hide_online ? 'flash_off' : 'flash_on'}</i>
                                        </span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            <div role="tabpanel" className="tab-pane" id={"group-chat-info-"+props.chatId}>

                                {state.chat.type == 1 && <div>
                                    <div className="form-row">
                                        <div className="col-9">
                                            <input ref={searchOperatorElement} onKeyUp={searchOpeartors} type="text" placeholder={t('operator.search_tip')} className="form-control form-control-sm" />
                                        </div>
                                        <div className="col-3">
                                            <button onClick={searchOpeartors} className="btn w-100 d-block btn-default btn-sm"><span className="material-icons">search</span></button>
                                        </div>
                                    </div>

                                    <ul className="m-0 p-0 mt-2 mx275">
                                        {state.operators_invite.map((operator, index) => (
                                            <li className="list-group-item p-2 fs13" title={operator.id}>
                                                {operator.name_official}
                                                {!operator.member && !operator.invited && <button className="float-right btn btn-xs btn-secondary" onClick={(e) => inviteOperator(operator)}>{t('operator.invite')}</button>}
                                                {!operator.member && operator.invited && <button className="float-right btn btn-xs btn-warning" onClick={(e) => cancelInvite(operator)}>{t('operator.cancel_invite')}</button>}
                                                {operator.member && <button disabled="disabled" className="float-right btn btn-xs btn-success">{t('operator.already_member')}</button>}
                                            </li>
                                        ))}
                                    </ul>
                                    <hr/>
                                </div>}

                                <button className="btn btn-xs btn-danger" title={t('operator.leave_group_tip')} onClick={(e) => leaveGroup()}>{t('operator.leave_group')}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

export default GroupChat