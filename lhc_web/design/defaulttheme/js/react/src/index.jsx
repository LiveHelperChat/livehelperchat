import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { Provider } from "react-redux";
import store from "./store/index";

var root = document.getElementById('root');

var dataSet = root.dataset;

dataSet['triggerId'] = 0;
var hash = window.location.hash;
if (hash != '') {
    var matchData = hash.match(/\d+$/);
    if (matchData !== null && matchData[0]) {
        dataSet['triggerId'] = parseInt(matchData[0]);
    }
}

ReactDOM.render(
    <Provider store={store}>
        <App {...(dataSet)} />
    </Provider>,
    root
 );