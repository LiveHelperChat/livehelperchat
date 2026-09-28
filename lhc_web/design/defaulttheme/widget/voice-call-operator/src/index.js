import React from 'react';
import ReactDOM from 'react-dom';
import { Suspense } from 'react';
import i18n from "./components/i18n/i18n";

const VoiceCall = React.lazy(() => import('./components/VoiceCall'));

// set webpack loading path
__webpack_public_path__ = WWW_DIR_LHC_WEBPACK_ADMIN;

var el = document.getElementById('root');
if (el !== null) {
    // Media SDK is loaded only when call window is opened
    import(/* webpackChunkName: "provider-livekit" */ './providers/livekit').then(module => {
        const provider = module.default();
        ReactDOM.render(
            <Suspense fallback="..."><VoiceCall isVisitor={window.initParams.isVisitor} initParams={window.initParams} provider={provider}></VoiceCall></Suspense>,
            el
        );
    });
}
