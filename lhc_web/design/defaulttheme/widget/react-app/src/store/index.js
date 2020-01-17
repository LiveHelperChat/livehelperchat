import { applyMiddleware, createStore } from "redux";
import rootReducer from "../reducers/index";

import thunk from "redux-thunk"
import promise from "redux-promise-middleware"

//import {logger} from "redux-logger"
//import { createLogger } from 'redux-logger'

import addChatWidgetListener from './chatWidgetListener';

const middleware = applyMiddleware(promise, thunk/*, logger*/)

const store = createStore(
    rootReducer,
    middleware
);

addChatWidgetListener(store.dispatch, store.getState);

export default store;