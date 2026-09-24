import AgoraRTC from "agora-rtc-sdk-ng"

/**
 * Agora.io adapter. Exposes the same API as the LiveKit adapter.
 */

const wrapLocalTrack = (track) => {
    return {
        native: track,
        play: (elementId) => track.play(elementId),
        stop: () => track.stop(),
        close: () => track.close(),
        setEnabled: (enabled) => track.setEnabled(enabled),
        setDevice: (deviceId) => track.setDevice(deviceId)
    }
}

const createProvider = () => {

    const client = AgoraRTC.createClient({ mode: "rtc", codec: "vp8" });

    const rtc = {
        getDevices: () => AgoraRTC.getDevices(),
        createMicrophoneAudioTrack: async (deviceId) => wrapLocalTrack(await AgoraRTC.createMicrophoneAudioTrack(deviceId ? {microphoneId: deviceId} : {})),
        createCameraVideoTrack: async (deviceId) => wrapLocalTrack(await AgoraRTC.createCameraVideoTrack(deviceId ? {cameraId: deviceId} : {})),
        createScreenVideoTrack: async () => {
            const track = await AgoraRTC.createScreenVideoTrack({}, "disable");
            return wrapLocalTrack(Array.isArray(track) ? track[0] : track);
        }
    };

    const toNative = (tracks) => Array.isArray(tracks) ? tracks.map(track => track.native) : tracks.native;

    const wrapper = {
        join: (initParams, token) => client.join(initParams.appid, initParams.room, token || null),
        leave: () => client.leave(),
        publish: (tracks) => client.publish(toNative(tracks)),
        unpublish: (tracks) => client.unpublish(toNative(tracks)),
        subscribe: (user, mediaType) => client.subscribe(user, mediaType),
        renewToken: (token) => client.renewToken(token),
        on: (event, callback) => client.on(event, callback),
        off: (event, callback) => client.off(event, callback)
    };

    return {rtc: rtc, client: wrapper};
}

export default createProvider;
