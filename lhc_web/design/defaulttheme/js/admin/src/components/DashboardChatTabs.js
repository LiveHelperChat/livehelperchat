import React, { useEffect, useState, useReducer, useRef } from "react";
import axios from "axios";
import {useTranslation} from 'react-i18next';
import useInterval from "./lib/useInterval";

function reducer(state, action) {
    switch (action.type) {

        case 'attr':
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.chats[foundIndex] = { ...state.chats[foundIndex], ...action.value};
            state = { ... state};
            return state;

        case 'attr_remove':
            var foundIndex = state.chats.findIndex(x => x[action.attr] == action.id);
            if (foundIndex === -1) return state;
            state.chats[foundIndex] = { ...state.chats[foundIndex], ...action.value};
            state = { ... state};
            return state;

        case 'attr_remove_mail':
            var foundIndex = state.mails.findIndex(x => x[action.attr] == action.id);
            if (foundIndex === -1) return state;
            state.mails[foundIndex] = { ...state.mails[foundIndex], ...action.value};
            state = { ... state};
            return state;

        case 'update': {
            return { ...state, ...action.value }
        }

        case 'add': {
            var foundIndex = state.chats.findIndex(x => x.id == action.value.id);
            if (foundIndex === -1) {
                if (action.static_order === true) {

                    var insertIndex = state.chats.findIndex(x => x.id > action.value.id);

                    if (insertIndex === -1) {
                        state.chats.push(action.value);
                    } else {
                        state.chats.splice(insertIndex, 0, action.value);
                    }

                } else {
                    state.chats.unshift(action.value);
                }
            } else {
                state.chats[foundIndex].active = action.value.active;
                state.chats[foundIndex].mn = 0;
                state.chats[foundIndex].support_chat = false;
            }

            return { ...state}
        }

        case 'add_mail': {
            var foundIndex = state.mails.findIndex(x => x.id == action.value.id);
            if (foundIndex === -1) {
                state.mails.unshift(action.value);
            } else {
                state.mails[foundIndex].active = true;
            }
            return { ...state}
        }

        case 'remove': {
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.chats.splice(foundIndex,1);
            return { ...state}
        }

        case 'remove_mail': {
            var foundIndex = state.mails.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.mails.splice(foundIndex,1);
            return { ...state}
        }

        case 'update_chat': {
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.chats[foundIndex] = {...state.chats[foundIndex], ...action.value}
            return { ...state}
        }

        case 'update_mail': {
            var foundIndex = state.mails.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.mails[foundIndex] = {...state.mails[foundIndex], ...action.value}
            return { ...state}
        }

        case 'msg_received': {
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;

            state.chats[foundIndex].msg = action.value.msg;

            var el = document.getElementById('chat-tab-link-'+action.id);

            if (el === null || !el.classList.contains('active')) {
                state.chats[foundIndex].active = false;
            } else {
                state.chats[foundIndex].active = true;
            }

            state.chats[foundIndex].mn = state.chats[foundIndex].active == false ? (state.chats[foundIndex].mn ? (state.chats[foundIndex].mn + action.value.mn) : action.value.mn) : 0;

            // Push to very first if it's not static order
            if (action.static_order === false) {
                // Set last appended messages as first array element
                state.chats.splice(0, 0, state.chats.splice(foundIndex, 1)[0]);
            }

            return { ...state}
        }

        case 'refocus_mail': {
            var foundIndex = state.chats.findIndex(x => x.active == true);
            if (foundIndex !== -1) {
                state.chats[foundIndex].active = false;
            }
            var foundIndex = state.mails.findIndex(x => x.active == true);
            if (foundIndex !== -1) {
                if (action.id == state.mails[foundIndex].id) {
                    return state;
                }
                state.mails[foundIndex].active = false;
            }

            var foundIndex = state.mails.findIndex(x => x.id == action.id);
            if (foundIndex !== -1) {
                state.mails[foundIndex].active = true;
            }

            return { ...state}
        }

        case 'refocus': {
            // Unselect all active mails
            state.mails.forEach((mail, index) => {
                if (mail.active) {
                    state.mails[index].active = false;
                }
            });
            
            // Unselect all active chats except the target one
            state.chats.forEach((chat, index) => {
                if (chat.active && chat.id != action.id) {
                    state.chats[index].active = false;
                }
            });

            // Activate the target chat
            var targetChatIndex = state.chats.findIndex(x => x.id == action.id);
            if (targetChatIndex !== -1) {
                state.chats[targetChatIndex].active = true;
                state.chats[targetChatIndex].mn = 0;
                state.chats[targetChatIndex].support_chat = false;
            }

            return { ...state}
        }

        case 'group_offline':
            state.group_offline = action.value;
            return {...state};

        default:
            throw new Error('Unknown action!');
    }
}

const DashboardChatTabs = props => {

    const [state, dispatch] = useReducer(reducer, {
        chats: [],
        mails: [],
        group_offline : false
    });

    const chatsRef = useRef(state);

    useEffect(
        () => { chatsRef.current = state },
        [state]
    )

    const getChatIds = () => {
        var chatIds = [];
        state.chats.map((chat, index) => chatIds.push(chat.id));
        return chatIds;
    }

    const loadChatTabIntro = (chatIds) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "front/tabs/(id)/" + (typeof chatIds !== 'undefined' ? chatIds.join('/') : getChatIds().join('/'))).then(result => {

            result.data.map((chat, index) => {

                // If nodeJS extension is enabled check chat live status
                // As on page reload react app can be yet not started and we might not receive event
                // at that moment react app starts
                var nodeJSStatus = document.getElementById('node-js-indicator-'+chat.id);
                if (nodeJSStatus !== null) {
                    chat.live_status = nodeJSStatus.textContent == 'wifi';
                }

                if (!chat.nb) {
                    chat.nb = false;
                }

                if (!chat.nc) {
                    chat.nc = null;
                }

                if (!chat.aicons) {
                    chat.aicons = {};
                }

                dispatch({
                    type: 'update_chat',
                    id: chat.id,
                    value: chat
                })

            })
        });
    }


    const loadMailTabIntro = (chatIds) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "front/tabs/(idmail)/" + (typeof chatIds !== 'undefined' ? chatIds.join('/') : getChatIds().join('/'))).then(result => {
            result.data.map((chat, index) => {
                dispatch({
                    type: 'update_mail',
                    id: chat.id,
                    value: chat
                })
            })
        });
    }

    if (!document.getElementById('tabs') && !document.getElementById('views-home')) {
        useInterval(() => {

            if (!state.chats || state.chats.length == 0) {
                return;
            }

            axios.get(WWW_DIR_JAVASCRIPT  + "front/tabs/(id)/" + getChatIds().join('/')).then(result => {
                result.data.map((chat, index) => {

                    // If nodeJS extension is enabled check chat live status
                    // As on page reload react app can be yet not started and we might not receive event
                    // at that moment react app starts
                    var nodeJSStatus = document.getElementById('node-js-indicator-'+chat.id);
                    if (nodeJSStatus !== null) {
                        chat.live_status = nodeJSStatus.textContent == 'wifi';
                    }

                    if (!(!state.chats || state.chats.length == 0)) {
                        var foundIndex = state.chats.findIndex(x => x.id == chat.id);
                        if (foundIndex !== -1 ) {
                            if (state.chats[foundIndex].lmsg_id !== chat.lmsg_id) {
                                chat.mn = 1;
                            } else {
                                chat.mn = state.chats[foundIndex].mn;
                            }
                        }
                    }

                    dispatch({
                        type: 'update_chat',
                        id: chat.id,
                        value: chat
                    })

                })
            });

        }, 1000);
    }

    useEffect(() => {

        function addTabPreload(chatId, params) {

            if (!(!chatsRef.current.chats || chatsRef.current.chats.length == 0) && chatsRef.current.chats.findIndex(x => x.id == chatId) !== -1) {
                return; // We already have this chat tab
            }

            addTab(chatId, params);
        }

        function addTab(chatId, params) {
            if (params.focus) {

                dispatch({
                    type: 'attr_remove',
                    id: true,
                    attr: 'active',
                    value: {
                        "active" : false
                    }
                });

                dispatch({
                    type: 'attr_remove_mail',
                    id: true,
                    attr: 'active',
                    value: {
                        "active" : false
                    }
                });
            }

            dispatch({
                type: 'add',
                static_order: props.static_order,
                value: {
                    "id" : chatId,
                    active: params.focus
                }
            });
            loadChatTabIntro([chatId]);
        }

        function addTabBackground(chatId, params) {
            dispatch({
                type: 'add',
                value: {
                    "id" : chatId,
                    static_order: props.static_order,
                    active: false,
                    mn : 1
                }
            });
            loadChatTabIntro([chatId]);
        }

        function updateChatTab(params) {
            if (!(!chatsRef.current.chats || chatsRef.current.chats.length == 0) && chatsRef.current.chats.findIndex(x => x.id == params.id) !== -1) {
                loadChatTabIntro([params.id]);
            }
        }

        function mailContentLoaded(chatId) {
            addMailTab(chatId);
        }

        function addMailTab(chatId, params) {
            dispatch({
                type: 'attr_remove_mail',
                id: true,
                attr: 'active',
                value: {
                    "active" : false
                }
            });

            dispatch({
                type: 'attr_remove',
                id: true,
                attr: 'active',
                value: {
                    "active" : false
                }
            });

            dispatch({
                type: 'add_mail',
                value: {
                    "id" : chatId.replace('mc',''),
                    active: (typeof params !== 'undefined' && params.focus === true)
                }
            });

            loadMailTabIntro([chatId.replace('mc','')]);
        }

        function removeTab(chatId) {
            dispatch({
                type: 'remove',
                id: chatId
            });
        }

        function removeMailTab(chatId) {
            dispatch({
                type: 'remove_mail',
                id: chatId.replace('mc','')
            });
        }

        function tabClicked(chatId) {

            if (typeof chatId == 'string' && chatId.indexOf('mc') !== -1) {
                mailTabClicked(chatId.replace('mc',''));
                return;
            }

            dispatch({
                type: 'refocus',
                id: chatId
            });
        }

        function mailTabClicked(chatId) {
            dispatch({
                type: 'refocus_mail',
                id: chatId
            });
        }

        function chatAdminSync(data) {

            dispatch({
                type: 'group_offline',
                value: lhinst.hidenicknamesstatus
            })

            Object.keys(data.result_status).map((key) => {
                dispatch({
                    type: 'update_chat',
                    id: data.result_status[key].chat_id,
                    value: data.result_status[key]
                })
            });

            if (data.result !== 'false') {
                Object.keys(data.result).map((key) => {
                    dispatch({
                        type: 'msg_received',
                        id: data.result[key].chat_id,
                        value: {msg: data.result[key].msg, mn: data.result[key].mn},
                        order_chats: props.static_order
                    })
                });
            }
        }

        function supportUnreadChat(params) {
            if (params.id && params.unread == true) {
                dispatch({
                    type: 'update_chat',
                    id: params.id,
                    value: {support_chat: true}
                })
            }
        }

        function typingVisitor(params) {
            dispatch({
                type: 'update_chat',
                id: params.id,
                value: {tp: 'true','tx' : params.txt}
            })
        }

        function typingVisitorStopped(params) {
            dispatch({
                type: 'update_chat',
                id: params.id,
                value: {tp: 'false'}
            })
        }

        function nodeJsVisitorStatus(params) {
            dispatch({
                type: 'update_chat',
                id: params.id,
                value: {live_status: params.status}
            })
        }

        function activateNextTab(chatid,up) {
            var index = chatsRef.current.chats.findIndex(x => x.active == true);
            if (index === -1) { return; }
            if ((chatsRef.current.chats.length - 1) > index && up == false) {
                chatTabClick(chatsRef.current.chats[index + 1]);
            } else if (index > 0 && up == true) {
                chatTabClick(chatsRef.current.chats[index - 1]);
            }
        }

        ee.addListener('chatStartTab',addTab)
        ee.addListener('chatTabPreload',addTabPreload)
        ee.addListener('chatStartBackground',addTabBackground)
        ee.addListener('removeSynchroChat',removeTab)
        ee.addListener('chatTabClicked',tabClicked)
        ee.addListener('chatTabFocused',tabClicked)
        ee.addListener('chatAdminSync',chatAdminSync)
        ee.addListener('supportUnreadChat',supportUnreadChat)
        ee.addListener('nodeJsTypingVisitor',typingVisitor)
        ee.addListener('nodeJsTypingVisitorStopped',typingVisitorStopped)
        ee.addListener('nodeJsVisitorStatus',nodeJsVisitorStatus)
        ee.addListener('activateNextTab',activateNextTab)
        ee.addListener('updateChatTab',updateChatTab)

        // Mail module
        ee.addListener('unloadMailChat',removeMailTab)
        ee.addListener('mailChatTabLoaded',addMailTab)
        ee.addListener('mailChatTabClicked',mailTabClicked)
        ee.addListener('mailChatContentLoaded',mailContentLoaded)

        if (localStorage) {
            var achat_id = localStorage.getItem('achat_id');
            if (achat_id !== null && achat_id !== '') {
                var ids = achat_id.split(',').map(Number);

                if (props.static_order == true) {
                    ids.sort(function(a, b){return a - b});
                }

                var entries = [];
                ids.forEach((id) => {
                   var el = document.getElementById('chat-tab-link-'+id);
                   if (parseInt(id) > 0) {
                       entries.push({id: parseInt(id), active: el !== null && el.classList.contains('active')})
                   }
               });
               dispatch({
                    type: 'update',
                    value: {
                        "chats" : entries
                    }
                });
               ids.length > 0 && loadChatTabIntro(ids);

               // Find active chat
               setTimeout(() => {
                   ids.forEach((id) => {
                       var el = document.getElementById('chat-tab-link-'+id);
                       if (el !== null) {
                           el.classList.contains('active') && tabClicked(parseInt(id));
                       }
                   });
               },1000);
            }

            var achat_id = localStorage.getItem('machat_id');

            if (achat_id !== null && achat_id !== '') {
                var ids = achat_id.split(',');
                var entries = [];
                ids.forEach((id) => {
                   var el = document.getElementById('chat-tab-link-mc'+id);
                   if (el !== null) {
                       var active = el.classList.contains('active');
                       entries.push({id: parseInt(id), active: active})
                   }
               });
               dispatch({
                    type: 'update',
                    value: {
                        "mails" : entries
                    }
                });
               ids.length > 0 && loadMailTabIntro(ids);

               // Find active chat
               setTimeout(() => {
                   ids.forEach((id) => {
                       var el = document.getElementById('chat-tab-link-mc'+id);
                       if (el !== null) {
                           el.classList.contains('active') && mailTabClicked(parseInt(id));
                       }
                   });
               },1000);
            }
         }

        // Cleanup
        return function cleanup() {
            ee.removeListener('chatStartTab', addTab);
            ee.removeListener('chatStartBackground', addTabBackground);
            ee.removeListener('removeSynchroChat', removeTab);
            ee.removeListener('chatTabClicked', tabClicked);
            ee.removeListener('chatTabFocused', tabClicked);
            ee.removeListener('chatAdminSync', chatAdminSync);
            ee.removeListener('supportUnreadChat', chatAdminSync);
            ee.removeListener('nodeJsTypingVisitor', typingVisitor);
            ee.removeListener('nodeJsTypingVisitorStopped', typingVisitorStopped);
            ee.removeListener('nodeJsVisitorStatus', nodeJsVisitorStatus);
            ee.removeListener('activateNextTab', activateNextTab);
            ee.removeListener('updateChatTab',updateChatTab);
            ee.removeListener('unloadMailChat', removeMailTab);
            ee.removeListener('mailChatTabLoaded', addMailTab);
            ee.removeListener('mailChatTabClicked', mailTabClicked);
            ee.removeListener('mailChatContentLoaded', mailContentLoaded);
        };

    },[]);

    const chatTabClick = (chat) => {
        if (document.getElementById('chat-tab-link-'+chat.id) !== null) {
            $('#chat-tab-link-'+chat.id).click();
            (new bootstrap.Tab(document.querySelector('#chat-tab-link-'+chat.id))).show();
            } else {
            document.location = WWW_DIR_JAVASCRIPT + 'front/default/(cid)/' + chat.id + '/#!#chat-id-' + chat.id;
        }
    }

    const mailTabClick = (chat) => {
        $('#chat-tab-link-mc'+chat.id).click();
        (new bootstrap.Tab(document.querySelector('#chat-tab-link-mc'+chat.id))).show();
    }

    const closeDialog = (e,chat) => {
        e.preventDefault();
        e.stopPropagation();
        lhinst.removeDialogTab(chat.id,$('#tabs'),true);
        lhinst.channel && lhinst.channel.postMessage({'action':'close_chat','args':{'chat_id' : chat.id}});
    }

    const closeMailDialog = (e,chat) => {
        e.preventDefault();
        e.stopPropagation();
        lhinst.removeDialogTabMail('mc'+chat.id,$('#tabs'),true)
    }

    const iconClick = (e,icon,chat) => {
        e.preventDefault();
        e.stopPropagation();
        if (icon.has_popup) {
            lhc.revealModal({'url':WWW_DIR_JAVASCRIPT + 'chat/icondetailed/' + chat.id + '/' + icon.icon_id});
        }
    }

    const { t, i18n } = useTranslation('chat_tabs');

    return (
        <React.Fragment>
            {(!state.chats || state.chats.length == 0) && <div className="text-center text-muted p-2"><span className="material-icons">chat</span>{t('chat_tabs.open_chats')}</div>}
            {state.chats.map((chat, index) => (

                <div key={"chat-"+chat.id}  title={chat.id} onClick={() => chatTabClick(chat)} className={"p-1 action-image chat-tabs-row"+(chat.active ? ' chat-tab-active' : '')+(chat.vwa ? ' long-response-chat' : '')} {...(chat.add_attr ? Object.entries(chat.add_attr).reduce((acc, [key, value]) => ({...acc, [`data-${key}`]: value}), {}) : {})}>
                        <div className="fs12">

                            <button type="button" onClick={(e) => closeDialog(e,chat)} className="float-end btn-link m-0 ms-1 p-0 btn btn-xs"><i className="material-icons me-0">close</i></button>
                            {chat.dep && <span className="float-end text-muted text-truncate mw-80px">
                                {chat.cs == 0 && <span title={t('chat_tabs.pending_status')} className="material-icons chat-pending me-0">chat</span>}
                                {chat.cs == 1 && <span title={t('chat_tabs.active_status')} className="material-icons chat-active me-0">chat</span>}
                                {chat.cs == 5 && <span title={t('chat_tabs.bot_status')} className="material-icons chat-active me-0">android</span>}
                                {chat.cs == 2 && <span title={t('chat_tabs.closed_status')} className="material-icons chat-closed me-0">chat</span>}

                                <span className="d-none d-lg-inline"><span className="material-icons" title={chat.dep}>home</span>{chat.dep}</span>

                            </span>}

                            <span className={"material-icons"+(chat.pnd_rsp == true ? ' text-danger' : ' text-success')}>{chat.pnd_rsp == true ? 'call_received' : 'call_made'}</span>
                            {chat.adicons && chat.adicons.map((icon, index) => <span onClick={(event) => iconClick(event,icon,chat)} style={{'color': icon.color}} className="material-icons" title={icon.title}>{icon.icon}</span>)}
                            {chat.aicons && Object.keys(chat.aicons).map((key, index) => <React.Fragment>
                                    {chat.aicons[key].i.includes('/') && <img className="me-1" title={chat.aicons[key].t || chat.aicons[key].i} src={chat.aicons[key].i}/>}
                                    {!chat.aicons[key].i.includes('/') && <span style={{'color': chat.aicons[key].c ? chat.aicons[key].c : "#1d548e;"}} className="material-icons" title={chat.aicons[key].t || chat.aicons[key].i}>{chat.aicons[key].i}</span>}
                                </React.Fragment>)}
                            {chat.vwa && <span title={chat.vwa} className="d-none d-lg-inline material-icons text-danger">timer</span>}
                            {chat.support_chat && <span className="whatshot blink-ani text-warning material-icons">whatshot</span>}<i className={"material-icons "+(typeof chat.live_status === "boolean" ? (chat.live_status === true ? 'icon-user-online' : 'icon-user-offline') : (chat.us == 2 ? "icon-user-away" : (chat.us == 0 ? "icon-user-online" : "icon-user-offline")))}  >{typeof chat.live_status === "boolean" ? (chat.live_status === true ? 'wifi' : 'wifi_off') : (chat.us == 2 ? "wifi_1_bar" : (chat.us == 0 ? "wifi" : "wifi_off"))}</i><i className={"material-icons icon-user-online " + (chat.um == 1 ? "icon-user-offline" : "icon-user-online")}>send</i>{chat.cc && <img className="d-none d-lg-inline" title={chat.cn} src={chat.cc} alt="" />} {(state.group_offline == false || !(chat.us != 0)) && <span  style={{color: chat.nc ? chat.nc : null}}  className={"small-truncate-nick "+(chat.nb || chat.mn > 0 || chat.cs == 0 ? "fw-bold " : '') + (chat.cs == 0 ? 'text-danger' : '')}>{chat.nick || chat.id}</span>}{chat.mn > 0 && <span className="msg-nm ps-1">({chat.mn})</span>}{chat.lmsg && <span className="d-none d-xl-inline text-muted"> {chat.lmsg}</span>}


                            {chat.co == confLH.user_id && <span className="d-none d-lg-inline float-end text-muted"><span title={t('chat_tabs.chat_owner')} className="material-icons">account_box</span></span>}
                        </div>

                        {(chat.msg || (chat.tp == 'true' && chat.tx)) && <div className="fs13 text-muted pt-1">
                            <span title={chat.tp == 'true' && chat.tx ? chat.tx : chat.msg} className={"d-none d-lg-inline-block text-truncate mw-100 "+(chat.mn > 0 ? 'fw-bold' : '')+(chat.tp == 'true' && chat.tx ? ' fst-italic': '')}>
                                {chat.tp == 'true' && chat.tx ? chat.tx : chat.msg}
                            </span>
                        </div>}
                </div>
            ))}

            {state.mails.map((chat, index) => (
                <div key={"mail-"+chat.id} title={chat.id} onClick={() => mailTabClick(chat)} className={"p-1 action-image chat-tabs-row"+(chat.active ? ' chat-tab-active' : '')}>
                    <div className="fs12">
                        <span title={chat.from_name} ><i className="material-icons">mail_outline</i>{chat.from_address}</span>

                        <button type="button" onClick={(e) => closeMailDialog(e,chat)} className="float-end btn-link m-0 ms-1 p-0 btn btn-xs"><i className="material-icons me-0">close</i></button>
                        {chat.dep && <span className="float-end text-muted text-truncate mw-80px"><span className="material-icons">home</span>{chat.dep}</span>}
                        {chat.co == confLH.user_id && <span className="float-end text-muted"><span title={t('chat_tabs.chat_owner')} className="material-icons">account_box</span></span>}
                    </div>
                    <span title={chat.nick} className="fs13 text-muted pt-1 d-inline-block text-truncate mw-100">
                        {chat.nick}
                    </span>
                </div>
            ))}

        </React.Fragment>
    );
}

export default DashboardChatTabs