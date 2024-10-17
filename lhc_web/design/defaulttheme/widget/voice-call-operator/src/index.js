import React from 'react';
import { Suspense, lazy } from 'react';
import i18n from "./components/i18n/i18n";
import { createRoot } from 'react-dom/client';

const VoiceCall = React.lazy(() => import('./components/VoiceCall'));

// set webpack loading path
__webpack_public_path__ = WWW_DIR_LHC_WEBPACK_ADMIN;

var el = document.getElementById('root');
if (el !== null) {
    const root = createRoot(el);
    root.render(<Suspense fallback="..."><VoiceCall isVisitor={window.initParams.isVisitor} initParams={window.initParams} ></VoiceCall></Suspense>);
}
