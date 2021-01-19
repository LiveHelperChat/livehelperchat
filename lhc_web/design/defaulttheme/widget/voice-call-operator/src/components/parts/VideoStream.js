import React, { useEffect, useState } from "react";

const VideoStream = props => {

    const [isFullScreen, setFullScreen] = useState(false);
    
    useEffect(() => {
        props.user.videoTrack.play('player-'+props.user.uid);
    },[]);
    
    return <div className={(isFullScreen ? "col-12" : "col")+" bg-light m-0 align-middle text-center d-flex p-0"} id={"player-"+props.user.uid} title={"UID "+props.user.uid}>
        <div className="full-screen-icon" onClick={(e) => setFullScreen(!isFullScreen)}>
            <span className="material-icons">{isFullScreen ? 'fullscreen_exit' : 'fullscreen'}</span>
        </div>
    </div>
}

export default VideoStream;