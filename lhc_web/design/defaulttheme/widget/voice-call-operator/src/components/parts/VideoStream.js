import React, { useEffect } from "react";

const VideoStream = props => {
    useEffect(() => {
        props.user.videoTrack.play('player-'+props.user.uid);
    },[]);
    return <div className="col bg-light mx-1 align-middle text-center d-flex pl-0 pr-0" id={"player-"+props.user.uid} title={"UID "+props.user.uid}></div>
}

export default VideoStream;