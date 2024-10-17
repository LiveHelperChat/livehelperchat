import React from 'react';
import { Suspense, lazy } from 'react';
import i18n from "./components/i18n/i18n";
import { createRoot } from 'react-dom/client';


const CannedMessages = React.lazy(() => import('./components/CannedMessages'));
const MailChat = React.lazy(() => import('./components/MailChat'));
const DashboardChatTabs = React.lazy(() => import('./components/DashboardChatTabs'));
const GroupChat = React.lazy(() => import('./components/GroupChat'));

// set webpack loading path
__webpack_public_path__ = WWW_DIR_LHC_WEBPACK_ADMIN;

let rootScopes = {};

ee.addListener('adminChatLoaded',(chatId) => {
    let scope = 'canned-messages-chat-container-'+chatId;
    var el = document.getElementById(scope);
    if (el !== null) {
        rootScopes[scope] = createRoot(el);
        rootScopes[scope].render(<Suspense fallback="..."><CannedMessages chatId={chatId}/></Suspense>);
    }
})

ee.addListener('groupChatTabLoaded',(chatId) => {
    let scope = 'chat-id-'+chatId;
    var el = document.getElementById(scope);
    if (el !== null) {
        chatId = chatId.replace('gc','');
        rootScopes[scope] = createRoot(el);
        rootScopes[scope].render( <Suspense fallback="..."><GroupChat chatId={chatId} userId={confLH.user_id} /></Suspense>);
    }
})

ee.addListener('mailChatTabLoaded',(chatId, modeChat, disableRemember, keyword) => {
    modeChat = (typeof modeChat != 'undefined' ? modeChat : '');
    disableRemember = (typeof disableRemember != 'undefined' ? disableRemember : false);
    keyword = (typeof keyword != 'undefined' ? keyword : []);
    let scope = 'chat-id-' + modeChat + chatId;
    var el = document.getElementById(scope);
    if (el !== null) {
        chatId = chatId.replace('mc','');
        rootScopes[scope] = createRoot(el);
        rootScopes[scope].render( <Suspense fallback="..."><MailChat chatId={chatId} keyword={keyword} userId={confLH.user_id} mode={modeChat} disableRemember={disableRemember} /></Suspense>);
    }
})

ee.addListener('privateChatStart',(chatId, params) => {
    let scope = 'private-chat-tab-root-'+chatId;
    var el = document.getElementById(scope);
    if (el !== null) {
        rootScopes[scope] = createRoot(el);
        rootScopes[scope].render(<Suspense fallback="..."><GroupChat paramsStart={params || {}} chatPublicId={chatId} userId={confLH.user_id} /></Suspense>);
    }
})

ee.addListener('unloadGroupChat', (chatId) => {
    let scope = 'chat-id-'+chatId;
    var el = document.getElementById(scope);
    if (el !== null && rootScopes[scope]) {
        rootScopes[scope].unmount();
        rootScopes[scope] = null;
        delete rootScopes[scope];
    }
});


ee.addListener('unloadMailChat', (chatId, modeChat) => {
    modeChat = (typeof modeChat != 'undefined' ? modeChat : '');
    let scope = 'chat-id-'+modeChat+chatId;
    var el = document.getElementById(scope);
    if (el !== null && rootScopes[scope]) {
        rootScopes[scope].unmount();
        rootScopes[scope] = null;
        delete rootScopes[scope];
    }
});

ee.addListener('removeSynchroChat', (chatId) => {
    // Canned messages component
    let scope = 'canned-messages-chat-container-'+chatId;
    var el = document.getElementById(scope);
    if (el !== null && rootScopes[scope]) {
        rootScopes[scope].unmount();
        rootScopes[scope] = null;
        delete rootScopes[scope];
    }
    // Private chat component
    scope = 'private-chat-tab-root-'+chatId;
    el = document.getElementById(scope);
    if (el !== null && rootScopes[scope]) {
        rootScopes[scope].unmount();
        rootScopes[scope] = null;
        delete rootScopes[scope];
    }
});


$(document).ready(function(){
    var el = document.getElementById('tabs-dashboard');
    var elOrderMode = document.getElementById('chats-order-mode');
    var elOrder = elOrderMode && elOrderMode.getAttribute('data-mode');

    if (el !== null) {
        const root = createRoot(el);
        root.render(<Suspense fallback="..."><DashboardChatTabs static_order={elOrder == 'static'} /></Suspense>);
    }

    try {
        if (localStorage) {
                var achat_id_array = [];
                var achat_id = localStorage.getItem('gachat_id');

                if (achat_id !== null && achat_id !== '') {
                    achat_id_array = achat_id.split(',');
                    achat_id_array.forEach((chatId) => {
                        if ($('#tabs').length > 0) {
                            return lhinst.startGroupChat(chatId, $('#tabs'), LiveHelperChatFactory.truncate(name,10), true);
                        }
                    });
                }
        }
    } catch(e) {

    }
});