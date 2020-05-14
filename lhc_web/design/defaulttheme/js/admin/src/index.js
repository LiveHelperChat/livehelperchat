import React from 'react';
import ReactDOM from 'react-dom';
import { Suspense, lazy } from 'react';

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
            <Suspense fallback="..."><GroupChat chatId={chatId} /></Suspense>,
            el
        );
    }
})

ee.addListener('removeSynchroChat', (chatId) => {
    var el = document.getElementById('canned-messages-chat-container-'+chatId);

    if (el !== null) {
        ReactDOM.unmountComponentAtNode(el)
    }
});