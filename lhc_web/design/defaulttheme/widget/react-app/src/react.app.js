import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { Provider } from "react-redux";
import store from "./store/index";

// import i18n (needs to be bundled ;))
import './i18n';

var root = document.getElementById('root');

ReactDOM.render(
    <Provider store={store}>
        <App {...(root.dataset)} />
    </Provider>,
    root
);