import { combineReducers } from "redux";
import nodeGroupReducer from "./nodeGroupReducer";
import nodeGroupTriggerReducer from "./nodeGroupTriggerReducer";

export default combineReducers({ nodegroups: nodeGroupReducer,  nodegrouptriggers: nodeGroupTriggerReducer});