import { applyMiddleware, createStore } from "redux";
import rootReducer from "../reducers/index";
import {logger} from "redux-logger"
import thunk from "redux-thunk"
import promise from "redux-promise-middleware"
import { createLogger } from 'redux-logger'

const middleware = applyMiddleware(promise(), thunk/*, logger*/)

const store = createStore(
    rootReducer,
    middleware
);

export default store;