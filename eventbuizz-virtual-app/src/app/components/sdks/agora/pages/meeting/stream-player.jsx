import React, { useMemo, useState, useEffect } from 'react'
import PropTypes from 'prop-types'
import Tooltip from '@material-ui/core/Tooltip'
import clsx from 'clsx'
import { makeStyles } from '@material-ui/core/styles'
import { useGlobalState, useGlobalMutation } from '@app/sdks/agora/utils/container'
import { service } from 'services/service';

const in_array = require("in_array");

const useStyles = makeStyles({
  menu: {
    height: '150px',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: '10'
  },
  customBtn: {
    width: '50px',
    height: '50px',
    borderRadius: '26px',
    backgroundColor: 'rgba(0, 0, 0, 0.4)',
    backgroundSize: '50px',
    cursor: 'pointer'
  },
  leftAlign: {
    display: 'flex',
    flex: '1',
    justifyContent: 'space-evenly'
  },
  rightAlign: {
    display: 'flex',
    flex: '1',
    justifyContent: 'center'
  },
  menuContainer: {
    width: '100%',
    height: '100%',
    position: 'absolute',
    display: 'flex',
    flexDirection: 'column',
    justifyContent: 'flex-end'
  }
})

StreamPlayer.propTypes = {
  stream: PropTypes.object
}

export default function StreamPlayer(props) {

  const { stream, domId, uid, channel, currentStream, client, config, auth } = props

  const [resume, changeResume] = useState(false)

  const [autoplay, changeAutoplay] = useState(false)

  const [attendee, setAttendee] = useState({});

  const [meeting, setMeeting] = useState({ audio: 1, video: 1, share: 0 });

  const classes = useStyles();

  const stateCtx = useGlobalState();

  const mutationCtx = useGlobalMutation();

  const _isMounted = React.useRef(true);

  const [audioButton, setAudioButton] = useState(props.host === "audience" ? true : false);

  const handleClick = () => {
    if (autoplay && !resume) {
      stream.resume()
      changeResume(true)
    }
  }

  const handleDoubleClick = (evt) => {
    evt.stopPropagation();
    if (props.host && props.host === "host") {
      service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-presenter`, { uid: stream.getId(), channel: channel });
      props.onDoubleClick(stream);
    }
  }

  const [state, setState] = useState({
    accessDelay: 0,
    fps: 0,
    resolution: 0
  })

  const analytics = useMemo(() => state, [state])

  useEffect(() => {
    if (!stream) return () => { }

    const timer = setInterval(() => {
      stream.getStats((stats) => {
        const width = props.local ? stats.videoSendResolutionWidth : stats.videoReceiveResolutionWidth
        const height = props.local ? stats.videoSendResolutionHeight : stats.videoReceiveResolutionHeight
        const fps = props.local ? stats.videoSendFrameRate : stats.videoReceiveFrameRate
        if (_isMounted.current) {
          setState({
            accessDelay: `${stats.accessDelay ? stats.accessDelay : 0}`,
            resolution: `${width}x${height}`,
            fps: `${fps || 0}`
          })
        }
      })
    }, 500)

    return () => {
      clearInterval(timer);
      _isMounted.current = false;
    }
  }, [stream])

  const lockPlay = React.useRef(false)

  useEffect(() => {
    if (!stream || !domId || lockPlay.current || stream.isPlaying()) return

    //set share streamId if exist 
    if (stream && in_array(Number(uid), [1, 2])) {
      if (_isMounted.current) {
        mutationCtx.setShareStreamId(uid);
      }
    }

    //set stream type
    if (Number(currentStream.getId()) === uid && client) {
      client.setRemoteVideoStreamType(stream, 0)
    }

    lockPlay.current = true

    stream.play(domId, { fit: 'cover' }, (errState) => {
      if (_isMounted.current) {
        if (errState && errState.status !== 'aborted') {
          console.log('stream-player play failed ', domId)
          changeAutoplay(true)
        }
        lockPlay.current = false
      }
    });

    //Attendee detail 
    if (!in_array(Number(uid), [1, 2])) {
      service.post(`${process.env.REACT_APP_URL}/${props.event.url}/attendee/detail/${uid}`, { channel: channel })
        .then(
          response => {
            if (response.success) {
              if (_isMounted.current) {
                if (response.data.detail) {
                  setAttendee(response.data.detail);
                  mutationCtx.toastInfo(`${response.data.detail.first_name + ' ' + response.data.detail.last_name} has joined.`);
                }
                if (response.data && response.data.meeting) {
                  setMeeting(response.data.meeting);
                }
              }
            }
          },
          error => { }
        );
    }

    return () => {
      if (stream && stream.isPlaying()) {
        stream.stop()
      }
      _isMounted.current = false;
    }
  }, [stream, domId])

  const handleMic = (e) => {
    service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-mic`, { uid: e.target.getAttribute('uid'), value: e.target.getAttribute('value'), actionBy: 'moderator', channel: channel })
      .then(
        response => {
          if (response.success && _isMounted.current) {
            document.getElementById("streaming-mic-uid-" + response.uid).classList.remove('mute-audio', 'unmute-audio');
            document.getElementById("streaming-mic-uid-" + response.uid).classList.add(Number(response.value) === 1 ? "mute-audio" : 'unmute-audio');
            document.getElementById("streaming-mic-uid-" + response.uid).setAttribute('value', Number(response.value) === 1 ? 0 : 1);
          }
        },
        error => { }
      );
  }

  const handleVid = (e) => {
    service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-vid`, { uid: e.target.getAttribute('uid'), value: e.target.getAttribute('value'), actionBy: 'moderator', channel: channel })
      .then(
        response => {
          if (response.success && _isMounted.current) {
            document.getElementById("streaming-vid-uid-" + response.uid).classList.remove('mute-video', 'unmute-video');
            document.getElementById("streaming-vid-uid-" + response.uid).classList.add(Number(response.value) === 1 ? "mute-video" : 'unmute-video');
            document.getElementById("streaming-vid-uid-" + response.uid).setAttribute('value', Number(response.value) === 1 ? 0 : 1);
          }
        },
        error => { }
      );
  }

  const handleShare = (e) => {
    service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-share`, { uid: e.target.getAttribute('uid'), value: e.target.getAttribute('value'), actionBy: 'moderator', channel: channel })
      .then(
        response => {
          if (response.success && _isMounted.current) {
            document.getElementById("streaming-share-uid-" + response.uid).classList.remove('mute-screen-shot', 'unmute-screen-shot');
            document.getElementById("streaming-share-uid-" + response.uid).classList.add(Number(response.value) === 1 ? "unmute-screen-shot" : 'mute-screen-shot');
            document.getElementById("streaming-share-uid-" + response.uid).setAttribute('value', Number(response.value) === 1 ? 0 : 1);
          }
        },
        error => { }
      );
  }

  const handleLive = (e) => {
    service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/start-live-streaming`, { uid: e.target.getAttribute('uid'), channel: channel });
  }

  const handleSubscriberAudio = (evt) => {
    var videos = document.getElementsByTagName("video");

    for (var i = 0, len = videos.length; i < len; i += 1) {
      videos[i].click();
    }
    
    setAudioButton(false);
  }

  return (
    stream
      ? <div style={props.style} className={`stream-player grid-player ${audioButton ? 'ipad-safari' : ''} ${(Number(stateCtx.shareStreamId) === Number(stream.getId()) ? 'share-stream-thumb' : '')} ${(props.main ? 'main-stream-player-alt' : '')} ${autoplay ? 'autoplay' : ''}`} id={domId} onClick={uid ? handleClick : null} onDoubleClick={props.onDoubleClick ? handleDoubleClick : null}>
        {props.showProfile && ((attendee && attendee.first_name) || in_array(process.env.REACT_APP_ENVIRONMENT, ["local", "dev", "stage"]))
          ? <div className={(attendee && attendee.first_name) || in_array(process.env.REACT_APP_ENVIRONMENT, ["local", "dev", "stage"]) ? `main-stream-profile` : ''}>
            {attendee && attendee.first_name !== undefined && (
              <span>{attendee.first_name + ' ' + attendee.last_name}</span>
            )}
            {in_array(process.env.REACT_APP_ENVIRONMENT, ["local", "dev", "stage"]) && (
              <span>Video: {analytics.fps}fps {analytics.resolution}</span>
            )}
          </div>
          : null}
        {!props.local && attendee.id && props.host && props.host === "host" && uid && !in_array(Number(uid), [Number(stateCtx.shareStream), Number(stateCtx.shareStreamId)]) ? (
          <div className={`stream-uid`}>
            <Tooltip title={(Number(meeting.audio) === 1 ? props.event.labels.DESKTOP_APP_LABEL_MUTE_MEETING : props.event.labels.DESKTOP_APP_LABEL_UNMUTE_MEETING)}>
              <i id={`streaming-mic-uid-${uid}`} value={Number(meeting.audio) === 1 ? 0 : 1} uid={uid} onClick={handleMic} className={clsx(classes.customBtn, 'margin-right-19', (Number(meeting.audio) === 1 ? 'mute-audio' : 'unmute-audio'))} />
            </Tooltip>
            <Tooltip title={(Number(meeting.video) === 1 ? props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_OFF : props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_ON)}>
              <i id={`streaming-vid-uid-${uid}`} value={Number(meeting.video) === 1 ? 0 : 1} uid={uid} onClick={handleVid} className={clsx(classes.customBtn, 'margin-right-19', (Number(meeting.video) === 1 ? 'mute-video' : 'unmute-video'))} />
            </Tooltip>
            <Tooltip title={(Number(meeting.share) === 1 ? props.event.labels.DESKTOP_APP_LABEL_HIDE_SHARE_CONTENT : props.event.labels.DESKTOP_APP_LABEL_SHARE_CONTENT)}>
              <i id={`streaming-share-uid-${uid}`} value={Number(meeting.share) === 1 ? 0 : 1} uid={uid} onClick={handleShare} className={clsx(classes.customBtn, 'margin-right-19', (Number(meeting.share) === 1 ? 'unmute-screen-shot' : 'mute-screen-shot'))} />
            </Tooltip>
            {config.streaming_url && config.streaming_key && (
              <Tooltip title="Live stream">
                <i uid={uid} onClick={handleLive} className={clsx(classes.customBtn, 'margin-right-19', (stateCtx.liveStream && Number(stateCtx.liveStream) === Number(uid)) ? 'lived-stream' : 'live-stream')} />
              </Tooltip>
            )}
          </div>
        ) : null}
        {(props.local || audioButton) ? <div className={classes.menuContainer}>
          {(props.host === "host" || props.host === "participant") && <div className={`stream-uid`}>
            {!in_array(Number(uid), [Number(stateCtx.shareStream), Number(stateCtx.shareStreamId)]) && (
              <React.Fragment>
                <Tooltip style={{ display: (stateCtx.enableVideo ? 'block' : 'none') }} title={stateCtx.muteVideo ? props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_OFF : props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_ON}>
                  <i onClick={props.handleControl ? props.handleControl('video', currentStream.getId()) : null} className={clsx(classes.customBtn, 'margin-right-19', stateCtx.muteVideo ? 'mute-video' : 'unmute-video')} />
                </Tooltip>
                <Tooltip style={{ display: (stateCtx.enableAudio ? 'block' : 'none') }} title={stateCtx.muteAudio ? props.event.labels.DESKTOP_APP_LABEL_MUTE_MEETING : props.event.labels.DESKTOP_APP_LABEL_UNMUTE_MEETING}>
                  <i onClick={props.handleControl ? props.handleControl('audio', currentStream.getId()) : null} className={clsx(classes.customBtn, 'margin-right-19', stateCtx.muteAudio ? 'mute-audio' : 'unmute-audio')} />
                </Tooltip>
                {(stateCtx.enableShareStream === true || props.host === "host") && (
                  <Tooltip title={stateCtx.shareStream ? props.event.labels.DESKTOP_APP_LABEL_HIDE_SHARE_CONTENT : props.event.labels.DESKTOP_APP_LABEL_SHARE_CONTENT}>
                    <i onClick={props.handleControl ? props.handleControl('screen-sharing', stateCtx.shareStream) : null} className={clsx(classes.customBtn, 'margin-right-19', stateCtx.shareStream ? 'mute-screen-shot' : 'unmute-screen-shot')} />
                  </Tooltip>
                )}
                {props.host === "host" && config.streaming_url && config.streaming_key && (
                  <Tooltip title="Live stream">
                    <i onClick={props.handleControl ? props.handleControl('start-live-streaming', uid) : null} className={clsx(classes.customBtn, 'margin-right-19', (stateCtx.liveStream && Number(stateCtx.liveStream) === Number(uid)) ? 'lived-stream' : 'live-stream')} />
                  </Tooltip>
                )}
                <Tooltip title={props.event.labels.DESKTOP_APP_LABEL_LEAVE_MEETING}>
                  <i onClick={() => {
                    client && client.leave().then(() => {
                      service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-screen-sharing`, { uid: '', channel: channel, attendee_id: auth.id });
                      mutationCtx.setShareStream(null);
                      mutationCtx.setShareStreamId(null);
                      if (props.host === 'host') {
                        if (props.video !== undefined && props.video.type === "agora-panel-disscussions") {
                          props.history.push(`/event/${props.event.url}/agora/join-video-meeting/${props.video.id}/${channel}/${props.host}/0`)
                        } else {
                          mutationCtx.clearAllStream();
                          props.history.push(`/event/${props.event.url}/streaming`)
                        }
                      } else {
                        mutationCtx.clearAllStream();
                        if (props.video !== undefined && props.video.type === "agora-panel-disscussions") {
                          props.history.push(`/event/${props.event.url}/agora/join-video-meeting/${props.video.id}/${channel}/${props.host}/0`)
                        } else {
                          props.history.push(`/event/${props.event.url}/streaming`);
                        }
                      }
                    })
                  }} className={clsx(classes.customBtn, 'margin-right-19', 'leave-stream')} />
                </Tooltip>
              </React.Fragment>
            )}
          </div>}
          {audioButton && (
            <div className={`stream-uid`}>
              <Tooltip title={props.event.labels.DESKTOP_APP_LABEL_UNMUTE_MEETING}>
                <i onClick={handleSubscriberAudio} className={clsx(classes.customBtn, 'margin-right-19', 'unmute-speaker')} />
              </Tooltip>
            </div>
          )}
        </div> : null}
      </div>
      : ''
  )
}
