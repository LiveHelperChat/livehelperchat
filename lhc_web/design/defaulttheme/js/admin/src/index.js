import React from 'react';
import ReactDOM from 'react-dom';
// import CannedMessages from './components/CannedMessages';
import { Suspense, lazy } from 'react';

const CannedMessages = React.lazy(() => import('./components/CannedMessages'));

// set webpack loading path
__webpack_public_path__ = WWW_DIR_LHC_WEBPACK_ADMIN;

ee.addListener('adminChatLoaded',(chatId) => {
    ReactDOM.render(
        <Suspense fallback="..."><CannedMessages chatId={chatId}/></Suspense>,
        document.getElementById('canned-messages-chat-container-'+chatId)
    );
})

ee.addListener('removeSynchroChat', (chatId) => {
    var el = document.getElementById('canned-messages-chat-container-'+chatId);
    if (el !== null) {
        ReactDOM.unmountComponentAtNode(el)
    }
});