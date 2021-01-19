import React, { useEffect } from "react";
import VideoStream from "./VideoStream";

import {useTranslation} from 'react-i18next';

const MediaStream = props => {

    const { t, i18n } = useTranslation('voice_call');

    if (props.video == true) {
        return <VideoStream user={props.user} key={"video-stream-"+props.user.uid} />
    } else {
        return (<div className="col bg-light m-0 align-middle text-center d-flex p-0" id={"player-"+props.user.uid} title={"UID "+props.user.uid}>
                    <div className="align-self-center mx-auto text-muted font-weight-bold" title={"UID "+props.user.uid}><span className="material-icons">graphic_eq</span>{t('voice_call.audio_call')}</div>
               </div>)

    }
}

export default MediaStream;