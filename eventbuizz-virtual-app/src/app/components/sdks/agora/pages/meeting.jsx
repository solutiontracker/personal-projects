import React, { useEffect, useMemo, useState } from 'react'
import { useGlobalState, useGlobalMutation } from '@app/sdks/agora/utils/container'
import useStream from '@app/sdks/agora/utils/use-stream'
import RTCClient from '@app/sdks/agora/rtc-client'
import StreamPlayer from './meeting/stream-player'
import { service } from 'services/service';
import SettingsCard from '@app/sdks/agora/pages/index/settings-card';
import socketIOClient from "socket.io-client";

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");

const MeetingPage = (props) => {
  const stateCtx = useGlobalState()

  const mutationCtx = useGlobalMutation();

  const [openSetting, setOpenSetting] = useState(false);

  const [minimize, setMinimize] = useState(true);

  const localClient = useMemo(() => {
    const client = new RTCClient()
    if (!client._created) {
      client.createClient({ codec: stateCtx.codec, mode: stateCtx.mode })
      client._created = true
    }
    return client
  }, [stateCtx.codec, stateCtx.mode])

  const localShareScreenClient = useMemo(() => {
    const client = new RTCClient()
    if (!client._created) {
      client.createClient({ codec: stateCtx.codec, mode: stateCtx.mode })
      client._created = true
    }
    return client
  }, [stateCtx.codec, stateCtx.mode])

  const { channelName, userRole, event, agora, auth, history, token, appID, video } = props;

  const [localStream, currentStream] = useStream(localClient, channelName, event.url, event.id, localShareScreenClient, history, auth, video, userRole)

  const config = useMemo(() => {
    return {
      token: token,
      appID: appID,
      channel: channelName,
      microphoneId: stateCtx.config.microphoneId,
      cameraId: stateCtx.config.cameraId,
      resolution: stateCtx.config.resolution,
      muteVideo: true,
      muteAudio: true,
      streaming_url: video.streaming_url,
      streaming_key: video.streaming_key,
      uid: (userRole === 'audience' ? 0 : auth.id),
      url: (video && video.streaming_url && video.streaming_key ? video.streaming_url + "/" + video.streaming_key : ""),
      host: userRole === 'host' || userRole === 'participant'
    }
  }, [stateCtx])

  useEffect(() => {
    let mounted = true;
    if (!config.channel && mounted) {
      history.push(`/event/${event.url}/streaming`);
    }
    return () => mounted = false;
  }, [config.channel])

  useEffect(() => {
    return () => {
      localShareScreenClient && localShareScreenClient.leaveShareScreen();
      localClient && localClient.leave()
        .then(() => mutationCtx.clearAllStream())
    }
  }, [localClient])

  useEffect(() => {
    let mounted = true;
    if (channelName && localClient._created && localClient._joined === false && token && appID && mounted) {
      mutationCtx.startLoading()
      localClient.join(config).then(() => {
        if (config.host) {
          localClient.setClientRole('host').then(() => {
            localClient.publish();
            mutationCtx.stopLoading();
            if (config.streaming_url && config.streaming_key && userRole === 'host') {
              //live streaming
              localClient.startLiveStreaming();
            }
          }, (err) => {
            mutationCtx.toastError(`setClientRole Failure: ${err.info}`)
          })
        } else {
          localClient.setClientRole('audience').then(() => {
            mutationCtx.stopLoading()
          }, (err) => {
            mutationCtx.toastError(`setClientRole Failure: ${err.info}`)
          })
        }
      }).catch((err) => {
        localClient && localClient.leave();
        mutationCtx.toastError(`Media ${err.info}`)
      });
      return () => mounted = false;
    }
  }, [localClient, mutationCtx, config, channelName])

  useEffect(() => {
    //socket
    if (config.uid) {
      socket.off(`event-buizz:event-streaming-actions-${event.id}`);
      socket.on(`event-buizz:event-streaming-actions-${event.id}`, data => {
        var json = JSON.parse(data.data_info);
        if (json.control === "audio") {
          if (Number(config.uid) === Number(json.uid)) {
            if (document.getElementById("streaming-mic-uid-" + json.uid) !== undefined && document.getElementById("streaming-mic-uid-" + json.uid) !== null) {
              document.getElementById("streaming-mic-uid-" + json.uid).classList.remove('mute-audio', 'unmute-audio');
              document.getElementById("streaming-mic-uid-" + json.uid).classList.add(Number(json.value) === 1 ? "mute-audio" : 'unmute-audio');
              document.getElementById("streaming-mic-uid-" + json.uid).setAttribute('value', Number(json.value) === 1 ? 0 : 1);
            }
          }
        } else if (json.control === "video") {
          if (Number(config.uid) === Number(json.uid)) {
            if (document.getElementById("streaming-vid-uid-" + json.uid) !== undefined && document.getElementById("streaming-vid-uid-" + json.uid) !== null) {
              document.getElementById("streaming-vid-uid-" + json.uid).classList.remove('mute-video', 'unmute-video');
              document.getElementById("streaming-vid-uid-" + json.uid).classList.add(Number(json.value) === 1 ? "mute-video" : 'unmute-video');
              document.getElementById("streaming-vid-uid-" + json.uid).setAttribute('value', Number(json.value) === 1 ? 0 : 1);
            }
          }
        } else if (json.control === "presenter") {
          mutationCtx.setCurrentStreamById(json.uid)
        }
      });

      socket.off(`event-buizz:event-streaming-common-actions-${event.id}`);
      socket.on(`event-buizz:event-streaming-common-actions-${event.id}`, data => {
        var json = JSON.parse(data.data_info);
        if (json.control === "handle-screen-sharing") {
          mutationCtx.setShareStreamId(Number(json.uid));
          if (json.attendee_id && Number(json.attendee_id) !== Number(auth.id)) {
            mutationCtx.setShareStream(null);
            localShareScreenClient && localShareScreenClient.leaveShareScreen();
          }
        } else if (in_array(json.control, ["leave-meeting", "end-meeting"])) {
          if (video !== undefined && video.type === "agora-panel-disscussions") {
            if ((json.attendee_id && Number(json.attendee_id) === Number(auth.id) && json.control === "leave-meeting") || json.control === "end-meeting") {
              window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${event.url}/agora/join-video-meeting/${video.id}/${channelName}/${Number(video.attachedAttendees) > 0 ? 'participant' : 'audience'}/0`;
            } else if (json.attendees !== undefined && Array.isArray(json.attendees) && in_array(auth.id, json.attendees) && json.control === "leave-meeting") {
              window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${event.url}/agora/join-video-meeting/${video.id}/${channelName}/audience/0`;
            }
          } else if (video !== undefined && video.type === "agora-rooms") {
            if (json.attendees !== undefined && Array.isArray(json.attendees) && in_array(auth.id, json.attendees) && json.control === "leave-meeting") {
              history.push(`/event/${event.url}/streaming`);
            }
          }
        } else if (json.control === "close-streaming") {
          localShareScreenClient && localShareScreenClient.leaveShareScreen();
          localClient.leave().then(() => {
            mutationCtx.clearAllStream();
            history.push(`/event/${event.url}/streaming`);
          });
        } else if (json.control === "start-live-streaming") {
          if (Number(json.uid) === Number(auth.id)) {
            localClient.stopLiveStreaming();
            setTimeout(() => {
              localClient.startLiveStreaming();
            }, 1000);
          }
        } else if (json.control === "started-live-streaming") {
          mutationCtx.toastInfo(`Live streaming started.`);
          mutationCtx.setLiveStream(Number(json.uid));
        } else if (json.control === "failed-live-streaming") {
          mutationCtx.toastInfo(`Live streaming failed.`);
        } else if (json.control === "stopped-live-streaming") {
          mutationCtx.toastInfo(`Live streaming stopped.`);
        }
      });
    }
  });

  const handleControl = (name, value) => {
    return (evt) => {
      evt.stopPropagation()
      switch (name) {
        case 'video': {
          stateCtx.muteVideo ? localStream.muteVideo() : localStream.unmuteVideo()
          service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-vid`, { uid: value, value: (stateCtx.muteVideo ? 0 : 1), actionBy: 'attendee', channel: channelName })
            .then(
              response => {
                if (response.success) {
                  mutationCtx.setVideo(!stateCtx.muteVideo);
                }
              },
              error => { }
            );
          break
        }
        case 'audio': {
          stateCtx.muteAudio ? localStream.muteAudio() : localStream.unmuteAudio()
          service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/handle-mic`, { uid: value, value: (stateCtx.muteAudio ? 0 : 1), actionBy: 'attendee', channel: channelName })
            .then(
              response => {
                if (response.success) {
                  mutationCtx.setAudio(!stateCtx.muteAudio)
                }
              },
              error => { }
            );
          break
        }
        case 'screen-sharing': {
          if (!value) {
            if (channelName) {
              mutationCtx.startLoading();
              config.uid = (Number(stateCtx.shareStreamId) === 1 ? 2 : 1);
              localShareScreenClient.join(config, true).then(() => {
                localShareScreenClient.setClientRole('host').then(() => {
                  localShareScreenClient.publish();
                  mutationCtx.stopLoading();
                }, (err) => {
                  mutationCtx.toastError(`setClientRole Failure: ${err.info}`)
                })
              }).catch((err) => {
                localShareScreenClient && localShareScreenClient.leaveShareScreen();
                mutationCtx.toastError(`Permission denied`);
                mutationCtx.stopLoading();
              })
            }
          } else {
            localShareScreenClient && localShareScreenClient.leaveShareScreen();
            service.post(`${process.env.REACT_APP_URL}/${event.url}/agora/handle-screen-sharing`, { uid: '', channel: channelName, attendee_id: auth.id })
              .then(
                response => {
                  if (response.success) {
                    mutationCtx.setShareStream(null);
                    mutationCtx.setShareStreamId(null);
                  }
                },
                error => { }
              );
          }
          break
        }
        case 'start-live-streaming': {
          service.post(`${process.env.REACT_APP_URL}/${props.event.url}/agora/start-live-streaming`, { uid: value, channel: channelName });
          break;
        }
        default:
          throw new Error(`Unknown click handler, name: ${name}`)
      }
    }
  }

  const handleDoubleClick = (stream) => {
    mutationCtx.setCurrentStream(stream)
  }

  const [otherStreams, shareStreamId, shareStream] = useMemo(() => {
    const _otherStreams = stateCtx.streams.filter(it => it.getId() !== currentStream.getId())
    const _shareStreamId = stateCtx.shareStreamId;
    const _shareStream = stateCtx.shareStream;
    return [_otherStreams, _shareStreamId, _shareStream]
  }, [currentStream, stateCtx])

  return !stateCtx.loading ? (
    <div className={`${video && in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? 'full-screen-player' : 'meeting'} ${in_array(video.type, ['agora-panel-disscussions']) ? 'panel-disscussions' : ''}`} style={{ backgroundImage: 'url(' + (video && in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) && video.thumbnail ? video.thumbnail : '') + ')' }}>
      <div className={`${video && in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? '' : 'current-view'}`}>
        <div className={`${video && !in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? 'flex-container' : ''}`}>
          <div className={`${video && !in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? in_array(Number(shareStreamId), [1, 2]) ? 'share-stream-screen' : 'app-grid-layout-' + otherStreams.length + ' z-index-5 custom-grid-layout' : ''} ${!shareStream && minimize ? 'minimize' : ''} w-100 h-100 ${!in_array(video.type, ['agora-rooms']) ? 'iframe-wrapped' : ''}`}>
            {stateCtx.currentStream
              ? <StreamPlayer
                key={stateCtx.currentStream.getId()}
                main={true}
                showProfile={stateCtx.profile}
                local={config.host && stateCtx.currentStream ? stateCtx.currentStream.getId() === localStream.getId() : false}
                stream={(Number(stateCtx.currentStream.getId()) !== Number(shareStream) ? stateCtx.currentStream : null)}
                onDoubleClick={handleDoubleClick}
                uid={stateCtx.currentStream.getId()}
                showUid={true}
                host={userRole}
                event={event}
                agora={agora}
                channel={channelName}
                currentStream={currentStream}
                handleControl={handleControl}
                client={localClient}
                auth={auth}
                config={config}
                history={history}
                video={video}
                domId={`stream-player-${stateCtx.currentStream.getId()}`}>
              </StreamPlayer>
              : <StreamPlayer
                main={true}
                showProfile={stateCtx.profile}
                local={false}
                stream={null}
                onDoubleClick={handleDoubleClick}
                uid={0}
                showUid={true}
                host={userRole}
                event={event}
                agora={agora}
                channel={channelName}
                currentStream={currentStream}
                handleControl={handleControl}
                client={localClient}
                auth={auth}
                config={config}
                history={history}
                video={video}
                domId={'default'}>
              </StreamPlayer>
            }
            {otherStreams.map((stream, index) => (<StreamPlayer
              className={'stream-profile'}
              showProfile={stateCtx.profile}
              local={config.host ? stream.getId() === localStream.getId() : false}
              key={index + '' + stream.getId()}
              stream={(Number(stream.getId()) !== Number(shareStream) ? stream : null)}
              uid={stream.getId()}
              domId={`stream-player-${stream.getId()}`}
              onDoubleClick={handleDoubleClick}
              host={userRole}
              event={event}
              agora={agora}
              channel={channelName}
              currentStream={currentStream}
              handleControl={handleControl}
              client={localClient}
              auth={auth}
              config={config}
              history={history}
              video={video}
              showUid={true}
            >
            </StreamPlayer>
            ))}
          </div>
        </div>
        {!shareStream && in_array(Number(shareStreamId), [1, 2]) && (
          <span onClick={() => setMinimize(!minimize)} className={`op-minimize-screen material-icons ${minimize ? 'minimize' : ''}`}>double_arrow</span>
        )}
        {openSetting && (
          <div style={{ background: 'none' }} className="device-wrapper">
            <div className="device-container" style={{ width: '559px', height: '220px' }}>
              <div className="device-menu" style={{ display: 'contents' }}>
                <div className="device-viewpoint">
                  <div className="w-100">
                    <span className="btn-close-viewpoints" onClick={() => setOpenSetting(false)}><i className="material-icons">close</i></span>
                    <SettingsCard />
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
        {video && !in_array(video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar', "agora-panel-disscussions"]) && (
          <span onClick={() => setOpenSetting(true)} className="btn_open_settings">
            <i className="material-icons">tune</i>
          </span>
        )}
      </div>
    </div>) : null
}

export default MeetingPage
