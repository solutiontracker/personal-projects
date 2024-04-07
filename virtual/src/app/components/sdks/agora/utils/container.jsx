import React, { createContext, useContext, useReducer, useState } from 'react'
import { reducer, defaultState } from './store'
import CustomizedSnackbar from '@app/sdks/agora/utils/snackbar-wrapper'
import Loading from '@app/sdks/agora/utils/loading'

const StateContext = createContext({})
const MutationContext = createContext({})

export const ContainerProvider = ({ children }) => {
  const [state, dispatch] = useReducer(reducer, defaultState)

  const [toasts, updateToasts] = useState([])

  const methods = {
    setShareStream (value) {
      dispatch({ type: 'shareStream', payload: value })
    },
    setShareStreamId (value) {
      dispatch({ type: 'shareStreamId', payload: value })
    },
    setEnableVideo (param) {
      dispatch({ type: 'enableVideo', payload: param })
    },
    setEnableAudio (param) {
      dispatch({ type: 'enableAudio', payload: param })
    },
    setCurrentStreamById (param) {
      dispatch({ type: 'currentStreamById', payload: param })
    },
    setEnableShare (param) {
      dispatch({ type: 'share', payload: param })
    },
    setLiveStream (value) {
      dispatch({ type: 'liveStream', payload: value })
    },
    startLoading () {
      dispatch({ type: 'loading', payload: true })
    },
    stopLoading () {
      dispatch({ type: 'loading', payload: false })
    },
    updateConfig (params) {
      dispatch({ type: 'config', payload: { ...state.config, ...params }, params: params })
    },
    setClient (clientInstance) {
      dispatch({ type: 'client', payload: clientInstance })
    },
    setCodec (param) {
      dispatch({ type: 'codec', payload: param })
    },
    setVideo (param) {
      dispatch({ type: 'video', payload: param })
    },
    setAudio (param) {
      dispatch({ type: 'audio', payload: param })
    },
    setScreen (param) {
      dispatch({ type: 'screen', payload: param })
    },
    setProfile (param) {
      dispatch({ type: 'profile', payload: param })
    },
    toastSuccess (message) {
      updateToasts([
        ...toasts,
        {
          variant: 'success',
          message
        }
      ])
    },
    toastInfo (message) {
      updateToasts([
        ...toasts,
        {
          variant: 'info',
          message
        }
      ])
    },
    toastError (message) {
      updateToasts([
        ...toasts,
        {
          variant: 'error',
          message
        }
      ])
    },
    removeTop () {
      const items = toasts.filter((e, idx) => idx > 0)
      updateToasts([
        ...items
      ])
    },
    setLocalStream (param) {
      dispatch({ type: 'localStream', payload: param })
    },
    setCurrentStream (param) {
      dispatch({ type: 'currentStream', payload: param })
    },
    setDevicesList (param) {
      dispatch({ type: 'devicesList', payload: param })
    },
    clearAllStream () {
      dispatch({ type: 'clearAllStream' })
    },
    addLocal (evt) {
      const { stream } = evt
      methods.setLocalStream(stream)
      methods.setCurrentStream(stream)
    },
    addStream (evt) {
      const { stream } = evt
      dispatch({ type: 'addStream', payload: stream })
    },
    removeStream (evt) {
      const { stream } = evt
      dispatch({ type: 'removeStream', stream: stream })
    },
    removeStreamById (evt) {
      const { uid } = evt
      dispatch({ type: 'removeStream', uid: uid })
    },
    connectionStateChanged (evt) {
      methods.toastInfo(`${evt.curState}`)
    }
  }

  return (
    <StateContext.Provider value={state}>
      <MutationContext.Provider value={methods}>
        <CustomizedSnackbar toasts={toasts} />
        {state.loading ? <Loading /> : null}
        {children}
      </MutationContext.Provider>
    </StateContext.Provider>
  )
}

export function useGlobalState () {
  return useContext(StateContext)
};

export function useGlobalMutation () {
  return useContext(MutationContext)
};