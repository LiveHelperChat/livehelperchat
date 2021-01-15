import React, { useEffect } from "react";

const MediaStream = ({user, media}) => {

    useEffect(() => {

        if (typeof user !== 'undefined' && typeof user.videoTrack !== 'undefined' ) {
            user.videoTrack.play('player-'+user.uid);
        } else {
            setTimeout(function(){
                user.videoTrack.play('player-'+user.uid);
            },1000);
        }

        // Cleanup
        return function cleanup() {
        };
        
    },[]);

    return <div className="col bg-light mx-1 align-middle text-center d-flex pl-0 pr-0" id={"player-"+user.uid} title={"UID "+user.uid}></div>
}

export default MediaStream;