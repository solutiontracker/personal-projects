import { useEffect, useMemo } from 'react'
import { useGlobalState, useGlobalMutation } from './container';
import { service } from 'services/service';
import socketIOClient from "socket.io-client";
const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");

export default function useStream(client, ChannelName, eventUrl, eventId, localShareScreenClient, history, auth, videoInfo, userRole) {
	const stateCtx = useGlobalState()
	const mutationCtx = useGlobalMutation()

	const [localStream, currentStream] = [stateCtx.localStream, stateCtx.currentStream]

	const otherStreams = useMemo(
		() => stateCtx.streams.filter(stream => stream.getId() !== currentStream.getId()),
		[stateCtx, currentStream])

	// const streamList = stateCtx.streams.filter((it) => it.getId() !== currentStream.getId());

	// const [streamList, localStream, currentStream] = useMemo(() => {
	//   return [stateCtx.streams, stateCtx.localStream, stateCtx.currentStream];
	// }, [stateCtx]);

	useEffect(() => {
		let mounted = true;
		const addRemoteStream = (evt) => {
			const { stream } = evt;
			client.subscribe(stream, (err) => {
				if (mounted) {
					mutationCtx.toastError(`stream ${evt.stream.getId()} subscribe failed: ${err}`)
				}
			})
		}

		if (client && client._subscribed === false && mounted) {
			if (in_array(videoInfo.type, ['agora-rooms'])) {
				client.on('connection-state-change', mutationCtx.connectionStateChanged);
			}
			client.on('stream-type-changed', (evt) => {
				//mutationCtx.toastInfo(`Uid: ${evt.uid} Stream Type Change to: ${evt.streamType}`)
			})
			client.on('stream-fallback', (evt) => {
				//mutationCtx.toastInfo(`Uid: ${evt.uid} Stream Fallback type to: ${evt.attr}`)
			})
			client.on('localStream-added', mutationCtx.addLocal)
			client.on('stream-published', (evt) => {
				mutationCtx.addStream(evt);

				//publish meeting into database  
				service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/meeting-publish`, { uid: evt.stream.getId(), channel: ChannelName, videoType : videoInfo.type, userRole: userRole  }).then(
					response => {
						if (response.success) {
							mutationCtx.setAudio(Number(response.data.meeting.audio) === 1 ? true : false);
							Number(response.data.meeting.audio) ? evt.stream.unmuteAudio() : evt.stream.muteAudio();

							mutationCtx.setVideo(Number(response.data.meeting.video) === 1 ? true : false);
							Number(response.data.meeting.video) ? evt.stream.unmuteVideo() : evt.stream.muteVideo();

							mutationCtx.setEnableShare(Number(response.data.meeting.share) === 1 ? true : false);
						}
					},
					error => { }
				);;

				//socket
				socket.off(`event-buizz:event-streaming-moderator-actions-${evt.stream.getId()}`);
				socket.on(`event-buizz:event-streaming-moderator-actions-${evt.stream.getId()}`, data => {
					var json = JSON.parse(data.data_info);
					if (json.actionBy === "moderator") {
						if (json.control === "audio") {
							Number(json.value) === 1 ? evt.stream.unmuteAudio() : evt.stream.muteAudio();
							mutationCtx.setAudio(Number(json.value) === 1 ? true : false);
							mutationCtx.setEnableAudio(Number(json.value) === 1 ? true : false);
						} else if (json.control === "video") {
							Number(json.value) === 1 ? evt.stream.unmuteVideo() : evt.stream.muteVideo();
							mutationCtx.setVideo(Number(json.value) === 1 ? true : false);
							mutationCtx.setEnableVideo(Number(json.value) === 1 ? true : false);
						} else if (json.control === "handle-share") {
							mutationCtx.setEnableShare(Number(json.value) === 1 ? true : false);
							if (stateCtx.shareStream) {
								service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/handle-screen-sharing`, { uid: '', channel: ChannelName, attendee_id: auth.id })
									.then(
										response => {
											if (response.success) {
												localShareScreenClient && localShareScreenClient.leaveShareScreen();
												mutationCtx.setShareStream(null);
												mutationCtx.setShareStreamId(null);
											}
										},
										error => { }
									);
							}
						}
					}
				});
			})
			client.on('stream-added', addRemoteStream)
			client.on('stream-removed', mutationCtx.removeStream)
			client.on('stream-subscribed', (evt) => {
				console.log('stream subscribed', evt.stream);
				client.setStreamFallbackOption(evt.stream, 2);
				mutationCtx.addStream(evt);

				//set share streamId if exist 
				if (in_array(Number(evt.stream.getId()), [1, 2])) {
					mutationCtx.setShareStreamId(evt.stream.getId());
				}
			});
			client.on('peer-leave', (evt) => {
				mutationCtx.removeStreamById(evt);
				if (in_array(Number(evt.stream.getId()), [1, 2])) {
					mutationCtx.setShareStreamId(null);
					mutationCtx.setShareStream(null);
				}
			});
			client.on('liveStreamingStarted', (evt) => {
				service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/started-live-streaming`, { uid: auth.id, channel: ChannelName });
				console.log('liveStreamingStarted', evt)
			});
			client.on('liveStreamingFailed', (evt) => {
				service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/failed-live-streaming`, { uid: auth.id, channel: ChannelName });
				console.log('liveStreamingFailed', evt)
			});
			client.on('liveStreamingStopped', (evt) => {
				service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/stopped-live-streaming`, { uid: auth.id, channel: ChannelName });
				console.log('liveStreamingStopped', evt)
			});
			client.on('liveTranscodingUpdated', (evt) => {
				mutationCtx.toastInfo(`Live streaming updated!`)
				console.log('liveTranscodingUpdated', evt)
			});
			client._subscribed = true;

			//local sharing client
			localShareScreenClient.on('stream-published', (evt) => {
				service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/handle-screen-sharing`, { uid: evt.stream.getId(), channel: ChannelName, attendee_id: auth.id })
					.then(
						response => {
							if (response.success) {
								mutationCtx.setShareStream(evt.stream.getId());
								mutationCtx.setShareStreamId(Number(evt.stream.getId()));
							}
						},
						error => { }
					);
			});

			const canceledScreenSharing = () => {
				localShareScreenClient && localShareScreenClient.leaveShareScreen();
				service.post(`${process.env.REACT_APP_URL}/${eventUrl}/agora/handle-screen-sharing`, { uid: '', channel: ChannelName, attendee_id: auth.id })
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

			localShareScreenClient.on("stopScreenSharing", canceledScreenSharing);

		}
		return () => mounted = false;
	}, [client, mutationCtx])

	useEffect(() => {
		let mounted = true;
		if (client && client._subscribed === true && currentStream != null && mounted) {
			if (client) client.setRemoteVideoStreamType(currentStream, 0)
			if (otherStreams.length > 4) {
				otherStreams.forEach((otherStream, i) => {
					if (!in_array(Number(otherStream.getId()), [1, 2])) {
						if (client) client.setRemoteVideoStreamType(otherStream, 1);
					}
				});
			}
		}
		return () => mounted = false;
	}, [client, currentStream, otherStreams])

	return [localStream, currentStream, otherStreams]
}
