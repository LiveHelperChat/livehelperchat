import React, { useEffect, useState, useReducer, useRef, useBoolean } from "react";
import axios from "axios";
import {useTranslation} from 'react-i18next';
import useInterval from "../lib/useInterval";
import AgoraRTC from "agora-rtc-sdk-ng"
import MediaStream from "./parts/MediaStream";

const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

function reducer(state, action) {
    switch (action.type) {

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

        case 'user_update': {
            if (typeof state.remoteUsers[action.user.uid] != 'undefined') {
                if (action.media == 'audio') {
                    state.remoteUsers[action.user.uid].audio = false;
                } else if (action.media == 'video') {
                    state.remoteUsers[action.user.uid].video = false;
                }
                state.remoteUsers[action.user.uid];
            }
            return { ...state}
        }

        case 'user_unpublished': {
            delete state.remoteUsers[action.user.uid];
            return { ...state}
        }

        default:
            throw new Error('Unknown action!');
    }
}

const VoiceCall = props => {

    const [state, dispatch] = useReducer(reducer, {
        call : {},
        localTracks : {
            videoTrack : null,
            audioTrack: null
        },
        hasAudio: false,
        hasVideo: false,
        screenShare: false,
        remoteUsers : {},
        uid : null,
        inCall: false,
        pendingJoin: false,
        type: "",
        isMuted : false
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

    useEffect(() => {

        updateUI();

        AgoraRTC.getDevices()
            .then(devices => {

                const audioDevices = devices.filter(function(device){
                    return device.kind === "audioinput";
                });

                const videoDevices = devices.filter(function(device){
                    return device.kind === "videoinput";
                });

                dispatch({
                    type: 'update',
                    value: {
                        "hasAudio" : (audioDevices.length > 0),
                        "hasVideo" : (videoDevices.length > 0)
                    }
                });
            });
    },[]);

    const { t, i18n } = useTranslation('voice_call');

    const requestJoin = (type) => {

        var url = null;

        if (props.isVisitor === true) {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash + '/(action)/request';
        } else {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/joinop/" + props.initParams.id + '/(action)/join';
        }

        axios.post(url, {
            "type" : type
        }).then(result => {
            dispatch({
                type: 'update',
                value: {
                    "call" : result.data,
                    "type" : type,
                    "pendingJoin" : true
                }
            });
        });
    }

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
                    "inCall": false,
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

    const muteMicrophone = () => {
        if (state.localTracks.audioTrack !== null) {
            state.localTracks.audioTrack.setEnabled(state.isMuted);
            dispatch({
                type: 'update',
                value: {
                    "isMuted" : !state.isMuted
                }
            });
        }
    }

    const updateUI = () => {
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
    }

    useInterval(
        () => {
            updateUI()
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
    }

    const handleUserPublished = (user, mediaType) =>  {
        subscribe(user, mediaType);
    }

    const handleUserLeft = (user) => {
        dispatch({
            type: 'user_unpublished',
            user: user
        });
        updateUI();
    }

    const handleUserUnpublished = (user, mediaType) => {
        dispatch({
            type: 'user_update',
            media: mediaType,
            user: user
        });
    }

    // Auto join leave for an operator
    useEffect(() => {
        if (props.isVisitor === true && state.pendingJoin == true) {
            if (state.call.vi_status === STATUS_VI_JOINED) {
                join(state.call);
            } else if (state.inCall === true && state.call.vi_status !== STATUS_VI_JOINED) {
                cancelJoin('cancel');
            }
        }
    },[state.call.vi_status, state.pendingJoin]);

    // Auto join for the operator
    useEffect(() => {
        if (state.call.op_status === STATUS_OP_JOINED && props.isVisitor === false && state.inCall === false && state.pendingJoin == true) {
            join(state.call);
        }
    },[state.call.op_status, state.pendingJoin]);

    const screenShare = async () => {

        if (state.screenShare == true) {

            let localTracks = state.localTracks;

            if (localTracks.videoTrack) {
                await client.unpublish(localTracks.videoTrack);
                localTracks.videoTrack.stop();
                localTracks.videoTrack.close();
            }

            localTracks.videoTrack = null;

            dispatch({
                type: 'update',
                value: {
                    "inCall": true,
                    "screenShare": false,
                    "localTracks" : localTracks
                }
            });

            // Add camera back if it was required
            if (state.type == "audiovideo") {
                addCamera();
            }

            return ;
        }

        try {
            const screenTrack = await AgoraRTC.createScreenVideoTrack(  );

            let localTracks = state.localTracks;

            if (state.localTracks.videoTrack) {
                await client.unpublish(state.localTracks.videoTrack);
                state.localTracks.videoTrack.stop();
                state.localTracks.videoTrack.close();
            }

            localTracks.videoTrack = screenTrack;
            localTracks.videoTrack.play("local-player");

            dispatch({
                type: 'update',
                value: {
                    "inCall": true,
                    "screenShare": true,
                    "localTracks" : localTracks,
                }
            });

            await client.publish(screenTrack);

        } catch (e) {
            alert('Screen could not be shared!');
        }
    }

    const addCamera = async () => {

        if (state.localTracks.videoTrack && state.localTracks.videoTrack !== null) {

            let localTracks = state.localTracks;

            if (localTracks.videoTrack) {
                await client.unpublish(localTracks.videoTrack);
                localTracks.videoTrack.stop();
                localTracks.videoTrack.close();
            }

            localTracks.videoTrack = null;

            dispatch({
                type: 'update',
                value: {
                    "inCall": true,
                    "type": "audio",
                    "localTracks" : localTracks
                }
            });

            return ;

        } else {

            const videoTrack = await AgoraRTC.createCameraVideoTrack();

            let localTracks = state.localTracks;

            localTracks.videoTrack = videoTrack;
            localTracks.videoTrack.play("local-player");

            dispatch({
                type: 'update',
                value: {
                    "inCall": true,
                    "type": "audiovideo",
                    "localTracks" : localTracks,
                }
            });

            await client.publish(videoTrack);
        }

    }

    const tokenWillExpire = () => {

        var url = null;

        if (props.isVisitor === true) {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/join/" + props.initParams.id + '/' + props.initParams.hash + '/(action)/token';
        } else {
            url = WWW_DIR_JAVASCRIPT  + "voicevideo/joinop/" + props.initParams.id + '/' + '/(action)/token';
        }

        axios.get(url).then( result => {
            dispatch({
                type: 'update',
                value: {
                    "call" : result.data
                }
            });

            client.renewToken(result.data.token);
        });

    }

    const join = async (data) => {

        if (state.inCall === true || (props.isVisitor === true && data.vi_status != STATUS_VI_JOINED) ) {
            return;
        }

        // add event listener to play remote tracks when remote user publishs.
        client.on("user-published", handleUserPublished);
        client.on("user-unpublished", handleUserUnpublished);
        client.on("user-left", handleUserLeft);
        client.on("token-privilege-will-expire", tokenWillExpire);

        var uui = await client.join(props.initParams.appid, props.initParams.id + '_' + props.initParams.hash, data.token || null);
        var localTracks = {
            audioTrack : await AgoraRTC.createMicrophoneAudioTrack()
        };

        if (state.type == "audiovideo" || (props.isVisitor == true && state.call.video == 1)) {
            localTracks.videoTrack = await AgoraRTC.createCameraVideoTrack();
            localTracks.videoTrack.play("local-player");
        }

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

    return (
        <React.Fragment>
            <div className="d-flex flex-md-row flex-column flex-grow-1 pt-0">
                <div className="col bg-light m-0 align-middle text-center d-flex p-0" title={"UID "+state.uid} id="local-player">
                    {props.isVisitor == true && state.call.vi_status == STATUS_VI_REQUESTED && <div className="align-self-center mx-auto text-muted font-weight-bold">{t('voice_call.wait_join_long')}</div>}
                    {state.localTracks.videoTrack == null && state.inCall == true && <div className="align-self-center mx-auto text-muted font-weight-bold"><span className="material-icons">graphic_eq</span>{t('voice_call.me_audio')}</div>}
                </div>
                {state.inCall == true && Object.keys(state.remoteUsers).map((val, k) => {
                    return (<MediaStream user={state.remoteUsers[val].user} key={"media_" + (state.remoteUsers[val].user.uid) + '_' + state.remoteUsers[val].media.join('_')} audio={state.remoteUsers[val].audio} video={state.remoteUsers[val].video} media={state.remoteUsers[val].media} />)
                })}
            </div>
            <div className="row border-top">

                <div className="btn-toolbar p-2 text-center mx-auto btn-toolbar" role="toolbar" >

                    <div className="p-2 text-center mx-auto btn-group" role="group">
                        {props.isVisitor == true && state.call.vi_status == STATUS_VI_REQUESTED && <span className="text-muted py-2">{t('voice_call.wait_let_in')} </span>}
                        {props.isVisitor == true && (state.call.vi_status == STATUS_VI_PENDING || state.pendingJoin === false) && <span className="text-muted py-2">{t('voice_call.join_to_start')} </span>}

                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_JOINED && <span className="text-muted py-2">{t('voice_call.visitor_joined')}</span>}
                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_PENDING && <span className="text-muted py-2">{t('voice_call.pending_visitor_join')}</span>}
                        {props.isVisitor == false && state.call.vi_status == STATUS_VI_REQUESTED && <span className="text-muted py-2">{t('voice_call.visitor_waiting_in')}</span>}
                    </div>

                    <div className="p-2 text-center mx-auto btn-group" role="group">
                        {props.isVisitor == false && state.call.token != '' && state.call.vi_status == STATUS_VI_REQUESTED && <button className="btn btn-sm btn-outline-primary" onClick={() => cancelJoin('letvisitorin')} ><span className="material-icons">face</span>{t('voice_call.let_visitor_in')}</button>}

                        {props.isVisitor == false && state.inCall == true && <React.Fragment>
                            <button title={t('voice_call.leave_a_call')} className="btn btn-sm btn-outline-secondary" onClick={() => cancelJoin('leave')}><span className="material-icons">exit_to_app</span>{t('voice_call.leave_call_op')}</button>
                            <button title={t('voice_call.end_call_op')} className="btn btn-sm btn-outline-secondary" onClick={() => cancelJoin('end')}><span className="material-icons">call_end</span>{t('voice_call.end_call_button')}</button>
                            <button title={state.isMuted == true ? t('voice_call.unmute_mic') : t('voice_call.mute_mic')} className="btn btn-sm btn-outline-secondary" onClick={() => muteMicrophone()} ><span className="material-icons mr-0">{state.isMuted == true ? 'mic_off' : 'mic'}</span></button>
                            {props.initParams.options.video == true && state.hasVideo === true && <button className="btn btn-sm btn-outline-secondary" disabled={state.screenShare} onClick={() => addCamera()} title={state.type == "audio" ? t('voice_call.share_video') : t('voice_call.stop_sharing_video') }><span className="material-icons mr-0">{(state.type == "audio" || state.screenShare == true) ? 'videocam_off' : 'videocam'}</span></button>}
                            {props.initParams.options.screenshare == true && <button className="btn btn-sm btn-outline-secondary" onClick={() => screenShare()} title={state.screenShare == true ? t('voice_call.stop_share_screen') : t('voice_call.share_your_screen')}><span className="material-icons mr-0">{state.screenShare == true ? 'stop_screen_share' : 'screen_share'}</span></button>}
                        </React.Fragment>}

                        {((props.isVisitor == false && state.call.op_status == STATUS_OP_PENDING) || (props.isVisitor == true && state.call.vi_status == STATUS_VI_PENDING) || state.pendingJoin == false) && <React.Fragment>
                            {state.hasAudio === true && <button className="btn btn-sm btn-outline-secondary" onClick={() => requestJoin('audio')}><span className="material-icons">call</span>{t('voice_call.join_with_audio')}</button>}
                            {props.initParams.options.video == true && state.hasVideo === true && <button className="btn btn-sm btn-outline-secondary" onClick={() => requestJoin('audiovideo')}><span className="material-icons">video_call</span>{t('voice_call.join_with_audio_video')}</button>}
                        </React.Fragment>}

                        {props.isVisitor == true && state.inCall == true && <React.Fragment>
                            <button className="btn btn-outline-primary btn-sm" onClick={() => cancelJoin('cancel')} ><span className="material-icons">call_end</span>{t('voice_call.leave_room')}</button>
                            <button title={state.isMuted == true ? t('voice_call.unmute_mic') : t('voice_call.mute_mic')} className="btn btn-outline-secondary btn-sm" onClick={() => muteMicrophone()} ><span className="material-icons mr-0">{state.isMuted == true ? 'mic_off' : 'mic'}</span></button>
                            {props.initParams.options.video == true && state.hasVideo === true && <button disabled={state.screenShare} className="btn btn-outline-secondary btn-sm" onClick={() => addCamera()} title={state.type == "audio" ? t('voice_call.share_video') : t('voice_call.stop_sharing_video')} ><span className="material-icons mr-0">{(state.type == "audio" || state.screenShare == true) ? 'videocam_off' : 'videocam'}</span></button>}
                            {props.initParams.options.screenshare == true && <button className="btn btn-outline-secondary btn-sm" onClick={() => screenShare()} title={state.screenShare == true ? t('voice_call.stop_share_screen') : t('voice_call.share_your_screen')}><span className="material-icons mr-0">{state.screenShare == true ? 'stop_screen_share' : 'screen_share'}</span></button>}
                        </React.Fragment>}

                        {props.isVisitor == true && state.pendingJoin === true && state.call.vi_status == STATUS_VI_REQUESTED && <button className="btn btn-outline-primary btn-sm" onClick={() => cancelJoin('cancel')} >{t('voice_call.cancel_join')}</button>}

                    </div>

                </div>

            </div>
        </React.Fragment>
    );
}

export default VoiceCall