import React from 'react';
import ReactDOM from 'react-dom';
import { Suspense, lazy } from 'react';
import i18n from "./components/i18n/i18n";

const CannedMessages = React.lazy(() => import('./components/CannedMessages'));
const GroupChat = React.lazy(() => import('./components/GroupChat'));

// set webpack loading path
__webpack_public_path__ = WWW_DIR_LHC_WEBPACK_ADMIN;

ee.addListener('adminChatLoaded',(chatId) => {
    var el = document.getElementById('canned-messages-chat-container-'+chatId);

    if (el !== null) {
        ReactDOM.render(
            <Suspense fallback="..."><CannedMessages chatId={chatId}/></Suspense>,
            el
        );
    }
})

ee.addListener('groupChatTabLoaded',(chatId) => {
    var el = document.getElementById('chat-id-'+chatId);
    if (el !== null) {
        chatId = chatId.replace('gc','');
        ReactDOM.render(
            <Suspense fallback="..."><GroupChat chatId={chatId} userId={confLH.user_id} /></Suspense>,
            el
        );
    }
})

ee.addListener('unloadGroupChat', (chatId) => {
    var el = document.getElementById('chat-id-'+chatId);
    if (el !== null) {
        ReactDOM.unmountComponentAtNode(el)
    }
});

ee.addListener('removeSynchroChat', (chatId) => {
    var el = document.getElementById('canned-messages-chat-container-'+chatId);

    if (el !== null) {
        ReactDOM.unmountComponentAtNode(el)
    }
});

$(document).ready(function(){

    if (localStorage) {
        try {
            var achat_id_array = [];
            var achat_id = localStorage.getItem('gachat_id');

            if (achat_id !== null && achat_id !== '') {
                achat_id_array = achat_id.split(',');
                achat_id_array.forEach((chatId) => {
                    if ($('#tabs').length > 0) {
                        return lhinst.startGroupChat(chatId,$('#tabs'),LiveHelperChatFactory.truncate(name,10));
                    }
                });
            }
        } catch(e) {

        }
    }

});