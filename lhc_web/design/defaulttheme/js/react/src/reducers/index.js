import { combineReducers } from "redux";
import nodeGroupReducer from "./nodeGroupReducer";

export default combineReducers({ nodegroups: nodeGroupReducer });