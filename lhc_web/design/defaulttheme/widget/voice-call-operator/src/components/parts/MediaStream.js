import React, { useEffect } from "react";
import VideoStream from "./VideoStream";

const MediaStream = props => {

    useEffect(() => {
        // Cleanup
        return function cleanup() {
        };
        
    },[]);

    if (props.video == true) {
        return <VideoStream user={props.user} key={"video-stream-"+props.user.uid} />
    } else {
        return <div className="col bg-light mx-1 align-middle text-center d-flex pl-0 pr-0" id={"player-"+props.user.uid} title={"UID "+props.user.uid}>Audio Call Only</div>
    }

}

export default MediaStream;