import React, { useEffect, useState, useReducer, useRef, useBoolean } from "react";
import axios from "axios";
import {useTranslation} from 'react-i18next';
import useInterval from "../lib/useInterval";

function reducer(state, action) {
    switch (action.type) {

        case 'attr':
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.chats[foundIndex] = { ...state.chats[foundIndex], ...action.value};
            state = { ... state};
            return state;

        case 'attr_remove':
            var foundIndex = state.chats.findIndex(x => x[action.attr] == action.id);
            if (foundIndex === -1) return state;
            state.chats[foundIndex] = { ...state.chats[foundIndex], ...action.value};
            state = { ... state};
            return state;

        case 'update': {
            return { ...state, ...action.value }
        }

        case 'add': {
            var foundIndex = state.chats.findIndex(x => x.id == action.value.id);
            if (foundIndex === -1) {
                state.chats.unshift(action.value);
            } else {
                state.chats[foundIndex].active = true;
                state.chats[foundIndex].mn = 0;
                state.chats[foundIndex].support_chat = false;
            }

            return { ...state}
        }

        case 'remove': {
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.chats.splice(foundIndex,1);
            return { ...state}
        }

        case 'update_chat': {
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;
            state.chats[foundIndex] = {...state.chats[foundIndex], ...action.value}
            return { ...state}
        }

        case 'msg_received': {
            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex === -1) return state;

            state.chats[foundIndex].msg = action.value.msg;

            var el = document.getElementById('chat-tab-link-'+action.id);

            if (el === null || !el.classList.contains('active')) {
                state.chats[foundIndex].active = false;
            } else {
                state.chats[foundIndex].active = true;
            }

            state.chats[foundIndex].mn = state.chats[foundIndex].active == false ? (state.chats[foundIndex].mn ? (state.chats[foundIndex].mn + action.value.mn) : action.value.mn) : 0;

            // Set last appended messages as first array element
            state.chats.splice(0, 0, state.chats.splice(foundIndex, 1)[0]);

            return { ...state}
        }

        case 'refocus': {
            var foundIndex = state.chats.findIndex(x => x.active == true);
            if (foundIndex !== -1) {
                if (action.id == state.chats[foundIndex].id) {
                    return state;
                }
                state.chats[foundIndex].active = false;
            }

            var foundIndex = state.chats.findIndex(x => x.id == action.id);
            if (foundIndex !== -1) {
                state.chats[foundIndex].active = true;
                state.chats[foundIndex].mn = 0;
                state.chats[foundIndex].support_chat = false;
            }

            return { ...state}
        }

        case 'group_offline':
            state.group_offline = action.value;
            return {...state};

        default:
            throw new Error('Unknown action!');
    }
}

const VoiceCall = props => {

    const [state, dispatch] = useReducer(reducer, {
        chats: [],
        call : {},
        inCall: false
    });

    const STATUS_OP_PENDING = 0;
    const STATUS_OP_JOINED = 1;

    const STATUS_VI_PENDING = 0;
    const STATUS_VI_REQUESTED = 1;
    const STATUS_VI_JOINED = 2;

    const STATUS_PENDING = 0;
    const STATUS_CONFIRM = 1;
    const STATUS_CONFIRMED = 2;

    const chatsRef = useRef(state);

    useEffect(
        () => { chatsRef.current = state },
        [state]
    )

    const loadCallStatus = (chatIds) => {
        axios.get(WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash).then(result => {
            dispatch({
                type: 'update',
                value: {
                    "call" : result.data
                }
            });
        });
    }

    useEffect(() => {

        loadCallStatus();

        // Cleanup
        return function cleanup() {
        };

    },[]);

    const { t, i18n } = useTranslation('voice_call');

    const cancelJoin = () => {
        axios.get(WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash + '/(action)/cancel').then(result => {
            dispatch({
                type: 'update',
                value: {
                    "call" : result.data
                }
            });
        });
    }

    useInterval(
        () => {
            axios.get(WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash).then(result => {
                dispatch({
                    type: 'update',
                    value: {
                        "call" : result.data
                    }
                });
            });
        },
        state.call.vi_status == STATUS_VI_REQUESTED ? 2000 : null
    );

    const requestJoin = () => {
        axios.post(WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash + '/(action)/request',{
            'type' : (document.getElementById('inlineFormCheck1').checked ? 'audiovideo' : 'audio')
        }).then(result => {
            dispatch({
                type: 'update',
                value: {
                    "call" : result.data
                }
            });
        });
    }

    return (
        <React.Fragment>

            {state.call.vi_status == STATUS_VI_JOINED && <div className="mx-auto pt-4">

                <div className="row">
                    <div className="col-4">Visitor visitor</div>
                </div>

                <div className="mx-auto">
                    <button className="btn btn-primary w-100" onClick={() => cancelJoin()} >{t('voice_call.leave_room')}</button>
                </div>

            </div>}

            {state.call.vi_status == STATUS_VI_REQUESTED && <div className="mx-auto pt-4">
                <p>Please wait untill operator let's you join a room</p>
                <div className="mx-auto">
                    <button className="btn btn-primary w-100" onClick={() => cancelJoin()} >{t('voice_call.cancel_join')}</button>
                </div>
            </div>}

            {state.call.vi_status == STATUS_VI_PENDING && <div className="mx-auto pt-4">
                        <div className="form-group">
                            <h5>Choose call type</h5>
                            <input type="radio" className="form-check-input" defaultChecked={true} value="audio" name="callType" id="inlineFormCheck1" />
                            <label className="form-check-label" htmlFor="inlineFormCheck1">Audio & Video call</label>
                            <br/>
                            <input type="radio" className="form-check-input" value="audio" name="callType" id="inlineFormCheck2" />
                            <label className="form-check-label" htmlFor="inlineFormCheck2">Only Audio call</label>
                        </div>
                        <div className="mx-auto">
                            <button onClick={() => requestJoin()} className="btn btn-primary w-100">{t('voice_call.join_call')}</button>
                        </div>
                    </div>}
        </React.Fragment>
    );
}

export default VoiceCall