import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { Provider } from "react-redux";
import store from "./store/index";
import { createRoot } from 'react-dom/client';

var container = document.getElementById('root');
const root = createRoot(container);
root.render(<Provider store={store}>
    <App {...(root.dataset)} />
</Provider>);