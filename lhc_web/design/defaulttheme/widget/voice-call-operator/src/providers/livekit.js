import {
    Room,
    RoomEvent,
    ConnectionQuality,
    createLocalAudioTrack,
    createLocalVideoTrack,
    createLocalScreenTracks,
    VideoPresets
} from "livekit-client";

/**
 * LiveKit (open source, self hosted) media adapter used by the call application.
 */

const attachTo = (track, elementId, isLocal) => {
    let container = elementId ? document.getElementById(elementId) : null;

    if (container === null) {
        // Remote audio tracks are played without visible element
        container = document.getElementById('lhc-lk-audio');
        if (container === null) {
            container = document.createElement('div');
            container.id = 'lhc-lk-audio';
            container.style.display = 'none';
            document.body.appendChild(container);
        }
    }

    const element = track.attach();

    if (track.kind === 'video') {
        element.style.width = '100%';
        element.style.height = '100%';
        element.style.objectFit = 'contain';
        element.setAttribute('playsinline', 'playsinline');
    }

    if (isLocal === true) {
        element.muted = true;
    }

    container.appendChild(element);
}

const wrapLocalTrack = (track) => {
    return {
        native: track,
        play: (elementId) => attachTo(track, elementId, true),
        stop: () => track.detach().forEach(element => element.remove()),
        close: () => track.stop(),
        setEnabled: (enabled) => enabled ? track.unmute() : track.mute(),
        setDevice: (deviceId) => track.setDeviceId(deviceId)
    }
}

const wrapRemoteTrack = (track) => {
    return {
        native: track,
        play: (elementId) => attachTo(track, elementId, false),
        stop: () => track.detach().forEach(element => element.remove())
    }
}

const qualityMap = (quality) => {
    switch (quality) {
        case ConnectionQuality.Excellent: return 1;
        case ConnectionQuality.Good: return 2;
        case ConnectionQuality.Poor: return 4;
        case ConnectionQuality.Lost: return 6;
        default: return 0;
    }
}

const createProvider = () => {

    let room = null;
    let users = {};
    let listeners = {};

    const emit = (event, ...args) => {
        (listeners[event] || []).forEach(callback => callback(...args));
    }

    const getUser = (participant) => {
        if (typeof users[participant.identity] === 'undefined') {
            users[participant.identity] = {
                uid: participant.identity,
                name: participant.name,
                audioTrack: null,
                videoTrack: null
            };
        }
        return users[participant.identity];
    }

    const bindRoom = (room) => {
        room.on(RoomEvent.TrackSubscribed, (track, publication, participant) => {
            const user = getUser(participant);
            if (track.kind === 'audio') {
                user.audioTrack = wrapRemoteTrack(track);
            } else if (track.kind === 'video') {
                user.videoTrack = wrapRemoteTrack(track);
            } else {
                return;
            }
            emit('user-published', user, track.kind);
        });

        room.on(RoomEvent.TrackUnsubscribed, (track, publication, participant) => {
            const user = getUser(participant);
            if (track.kind === 'audio' && user.audioTrack !== null) {
                user.audioTrack.stop();
                user.audioTrack = null;
            } else if (track.kind === 'video' && user.videoTrack !== null) {
                user.videoTrack.stop();
                user.videoTrack = null;
            }
            emit('user-unpublished', user, track.kind);
        });

        room.on(RoomEvent.ParticipantDisconnected, (participant) => {
            const user = getUser(participant);
            delete users[participant.identity];
            emit('user-left', user);
        });

        room.on(RoomEvent.Reconnecting, () => emit('connection-state-change', 'RECONNECTING'));
        room.on(RoomEvent.Reconnected, () => emit('connection-state-change', 'CONNECTED'));
        room.on(RoomEvent.Disconnected, () => emit('connection-state-change', 'DISCONNECTED'));

        room.on(RoomEvent.ConnectionQualityChanged, (quality, participant) => {
            if (participant.isLocal === true) {
                emit('network-quality', {uplinkNetworkQuality: qualityMap(quality), downlinkNetworkQuality: qualityMap(quality)});
            }
        });
    }

    const rtc = {
        getDevices: () => navigator.mediaDevices.enumerateDevices(),
        createMicrophoneAudioTrack: async (deviceId) => wrapLocalTrack(await createLocalAudioTrack({
            deviceId: deviceId || undefined,
            echoCancellation: true,
            noiseSuppression: true,
            autoGainControl: true
        })),
        createCameraVideoTrack: async (deviceId) => wrapLocalTrack(await createLocalVideoTrack({
            deviceId: deviceId || undefined,
            resolution: VideoPresets.h720.resolution
        })),
        createScreenVideoTrack: async () => {
            const tracks = await createLocalScreenTracks({audio: false});
            return wrapLocalTrack(tracks.find(track => track.kind === 'video'));
        }
    };

    const toArray = (tracks) => Array.isArray(tracks) ? tracks : [tracks];

    const client = {
        join: async (initParams, token) => {
            users = {};
            room = new Room({adaptiveStream: true, dynacast: true});
            bindRoom(room);
            await room.connect(initParams.url, token, {autoSubscribe: true});
            return room.localParticipant.identity;
        },
        leave: async () => {
            if (room !== null) {
                const current = room;
                room = null;
                users = {};
                await current.disconnect();
            }
        },
        publish: async (tracks) => {
            for (const track of toArray(tracks)) {
                if (track) {
                    await room.localParticipant.publishTrack(track.native);
                }
            }
        },
        unpublish: async (tracks) => {
            for (const track of toArray(tracks)) {
                if (track && room !== null) {
                    await room.localParticipant.unpublishTrack(track.native, false);
                }
            }
        },
        // Tracks are auto subscribed
        subscribe: () => Promise.resolve(),
        // LiveKit refreshes access token of connected participant automatically
        renewToken: () => Promise.resolve(),
        on: (event, callback) => {
            listeners[event] = listeners[event] || [];
            listeners[event].push(callback);
        },
        off: (event, callback) => {
            listeners[event] = (listeners[event] || []).filter(item => item !== callback);
        }
    };

    return {rtc: rtc, client: client};
}

export default createProvider;
