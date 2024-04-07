const defaultState = {
    loading: false,
    // media devices
    currentStream: null,
    localStream: null,
    otherStreams: [],
    shareStream: null,
    publisherShareStream: null,
    // web sdk params
    config: {
        uid: 0,
        host: true,
        sessionId: '',
        channelName: '',
        token: null,
        microphoneId: '',
        cameraId: '',
        resolution: '480p'
    },
    muteVideo: true,
    muteAudio: true,
    enableShareStream: false,
    enableVideo: true,
    enableAudio: true,
    screen: false,
    profile: true
}

export function vonage(state = defaultState, action) {
    switch (action.type) {
        case 'shareStream': {
            return { ...state, shareStream: action.payload }
        }
        case 'publisherShareStream': {
            return { ...state, publisherShareStream: action.payload }
        }
        case 'enableVideo': {
            return { ...state, enableVideo: action.payload }
        }
        case 'enableAudio': {
            return { ...state, enableAudio: action.payload }
        }
        case 'share': {
            return { ...state, enableShareStream: action.payload }
        }
        case 'loading': {
            return { ...state, loading: action.payload }
        }
        case 'video': {
            return { ...state, muteVideo: action.payload }
        }
        case 'audio': {
            return { ...state, muteAudio: action.payload }
        }
        case 'screen': {
            return { ...state, screen: action.payload }
        }
        case 'profile': {
            return { ...state, profile: action.payload }
        }
        case 'addStream': {
            const { currentStream } = state
            const newStream = action.payload
            let newCurrentStream = currentStream
            if (!newCurrentStream) {
                newCurrentStream = newStream
            }
            return { ...state, currentStream: newCurrentStream, localStream: newCurrentStream }
        }
        case 'currentStream': {
            const newCurrentStream = action.payload
            return { ...state, currentStream: newCurrentStream }
        }
        case 'currentStreamById': {
            const { otherStreams, localStream, currentStream } = state
            const newCurrentStreamId = action.payload;
            let newCurrentStream = otherStreams.filter(stream => Number(stream.name.split('|').shift()) === Number(newCurrentStreamId));
            if (newCurrentStream.length === 0) {
                if (Number(localStream.name.split('|').shift()) === Number(newCurrentStreamId)) {
                    newCurrentStream = localStream;
                } else {
                    newCurrentStream = currentStream;
                }
            } else {
                newCurrentStream = newCurrentStream[0]
            }
            
            return { ...state, currentStream: newCurrentStream, otherStreams }
        }
        case 'otherStreams': {
            const { currentStream } = state
            const otherStreams = action.payload
            const userRole = action.userRole
            return { ...state, otherStreams, currentStream: (!currentStream && otherStreams.length > 0 && userRole === 'audience' ? otherStreams[0] : currentStream) }
        }
        case 'reset': {
            return defaultState;
        }
        default:
            return state;
    }
}