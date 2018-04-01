import { combineReducers } from "redux";
import nodeGroupReducer from "./nodeGroupReducer";
import nodeGroupTriggerReducer from "./nodeGroupTriggerReducer";
import currentTriggerReducer from "./currentTriggerReducer";

export default combineReducers({
    nodegroups: nodeGroupReducer,
    nodegrouptriggers: nodeGroupTriggerReducer,
    currenttrigger: currentTriggerReducer
});