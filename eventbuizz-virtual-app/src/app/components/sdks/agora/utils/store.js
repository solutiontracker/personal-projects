const defaultState = {
  // loading effect
  loading: false,
  // media devices
  streams: [],
  shareStream: null,
  liveStream: null,
  shareStreamId: null,
  localStream: null,
  currentStream: null,
  otherStreams: [],
  devicesList: [],
  // web sdk params
  config: {
    uid: 0,
    host: true,
    channelName: '',
    token: null,
    microphoneId: '',
    cameraId: '',
    resolution: '480p'
  },
  agoraClient: null,
  mode: 'live',
  codec: 'h264',
  muteVideo: true,
  muteAudio: true,
  enableShareStream: false,
  enableVideo: true,
  enableAudio: true,
  screen: false,
  profile: true
}

const reducer = (state, action) => {
  switch (action.type) {
    case 'shareStream': {
      return { ...state, shareStream: action.payload }
    }
    case 'shareStreamId': {
      return { ...state, shareStreamId: action.payload }
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
    case 'liveStream': {
      return { ...state, liveStream: action.payload }
    }
    case 'config': {
      const { localStream } = state
      //switch camera, microphone
      if (action.params.cameraId !== undefined && action.params.cameraId) {
        if (localStream) {
          localStream.switchDevice('video', action.params.cameraId, function () {
            console.log('successfully switched to new device with id: ' + action.params.cameraId);
          }, function () {
            console.log('failed to switch to new device with id: ' + action.params.cameraId);
          });
        }
      } else if (action.params.microphoneId !== undefined && action.params.microphoneId) {
        if (localStream) {
          localStream.switchDevice('audio', action.params.microphoneId, function () {
            console.log('successfully switched to new device with id: ' + action.params.microphoneId);
          }, function () {
            console.log('failed to switch to new device with id: ' + action.params.microphoneId);
          });
        }
      }
      return { ...state, config: action.payload }
    }
    case 'client': {
      return { ...state, client: action.payload }
    }
    case 'loading': {
      return { ...state, loading: action.payload }
    }
    case 'codec': {
      return { ...state, codec: action.payload }
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
    case 'devicesList': {
      return { ...state, devicesList: action.payload }
    }
    case 'localStream': {
      return { ...state, localStream: action.payload }
    }
    case 'profile': {
      return { ...state, profile: action.payload }
    }
    case 'currentStream': {
      const { streams } = state
      const newCurrentStream = action.payload
      const otherStreams = streams.filter(it => it.getId() !== newCurrentStream.getId())
      return { ...state, currentStream: newCurrentStream, otherStreams }
    }
    case 'currentStreamById': {
      const { streams } = state
      const newCurrentStreamId = action.payload;
      let newCurrentStream = streams.filter(it => Number(it.getId()) === Number(newCurrentStreamId));
      if (newCurrentStream.length === 0) {
        newCurrentStream = null
      } else {
        newCurrentStream = newCurrentStream[0]
      }

      const otherStreams = newCurrentStream ? streams.filter(it => it.getId() !== newCurrentStream.getId()) : []

      return { ...state, currentStream: newCurrentStream, otherStreams }
    }
    case 'addStream': {
      const { streams, currentStream } = state
      const newStream = action.payload
      let newCurrentStream = currentStream
      if (!newCurrentStream) {
        newCurrentStream = newStream
      }
      if (streams.length === 17) return { ...state }
      const newStreams = [...streams, newStream]
      const otherStreams = newStreams.filter(it => it.getId() !== newCurrentStream.getId())
      window.streams = newStreams
      return { ...state, streams: newStreams, currentStream: newCurrentStream, otherStreams }
    }
    case 'removeStream': {
      const { streams, currentStream } = state
      const { stream, uid } = action
      const targetUid = stream ? stream.getId() : uid
      let newCurrentStream = currentStream
      const newStreams = streams
        .filter((stream) => (stream.getId() !== targetUid))
      if (targetUid === currentStream.getId()) {
        if (newStreams.length === 0) {
          newCurrentStream = null
        } else {
          newCurrentStream = newStreams[0]
        }
      }
      const otherStreams = newCurrentStream ? newStreams.filter(it => it.getId() !== newCurrentStream.getId()) : []
      return { ...state, streams: newStreams, currentStream: newCurrentStream, otherStreams }
    }
    case 'clearAllStream': {
      const { streams, localStream, currentStream } = state
      streams.forEach((stream) => {
        if (stream.isPlaying()) {
          stream.stop()
        }
        stream.close()
      })

      if (localStream) {
        localStream.isPlaying() &&
          localStream.stop()
        localStream.close()
      }
      if (currentStream) {
        currentStream.isPlaying() &&
          currentStream.stop()
        currentStream.close()
      }
      return { ...state, currentStream: null, localStream: null, streams: [] }
    }
    default:
      throw new Error('mutation type not defined')
  }
}

export {
  reducer,
  defaultState
}
