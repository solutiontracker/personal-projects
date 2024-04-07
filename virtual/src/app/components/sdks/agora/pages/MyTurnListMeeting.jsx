import React, { useEffect, useMemo } from 'react'
import { useGlobalState, useGlobalMutation } from '@app/sdks/agora/utils/container'
import useStream from '@app/sdks/agora/utils/use-stream'
import RTCClient from '@app/sdks/agora/rtc-client'
import MyTurnListPlayer from './meeting/MyTurnListPlayer'

const MyTurnListMeeting = (props) => {
    const stateCtx = useGlobalState()

    const mutationCtx = useGlobalMutation();

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

    const [localStream, currentStream] = useStream(localClient, channelName, event.url, event.id, localShareScreenClient, history, auth, video)

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
            uid: auth.id,
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
                mutationCtx.toastError(`Media ${err.info}`)
            });
            return () => mounted = false;
        }
    }, [localClient, mutationCtx, config, channelName])

    const [otherStreams, shareStream] = useMemo(() => {
        const _otherStreams = stateCtx.streams.filter(it => it.getId() !== currentStream.getId())
        const _shareStream = stateCtx.shareStream;
        return [_otherStreams, _shareStream]
    }, [currentStream, stateCtx])

    return !stateCtx.loading ? (
        <div className={`full-screen-player request-to-speak`}>
            <div className={`w-100 h-100`}>
                {stateCtx.currentStream
                    ? <MyTurnListPlayer
                        key={stateCtx.currentStream.getId()}
                        main={true}
                        showProfile={stateCtx.profile}
                        local={config.host && stateCtx.currentStream ? stateCtx.currentStream.getId() === localStream.getId() : false}
                        stream={(Number(stateCtx.currentStream.getId()) !== Number(shareStream) ? stateCtx.currentStream : null)}
                        uid={stateCtx.currentStream.getId()}
                        showUid={true}
                        host={userRole}
                        event={event}
                        agora={agora}
                        channel={channelName}
                        currentStream={currentStream}
                        client={localClient}
                        auth={auth}
                        config={config}
                        history={history}
                        video={video}
                        domId={`stream-player-${stateCtx.currentStream.getId()}`}>
                    </MyTurnListPlayer>
                    : <MyTurnListPlayer
                        main={true}
                        showProfile={stateCtx.profile}
                        local={false}
                        stream={null}
                        uid={0}
                        showUid={true}
                        host={userRole}
                        event={event}
                        agora={agora}
                        channel={channelName}
                        currentStream={currentStream}
                        client={localClient}
                        auth={auth}
                        config={config}
                        history={history}
                        video={video}
                        domId={'default'}>
                    </MyTurnListPlayer>
                }
                {otherStreams.map((stream, index) => (<MyTurnListPlayer
                    className={'stream-profile'}
                    showProfile={stateCtx.profile}
                    local={config.host ? stream.getId() === localStream.getId() : false}
                    key={index + '' + stream.getId()}
                    stream={(Number(stream.getId()) !== Number(shareStream) ? stream : null)}
                    uid={stream.getId()}
                    domId={`stream-player-${stream.getId()}`}
                    host={userRole}
                    event={event}
                    agora={agora}
                    channel={channelName}
                    currentStream={currentStream}
                    client={localClient}
                    auth={auth}
                    config={config}
                    history={history}
                    video={video}
                    showUid={true}
                >
                </MyTurnListPlayer>
                ))}
            </div>
        </div>) : null
}

export default MyTurnListMeeting
