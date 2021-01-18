import React, { useEffect, useState, useReducer, useRef, useBoolean } from "react";
import axios from "axios";
import {useTranslation} from 'react-i18next';
import useInterval from "../lib/useInterval";
import AgoraRTC from "agora-rtc-sdk-ng"
import MediaStream from "./parts/MediaStream";

const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

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

        case 'user_published': {

            if (typeof state.remoteUsers[action.user.uid] == 'undefined') {
                var obj =  {'user' : action.user, video: (action.media == 'video'), audio: (action.media == 'audio'), media : [action.media]};
                state.remoteUsers[action.user.uid] = obj;
            } else {
                if (action.media == 'audio') {
                    state.remoteUsers[action.user.uid].audio = true;
                } else if (action.media == 'video') {
                    state.remoteUsers[action.user.uid].video = true;
                }

                state.remoteUsers[action.user.uid].media.push(action.media);
            }

            return { ...state}
        }

        case 'user_unpublished': {
            delete state.remoteUsers[action.user.uid];
            return { ...state}
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
        localTracks : {
            videoTrack : null,
            audioTrack: null
        },
        remoteUsers : {},
        uid : null,
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
        axios.get(WWW_DIR_JAVASCRIPT  + "voicevideo/joinop/" + props.initParams.id).then(result => {
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

    const cancelJoin = async (type) => {

        var url = null;

        if (props.isVisitor === true) {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash + '/(action)/' + type;
        } else {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/joinop/" + props.initParams.id + '/' + '/(action)/' + type;
        }

        axios.get(url).then(result => {
            dispatch({
                type: 'update',
                value: {
                    "call" : result.data
                }
            });
        });

        if (type == 'leave' || type == 'end' || type == 'cancel')
        {

            Object.keys(state.localTracks).forEach(trackName => {
                var track = state.localTracks[trackName];
                if (track) {
                    track.stop();
                    track.close();
                    state.localTracks[trackName] = undefined;
                }
            })

            dispatch({
                type: 'update',
                value: {
                    "remoteUsers" : {},
                    "uid": null,
                    "localTracks" : {
                        videoTrack : null,
                        audioTrack: null
                    }
                }
            });

            // leave the channel
           await client.leave();
        }

    }

    useInterval(
        () => {

            var url = null;

            if (props.isVisitor === true) {
                url = WWW_DIR_JAVASCRIPT + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash;
            } else {
                url = WWW_DIR_JAVASCRIPT  + "voicevideo/joinop/" + props.initParams.id;
            }

            axios.get(url).then(result => {
                dispatch({
                    type: 'update',
                    value: {
                        "call" : result.data
                    }
                });
            });

        },
        (state.call.status != STATUS_CONFIRMED || state.call.vi_status != STATUS_VI_JOINED || state.call.op_status != STATUS_OP_JOINED) ? 2000 : null
    );

    const subscribe = async (user, mediaType) => {
        
        const uid = user.uid;
        
        // subscribe to a remote user
        await client.subscribe(user, mediaType);

        dispatch({
            type: 'user_published',
            media: mediaType,
            user: user
        });

        if (mediaType === 'audio') {
            user.audioTrack.play();
        }

        /*if (mediaType === 'audio') {
            user.audioTrack.play();
        }*/

        /*if (mediaType === 'video') {
            const player = $(`
              <div id="player-wrapper-${uid}">
                <p class="player-name">remoteUser(${uid})</p>
                <div id="player-${uid}" class="player"></div>
              </div>
            `);
            $("#remote-playerlist").append(player);
            user.videoTrack.play(`player-${uid}`);
        }
        if (mediaType === 'audio') {
            user.audioTrack.play();
        }*/
    }
    
    const handleUserPublished = (user, mediaType) =>  {

        subscribe(user, mediaType);
    }

    const handleUserUnpublished = (user) => {
        dispatch({
            type: 'user_unpublished',
            user: user
        });
    }

    useEffect(() => {
        if (state.call.vi_status === STATUS_VI_JOINED && props.isVisitor == true) {
            join(state.call);
        }
    },[state.call.vi_status]);


    const join = async (data) => {
        // add event listener to play remote tracks when remote user publishs.
        client.on("user-published", handleUserPublished);
        client.on("user-unpublished", handleUserUnpublished);

        var uui = null;
        var localTracks = {
            audioTrack : null,
            videoTrack : null
        };

        // join a channel and create local tracks, we can use Promise.all to run them concurrently
        [ uui, localTracks.audioTrack, localTracks.videoTrack] = await Promise.all([
            // join the channel
            client.join(props.initParams.appid, props.initParams.id + '_' + props.initParams.hash, data.token || null),
            // create local tracks, using microphone and camera
            AgoraRTC.createMicrophoneAudioTrack(),
            AgoraRTC.createCameraVideoTrack()
        ]);

        // play local video track
        localTracks.videoTrack.play("local-player");

        dispatch({
            type: 'update',
            value: {
                "uid" : uui,
                "inCall": true,
                "localTracks" : localTracks,
            }
        });

        await client.publish(Object.values(localTracks));
    }

    const requestJoin = (type) => {

        var url = null;

        if (props.isVisitor === true) {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash + '/(action)/request';
        } else {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/joinop/" + props.initParams.id + '/(action)/join';
        }

        axios.post(url).then(result => {
            if (type == 'audiovideo') {
                join(result.data);
            }
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
            <div className="d-flex flex-row flex-grow-1 pt-2">
                <div className="col bg-light mx-1 align-middle text-center d-flex pl-0 pr-0" title={"UID "+state.uid} id="local-player">
                    {props.isVisitor == true && state.call.vi_status == STATUS_VI_REQUESTED && <div className="align-self-center mx-auto text-muted font-weight-bold">Please wait untill operator let's you join a room</div>}
                </div>
                {state.inCall == true && Object.keys(state.remoteUsers).map((val, k) => {
                    return (<MediaStream user={state.remoteUsers[val].user} key={"media_" + (state.remoteUsers[val].user.uid) + '_' + state.remoteUsers[val].media.join('_')} audio={state.remoteUsers[val].audio} video={state.remoteUsers[val].video} media={state.remoteUsers[val].media} />)
                })}
            </div>
            <div className="row header-chat desktop-header">

                <div className="btn-toolbar p-2 text-center mx-auto btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">

                    <div className="p-2 text-center mx-auto btn-group" role="group">
                        {props.isVisitor == true && state.call.vi_status == STATUS_VI_PENDING && <span className="text-muted py-2">Please wait untill operator let's you in</span>}
                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_JOINED && <span className="text-muted py-2">Visitor has joined a call!</span>}
                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_PENDING && <span className="text-muted py-2">Pending visitor to join a call!</span>}
                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_REQUESTED && <span className="text-muted py-2">Visitor is waiting for someone to let him in!</span>}
                    </div>

                    <div className="p-2 text-center mx-auto btn-group" role="group">
                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_REQUESTED && <button className="btn btn-sm btn-outline-secondary" onClick={() => cancelJoin('letvisitorin')} ><span className="material-icons">face</span>Let visitor in</button>}
                        {props.isVisitor == false && state.call.op_status == STATUS_OP_JOINED && <button title="Leave a call. Visitor still remain on the call" className="btn btn-sm btn-outline-secondary" onClick={() => cancelJoin('leave')}><span className="material-icons">call_end</span>Leave a call</button>}
                        {props.isVisitor == false && state.call.op_status == STATUS_OP_JOINED && <button title="Call for the visitor also will end." className="btn btn-sm btn-outline-secondary" onClick={() => cancelJoin('end')}><span className="material-icons">call_end</span>End a call</button>}

                        {( (props.isVisitor == false && state.call.op_status == STATUS_OP_PENDING) || (props.isVisitor == true && state.call.vi_status == STATUS_VI_PENDING) )&& <React.Fragment>
                            <button className="btn btn-sm btn-outline-secondary" onClick={() => requestJoin('audio')}><span className="material-icons">call</span>Join with audio</button>
                            <button className="btn btn-sm btn-outline-secondary" onClick={() => requestJoin('audiovideo')}><span className="material-icons">video_call</span>Join with audio & video</button>
                        </React.Fragment>}

                        {props.isVisitor == true && state.call.vi_status == STATUS_VI_JOINED && <button className="btn btn-primary w-100" onClick={() => cancelJoin('cancel')} >{t('voice_call.leave_room')}</button>}
                        {props.isVisitor == true && state.call.vi_status == STATUS_VI_REQUESTED && <button className="btn btn-primary w-100" onClick={() => cancelJoin('cancel')} >{t('voice_call.cancel_join')}</button>}

                    </div>

                </div>

            </div>
        </React.Fragment>
    );
}

export default VoiceCall