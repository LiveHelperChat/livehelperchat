import { combineReducers } from "redux";
import chatWidgetReducer from "./chatWidgetReducer";

export default combineReducers({
    chatwidget: chatWidgetReducer
});