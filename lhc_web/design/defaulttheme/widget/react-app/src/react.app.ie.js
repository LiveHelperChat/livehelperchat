import 'core-js/stable';
import 'custom-event-polyfill';

import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { Provider } from "react-redux";
import store from "./store/index";

var root = document.getElementById('root');

ReactDOM.render(
    <Provider store={store}>
        <App {...(root.dataset)} />
    </Provider>,
    root
);