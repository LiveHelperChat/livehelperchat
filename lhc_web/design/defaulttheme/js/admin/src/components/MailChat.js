import React, { useEffect, useState, useReducer, useRef } from "react";
import axios from "axios";
import {useTranslation} from 'react-i18next';
import MailChatMessage from "./parts_mail/MailChatMessage";

axios.defaults.headers.common['X-CSRFToken'] = confLH.csrf_token;

function reducer(state, action) {
    switch (action.type) {
        case 'increment':
            return {count: state.count + 1};
        case 'decrement':
            return {count: state.count - 1};
        case 'update': {
            return { ...state, ...action.value }
        }
        case 'update_conversation': {
            state.conv = { ...state.conv, ...action.value };
            return { ... state};
        }
        case 'update_subjects': {
            var foundIndex = state.messages.findIndex(x => x.id == action.message_id);
            state.messages[foundIndex].subjects = action.subjects;
            state = { ... state};
            return state;
        }
        case 'update_message': {
            var foundIndex = state.messages.findIndex(x => x.id == action.message.id);
            state.messages[foundIndex] = action.message;

            if (action.conv) {
                state.conv = action.conv;
            }

            if (action.fetching_messages) {
                state.fetching_messages = action.fetching_messages;
            }

            state = { ... state};

            return state;
        }

        case 'update_watching_ops': {
            var foundIndex = state.op_watching.findIndex(x => x.user_id == action.watcher.user_id);
            if (action.watcher.status === true) {
                if (foundIndex === -1) {
                    if (confLH.user_id != action.watcher.user_id) {
                        action.watcher.ts = Math.floor(Date.now() / 1000);
                        state.op_watching.push(action.watcher);
                    }
                } else {
                    state.op_watching[foundIndex].ts = Math.floor(Date.now() / 1000);
                }
            } else if (action.watcher.status === false && foundIndex !== -1) {
                state.op_watching.splice(foundIndex,1);
            }

            state.op_watching.forEach((element,index) => {
                if (element.ts < Math.floor(Date.now() / 1000) - 15) {
                    state.op_watching.splice(index,1);
                }
            });

            return { ...state};
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
        op_watching: [],
        conv: null,
        loaded: false,
        saving_remarks: false,
        close_mode: false,
        old_message_id: 0,
        last_message: '',
        remarks: '',
        last_message_id: 0,
        lmsop: 0,
        lgsync: 0,
        fetching_messages: false,
        expand_messages: false
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

    const processRestAPIError = (err) => {
        if (!!err.isAxiosError && !err.response) {
            alert(t('system.error'));
        } else {
            if (err.response.data.error) {
                alert(err.response.data.error);
            } else {
                alert(JSON.stringify(err.response.data));
            }
        }
    }

    const changeStatus = (e) => {
        if (confirm(t('status.are_you_sure'))) {
            axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apichangestatus/" + state.conv.id + '/' + e.target.value).then(result => {
                dispatch({
                    type: 'update',
                    value: {
                        'conv': result.data.conv,
                    }
                });
            }).catch((error) => processRestAPIError(error));
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

        if (hasUnrespondedMessages == false || confirm(t('status.unresponded_messages'))) {
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
        axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/apinoreplyrequired/" + message.id + "/" + message.conversation_id).then(result => {
            dispatch({
                type: 'update_message',
                message: result.data.message,
                conv: result.data.conv
            });
        }).catch((error) => {

        });
    }

    const loadMessageBody = (message, data) => {
        dispatch({
            type: 'update_message',
            message: data.message,
            conv: data.conv
        });
    }

    const addLabel = (message) => {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + "mailconv/apilabelmessage/" + message.id + "/" + message.conversation_id,hidecallback : () => {
            updateLabels(message);
        }});
    }

    const updateLabels = (message) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "mailconv/apigetlabels/" + message.id + "/" + message.conversation_id).then(result => {
            dispatch({
                type: 'update_subjects',
                message_id: message.id,
                subjects: result.data
            });
        }).catch((error) => {

        });
    }

    const verifyOwner = (userId) => {
        if (userId != state.conv.user_id) {
            loadMainData();
        }
    }

    const loadMainData = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/loadmainconv/" + props.chatId + '/(mode)/' + (props.mode != '' ? props.mode : 'normal'),{keyword: props.keyword}).then(result => {
            dispatch({
                type: 'update',
                value: {
                    'conv': result.data.conv,
                    'customer_remarks': result.data.customer_remarks,
                    'messages' : result.data.messages,
                    'moptions' : result.data.moptions,
                    'loaded' : true,
                    'fetching_messages' : false,
                }
            });

            if (props.disableRemember === false && props.mode !== 'preview') {
                rememberChat(props.chatId);
            }

            if (props.mode !== 'preview' && result.data.conv.pending_sync) {
                fetchMessages();
            }

        }).catch((error) => {
            lhinst.removeDialogTabMail('mc'+ props.chatId,$('#tabs'),true);
        });
    }

    const fetchMessages = (message) => {
        // Reset previous state
        dispatch({
            type: 'update',
            value: {
                'fetching_messages': true
            }
        });

        fetchUntillUpdate(Math.floor(Date.now() / 1000));
    }

    const fetchUntillUpdate = (ts) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "mailconv/apifetchmails/" + props.chatId + '/' + ts).then(result => {
            if (result.data.updated == true) {
                loadMainData();
            } else {
                // Todo handle cleanup
                setTimeout(() => fetchUntillUpdate(ts),3000);
            }
        });
    }

    const showModal = (params) => {
        lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + params.url});
    }

    useEffect(() => {
        if (state.conv !== null) {
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
        }
    },[state.remarks]);

    useEffect(() => {
        if (state.conv !== null) {
            const timeout = setTimeout(() => {
                axios.post(WWW_DIR_JAVASCRIPT  + "mailconv/saveremarks/" + props.chatId + '/(type)/customer', {data: state.customer_remarks}).then(result => {
                    dispatch({
                        type: 'update',
                        value: {
                            'saving_customer_remarks': false
                        }
                    });
                });
            }, 500);
            return () => clearTimeout(timeout);
        }
    },[state.customer_remarks]);

    const saveRemarks = (params) => {
        dispatch({
            type: 'update',
            value: {
                'saving_remarks': true,
                'remarks': params
            }
        });
    }

    const saveCustomerRemarks = (params) => {
        dispatch({
            type: 'update',
            value: {
                'saving_customer_remarks': true,
                'customer_remarks': params
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

    const addOpWatching = (watcher) => {
        dispatch({
            type: 'update_watching_ops',
            watcher: watcher
        });
    }

    const setConversationStatus = (status) => {
        dispatch({
            type: 'update_conversation',
            value: {
                'status' : status
            }
        });
    }

    useEffect(() => {
        loadMainData();

        function mailChatModified(chatId) {
            if (props.chatId == chatId) {
                loadMainData();
            }
        }

        function mailLabelsModified(chatId, messageId) {
            if (props.chatId == chatId) {
                updateLabels({"id": messageId});
            }
        }

        function mailOpWatching(mail) {
            if (props.chatId == mail.id) {
                addOpWatching(mail);
            }
        }

        ee.addListener('mailChatModified', mailChatModified);
        ee.addListener('mailLabelsModified', mailLabelsModified);
        ee.addListener('mailMerged', mailChatModified);
        ee.addListener('mail.op_watching', mailOpWatching);

        return function cleanup() {
           ee.removeListener('mailChatModified', mailChatModified);
           ee.removeListener('mailLabelsModified', mailLabelsModified);
           ee.removeListener('mail.op_watching', mailOpWatching);
           ee.emitEvent('mailChatContentUnLoaded', [props.chatId]);
           forgetChat(props.chatId)
        };
    },[]);

    useEffect(() => {
        if (state.loaded == true) {
            var container = tabsContainer.current;
            ee.emitEvent('mailChatContentLoaded', [props.chatId]);
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

                    {props.mode !== 'preview' && <h1 className="pb-2">
                        <i className="material-icons">{state.conv.start_type == 1 ? 'call_made' : 'call_received'}</i>{state.conv.undelivered && <i title="Undelivered e-mail" className="text-danger material-icons">sms_failed</i>}{state.conv.subject}
                    </h1>}

                    <div>
                        {state.messages.map((message, index) => {
                            if (state.expand_messages == true ||            // Render all if requested
                            state.messages.length < 5 ||                    // Render all if less than 5
                            index == 0 ||                                   // Render first
                            state.messages.length == (index + 1) ||         // Render last one
                            state.messages.length - 2 == index) {           // Render before last one
                                    return <React.Fragment key={'msg-mail-hold-' + props.chatId + '_' + index + '_' + message.id}>{state.expand_messages == false && state.messages.length >= 5 && state.messages.length - 2 == index && <div className="previous-mails-row" onClick={() => dispatch({type: 'update',value: {'expand_messages': true}})} ><span className="previous-number" title={t('mail.previous_messages')}>{state.messages.length - 3}</span></div>}<MailChatMessage setConversationStatus={(e) => setConversationStatus(e)}
                                                            verifyOwner={(e) => verifyOwner(e)} moptions={state.moptions}
                                                            fetchMessages={(e) => fetchMessages(message)}
                                                            fetchingMessages={state.fetching_messages} mode={props.mode}
                                                            key={'msg_mail_' + props.chatId + '_' + index + '_' + message.id}
                                                            totalMessages={state.messages.length} index={index} message={message}
                                                            noReplyRequired={(e) => noReplyRequired(message)}
                                                            addLabel={(e) => addLabel(message)}
                                                            updateMessages={(e) => loadMainData()}
                                                            keyword={props.keyword}
                                                            loadMessageBody={(data) => loadMessageBody(message, data)}/></React.Fragment>;
                            }
                        })}

                        {state.fetching_messages && <div className="alert alert-success p-1 ps-2" role="alert">{t('mail.send_fetching')}</div>}
                    </div>
                </div>
                <div className={"chat-main-right-column " + (props.mode == 'preview' ? 'd-none' : 'col-5')}>
                    <div role="tabpanel">
                        <ul className="nav nav-pills" role="tablist" ref={tabsContainer}>
                            <li role="presentation" className="nav-item"><a className="nav-link active" href={"#mail-chat-info-"+props.chatId} aria-controls={"#mail-chat-info-"+props.chatId} title={t('mail.information')} role="tab" data-bs-toggle="tab"><i className="material-icons me-0">info_outline</i></a></li>
                            <li role="presentation" className="nav-item"><a className="nav-link" href={"#mail-chat-remarks-"+props.chatId} aria-controls={"#mail-chat-remarks-"+props.chatId} role="tab" data-bs-toggle="tab" title={t('mail.remarks')}><i className="material-icons me-0">mode_edit</i></a></li>
                        </ul>
                        <div className="tab-content">
                            <div role="tabpanel" className="tab-pane" id={"mail-chat-remarks-"+props.chatId}>

                                <div className={"pb-1 text-success" + (state.saving_customer_remarks ? ' text-warning' : '')}><span className="material-icons">mode_edit</span> Customer remarks</div>
                                <div>
                                    <textarea disabled={!state.moptions.can_write} placeholder="Enter your remarks here." onKeyUp={(e) => saveCustomerRemarks(e.target.value)} className="form-control mh150" defaultValue={state.customer_remarks}></textarea>
                                </div>

                                <div className={"pb-1 text-success" + (state.saving_remarks ? ' text-warning' : '')}><span className="material-icons">mode_edit</span> Conversation remarks</div>
                                <div>
                                    {state.conv && <textarea disabled={!state.moptions.can_write} placeholder="Enter your remarks here." onKeyUp={(e) => saveRemarks(e.target.value)} className="form-control mh150" defaultValue={state.conv.remarks}></textarea>}
                                </div>

                            </div>
                            <div role="tabpanel" className="tab-pane active" id={"mail-chat-info-"+props.chatId}>

                                {state.moptions.can_close && <div className="pb-2">

                                    {state.close_mode == false && <a className="btn btn-outline-secondary btn-sm" onClick={() => dispatch({type: 'update',value: {'close_mode': true}})}><i className="material-icons">close</i>{t('mail.close')}</a>}

                                    {state.close_mode == true && <p className="fs14 mb-2">{t('mail.sure_close')}</p>}

                                    {state.close_mode == true && <div className="btn-group">
                                            <button type="button" className="btn btn-danger btn-sm" onClick={() => closeConversation()}>{t('mail.yes')}</button>
                                            <button type="button" className="btn btn-success btn-sm" onClick={() => dispatch({type: 'update',value: {'close_mode': false}})}>{t('mail.no')}</button>
                                        </div>}

                                </div>}

                                <div id={"mail-external-details-"+props.chatId}></div>

                                {state.conv && <table className="table table-sm">
                                    <tbody>
                                    <tr>
                                        <td colSpan="2">

                                            <div className="row">
                                                <div className="col-6">
                                                    <span className="action-image" onClick={() => showModal({url: "mailconv/mailhistory/" + props.chatId})}><i className="material-icons">history</i>{t('mail.interactions_history')}</span>
                                                </div>
                                                {state.moptions.can_write && <div className="col-6">
                                                    <span className="action-image" onClick={() => showModal({url: "mailconv/transfermail/" + props.chatId})}><i className="material-icons">supervisor_account</i>{t('mail.transfer_chat')}</span>
                                                </div>}
                                                {state.moptions.can_write && <div className="col-6">
                                                    <span className="action-image" onClick={() => showModal({url: "mailconv/merge/" + props.chatId})}><i className="material-icons">merge_type</i>{t('mail.merge_chat')}</span>
                                                </div>}
                                                <div className="col-6">
                                                    <a className="text-dark" target="_blank" href={WWW_DIR_JAVASCRIPT  + "mailconv/mailprintcovnersation/" + props.chatId} ><i className="material-icons">print</i>{t('mail.print')}</a>
                                                </div>
                                                {state.moptions.can_write && state.conv.can_delete && <div className="col-6">
                                                    <span className="action-image me-0" onClick={(e) => deleteConversation()}><i className="material-icons">delete</i>{t('mail.delete')}</span>
                                                </div>}
                                           </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{t('mail.sender')}</td>
                                        <td>
                                            {state.conv.lang && state.moptions.lang_dir && <img src={state.moptions.lang_dir + '/' + state.conv.lang + '.png'}/>} {state.conv.from_name} &lt;{state.conv.from_address}&gt; <span className={"action-image" + (state.moptions.is_blocked ? " text-danger fw-bold" : "")} onClick={() => showModal({url: "mailconv/blocksender/" + props.chatId})}><span className="material-icons">block</span>{state.moptions.is_blocked ? t('mail.is_blocked') : t('mail.block')}</span>
                                        </td>
                                    </tr>
                                    {(state.conv.phone || state.conv.phone_front) && <tr>
                                        <td>{t('mail.phone')}</td>
                                        <td>
                                            {state.conv.phone && <a href={"tel:"+state.conv.phone}><span className="material-icons">dialpad</span>{state.conv.phone_front}</a>}
                                            {!state.conv.phone && <React.Fragment><span className="material-icons">dialpad</span>{state.conv.phone_front}</React.Fragment>}
                                        </td>
                                    </tr>}
                                    <tr>
                                        <td colSpan="2">

                                            {!state.conv.status && <span><i className="material-icons chat-pending">mail_outline</i>{t('status.pending')}</span>}

                                            {(!state.conv.status || state.conv.status == 1) && state.conv.opened_at && <span><span className="ms-2 material-icons text-success" title={t('status.opened_at')}>visibility</span>{state.conv.opened_at_front}</span>}

                                            {(state.conv.status == 2 || state.conv.status == 1) && <div className="input-group input-group-sm">
                                                <span className="input-group-text">
                                                    {!state.conv.status && <i className="material-icons chat-pending me-0">mail_outline</i>}
                                                    {state.conv.status == 1 && <i className="material-icons chat-active me-0">mail_outline</i>}
                                                    {state.conv.status == 2 && <i className="material-icons chat-closed me-0">mail_outline</i>}
                                                </span>
                                                <select disabled={state.moptions.is_archive} className="form-control form-control-sm" value={state.conv.status} onChange={(e) => changeStatus(e)}>
                                                    <option value="">{t('status.pending')}</option>
                                                    <option value="1">{t('status.active')}</option>
                                                    {state.conv.status == 2 && <option value="2">{t('status.closed')}</option>}
                                                </select>
                                                {state.conv.opened_at && <span className="input-group-text"><span className="ms-2 material-icons text-success" title={t('status.opened_at')}>visibility</span>{state.conv.opened_at_front}</span>}
                                            </div>}


                                        </td>
                                    </tr>
                                    <tr>
                                        <td>{t('mail.department')}</td>
                                        <td>
                                            <span title={state.conv.dep_id}><span className="material-icons">home</span>{state.conv.department_name}</span> <span className={state.moptions.can_change_mailbox ? "action-image" : ""} onClick={() => state.moptions.can_change_mailbox && showModal({url: "mailconv/changemailbox/" + props.chatId})} title={state.conv.mailbox_id}>
                                            <span className="material-icons">email</span>
                                            {state.conv.mailbox_front.name} ({state.conv.mailbox_front.mail})
                                            </span>
                                        </td>
                                    </tr>



                                    <tr>
                                        <td>{t('mail.received')}</td>
                                        <td>{state.conv.udate_front}</td>
                                    </tr>
                                    <tr>
                                        <td>ID</td>
                                        <td>
                                            <button data-success={t('mail.copied')}
                                                    className="btn ps-0 btn-xs btn-link py-0"
                                                    data-copy={window.location.origin + WWW_DIR_JAVASCRIPT + "front/default/(mid)/" + state.conv.id + "/#!#chat-id-mc"+state.conv.id}
                                                    onClick={(e) => {lhinst.copyContent($(e.target))}} type="button"><i className="material-icons">link</i>{t('mail.copy_link')}
                                            </button>
                                            {state.conv.id}
                                            {state.conv.follow_up_id && <a target="_blank" href={WWW_DIR_JAVASCRIPT  + "mailconv/view/" + state.conv.follow_up_id}><span className="material-icons">launch</span>{t('mail.follow_up')} {state.conv.follow_up_id}</a>}
                                        </td>
                                    </tr>
                                    {state.conv.accept_time && <tr>
                                        <td>{t('mail.last_accepted_at')}</td>
                                        <td>{state.conv.accept_time_front}{state.conv.wait_time_pending && <React.Fragment> | {t('mail.wait_time')} {state.conv.wait_time_pending}</React.Fragment>}</td>
                                    </tr>}
                                    {state.conv.response_time && <tr>
                                        <td>{t('mail.last_responded_at')}</td>
                                        <td>{state.conv.lr_time_front}{state.conv.wait_time_response && <React.Fragment> | {t('mail.wait_time')} {state.conv.wait_time_response}</React.Fragment>}</td>
                                    </tr>}
                                    {state.conv.cls_time && <tr>
                                        <td>{t('mail.closed_at')}</td>
                                        <td>{state.conv.cls_time_front}</td>
                                    </tr>}
                                    {state.conv.conv_duration && <tr>
                                        <td>{t('mail.conv_duration')}</td>
                                        <td>{state.conv.conv_duration_front}</td>
                                    </tr>}
                                    {state.conv.interaction_time && <tr>
                                        <td>{t('mail.last_interaction_time')}</td>
                                        <td>{state.conv.interaction_time_duration}</td>
                                    </tr>}
                                    {state.conv.priority && <tr>
                                        <td>{t('mail.priority')}</td>
                                        <td>{state.conv.priority}</td>
                                    </tr>}
                                    <tr>
                                        <td title={state.conv.user_id}>{t('mail.chat_owner')}</td>
                                        <td>{state.conv.plain_user_name}
                                            {state.op_watching.map((op_watching, index) => {
                                                return <span title={t('mail.op_watching')} className="ms-1 mail-watcher"><span className="material-icons text-success">visibility</span>{op_watching.name_official}</span>
                                            })}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>}

                                <div id={"mail-external-below-"+props.chatId}></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </React.Fragment>
    );
}

export default MailChat