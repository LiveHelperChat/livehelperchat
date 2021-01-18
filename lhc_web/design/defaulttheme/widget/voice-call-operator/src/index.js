import React from 'react';
import ReactDOM from 'react-dom';
import { Suspense, lazy } from 'react';
import i18n from "./components/i18n/i18n";

const VoiceCall = React.lazy(() => import('./components/VoiceCall'));

// set webpack loading path
__webpack_public_path__ = WWW_DIR_LHC_WEBPACK_ADMIN;

var el = document.getElementById('root');
if (el !== null) {
    ReactDOM.render(
        <Suspense fallback="..."><VoiceCall isVisitor={window.initParams.isVisitor} initParams={window.initParams} ></VoiceCall></Suspense>,
        el
    );
}
