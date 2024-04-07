import React, { Component } from 'react';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import { SignalingClient } from 'amazon-kinesis-video-streams-webrtc';
import AWS from 'aws-sdk';
import socketIOClient from "socket.io-client";
import ReactHtmlParser from 'react-html-parser';
import { GeneralAction } from 'actions/general-action';
import { store } from 'helpers';
import { withRouter } from "react-router-dom";

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const poolId = process.env.REACT_APP_IdentityPoolId;

const RoleArn = process.env.REACT_APP_RoleArn;

const Region = process.env.REACT_APP_Region;

const viewer = {};

class KinesisStreamTest extends Component {
	_isMounted = false;

	constructor(props) {
		super(props);
		this.state = {
			video: (this.props.video !== undefined && this.props.video ? this.props.video : ""),
			channelName: '',
			clientId: '',
			event_id: this.props.event.id,
			agenda_id: (this.props.match.params.request_to_speak_program_id || ''),
			program_id: (this.props.match.params.program_id || ''),
			action: 'close-channel-connection',
			attendee_id: (this.props.auth && this.props.auth.data ? this.props.auth.data.user.id : ''),
			streamingLoader: true,
			preLoader: false,
			projector_mode: (this.props.event && this.props.event.settings && this.props.event.settings.projector_mode ? this.props.event.settings.projector_mode : "moderator_camera"),
			popup: false
		};

		this.localVideoref = React.createRef();
		this.remoteVideoref = React.createRef();

		AWS.config.credentials = new AWS.CognitoIdentityCredentials({
			IdentityPoolId: poolId,
			RoleArn: RoleArn
		});

		AWS.config.region = Region;
	}

	static getDerivedStateFromProps(props, state) {
		if (props.auth && props.auth.data && props.auth.data.user.id !== state.attendee_id && props.auth.data.user.id !== undefined) {
			socket.off(`event-buizz:event-streaming-actions-${state.event_id}-${state.attendee_id}`);
			return {
				attendee_id: props.auth.data.user.id,
			};
		} else if (props.video && state.video.current_video !== props.video.current_video) {
			return {
				video: props.video,
			};
		}
		// Return null to indicate no change to state.
		return null;
	}

	componentDidMount() {
		this.streamingAction();
		this.loadAttendee();
		window.addEventListener("beforeunload", this.beforeUnload, false)
	}

	beforeUnload = e => {
		e.preventDefault();
		this.closeConnection();
	};

	loadAttendee() {
		this._isMounted = true;
		this.setState({ streamingLoader: true });
		service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/myturnlist/speaking-attendee`, this.state)
			.then(
				response => {
					if (response.success) {
						if (this._isMounted) {
							if (response.data && response.data.attendee) {
								this.setState({
									channel_name: `MyTurnList-${this.props.event.id}-${response.data.attendee.agenda_id}-${this.state.attendee_id}`,
									agenda_id: response.data.attendee.agenda_id,
									action: "start-live-streaming"
								}, () => {
									//if connection exist reconnect again it
									this.reconnectConnection();
								});
							}
						}
					}
				},
				error => { }
			);
	}

	componentDidUpdate(prevProps, prevState) {
		if ((prevState.attendee_id !== this.state.attendee_id) || (this.state.video.current_video !== prevState.video.current_video)) {
			this.streamingAction();
		}
	}

	streamingAction() {
		//streaming actions
		socket.off(`event-buizz:event-streaming-actions-${this.state.event_id}-${this.state.attendee_id}`);
		socket.on(`event-buizz:event-streaming-actions-${this.state.event_id}-${this.state.attendee_id}`, data => {
			var json = JSON.parse(data.data_info);
			if (json.action === "start-live-streaming") {
				this.setState({
					channelName: json.channel_name + '-Live',
					clientId: json.channel_name + '-Live',
					agenda_id: json.agenda_id,
					projector_mode: (json.event_settings !== undefined ? json.event_settings.projector_mode : this.state.projector_mode)
				}, () => {
					this.requestToSpeakSocket(this, this.props.history, this.props.event.url, this.state.video, this.state.video.agenda_id, this);
					if (Number(json.attendee_id) === Number(this.state.attendee_id) && !this.props.event.myturnlist_setting.lobby_url) {
						store.dispatch(GeneralAction.stream(json));
						if (!this.props.location.pathname.includes(`/streaming-live/${this.state.video.agenda_id}/${json.agenda_id}/${this.state.video.current_video}`)) {
							this.setState({
								popup: true
							}, () => {
								confirmAlert({
									customUI: ({ onClose }) => {
										return (
											<div className="app-popup-wrapper">
												<div className="app-popup-container">
													<div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
														{this.props.event.labels.DESKTOP_APP_LABEL_REQUEST_TO_SPEAK}
													</div>
													<div className="app-popup-pane">
														<div className="gdpr-popup-sec">
															<p>{ReactHtmlParser(this.props.event.labels.DESKTOP_APP_LABEL_TRSANSMITTED_TO_LIVE_CONFIRMATION_ALERT)}</p>
														</div>
													</div>
													<div className="app-popup-footer">
														<button
															style={{ backgroundColor: this.props.event.settings.primary_color }}
															className="btn btn-cancel"
															onClick={() => {
																onClose();
																store.dispatch(GeneralAction.stream({}));
																this.closeConnection();
															}}
														>
															{this.props.event.labels.DESKTOP_APP_LABEL_CANCEL || 'Cancel'}
														</button>
														<button
															style={{ backgroundColor: this.props.event.settings.primary_color }}
															className="btn btn-success"
															onClick={() => {
																onClose();
																if (this.state.popup) {
																	this.setState({
																		streamingLoader: true
																	}, () => {
																		//take it to live screen
																		this.props.history.push(`/event/${this.props.event.url}/streaming-live/${this.state.video.agenda_id}/${json.agenda_id}/${this.state.video.current_video}`);
																		this.stopViewer();
																		this.startViewer(this.localVideoref, this.remoteVideoref, this.getFormValues(), null, event => { });
																	})
																}
															}}
														>
															{this.props.event.labels.DESKTOP_APP_LABEL_GO_LIVE || 'Live'}
														</button>
													</div>
												</div>
											</div>
										);
									},
									closeOnClickOutside: false,
								});
							})
						} else {
							this.setState({
								streamingLoader: true
							}, () => {
								this.stopViewer();
								this.startViewer(this.localVideoref, this.remoteVideoref, this.getFormValues(), null, event => { });
							})
						}
					}
				});
			} else if (json.action === "close-channel-connection") {
				if (json.channel_name === this.state.channelName) {
					this.setState({
						streamingLoader: true,
						popup: false
					}, () => {
						this.stopViewer();
						store.dispatch(GeneralAction.stream({}));
					});
				}
			}
		});
	}

	requestToSpeakSocket(classObj, history, eventUrl, video, agenda_id, _this) {
		socket.off(`event-buizz:request_to_speak_action_${this.state.event_id}_${this.state.agenda_id}`);
		socket.on(`event-buizz:request_to_speak_action_${this.state.event_id}_${this.state.agenda_id}`, function (data) {
			var json = JSON.parse(data.raw_data);
			if (json.current_action === "stop") {
				_this.setState({
					popup: false
				}, () => {
					classObj.stopViewer();
					store.dispatch(GeneralAction.stream({}));
					history.push(`/event/${eventUrl}/streaming/${video.agenda_id}/${agenda_id}/${video.current_video}`);
				});
			}
		});
	}

	reconnectConnection() {
		this._isMounted = true;
		this.setState({ streamingLoader: true, action: 'live-projector-reconnect-again' }, () => {
			this.stopViewer();
			service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/myturnlist/streaming-actions`, this.state)
				.then(
					response => {
						if (response.success) {
							if (this._isMounted) {
								this.setState({
									streamingLoader: true,
								});
							}
						}
					},
					error => { }
				);
		});
	}

	closeConnection() {
		this._isMounted = true;
		this.setState({ streamingLoader: true, action: 'close-channel-connection', channel_type: "live" }, () => {
			service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/myturnlist/streaming-actions`, this.state)
				.then(
					response => {
						if (response.success) {
							if (this._isMounted) {
								this.setState({
									streamingLoader: false,
								});
							}
						}
					},
					error => { }
				);
		});
	}

	getFormValues() {
		return {
			channelName: this.state.channelName,
			clientId: this.state.channelName,
			agenda_id: this.state.agenda_id,
			sendVideo: true,
			sendAudio: true,
			openDataChannel: false,
			widescreen: true,
			fullscreen: false,
			useTrickleICE: true,
			natTraversalDisabled: false,
			forceTURN: true,
			endpoint: null,
			sessionToken: null,
		};
	}

	async startViewer(localView, remoteView, formValues, onStatsReport, onRemoteDataMessage) {
		viewer.localView = localView;
		viewer.remoteView = remoteView;

		// Create KVS client
		const kinesisVideoClient = new AWS.KinesisVideo({
			region: AWS.config.region,
			accessKeyId: AWS.config.credentials.accessKeyId,
			secretAccessKey: AWS.config.credentials.secretAccessKey,
			sessionToken: AWS.config.credentials.sessionToken,
			endpoint: formValues.endpoint,
			correctClockSkew: true,
		});

		// Get signaling channel ARN
		const describeSignalingChannelResponse = await kinesisVideoClient
			.describeSignalingChannel({
				ChannelName: formValues.channelName,
			})
			.promise();
		const channelARN = describeSignalingChannelResponse.ChannelInfo.ChannelARN;
		console.log('[VIEWER] Channel ARN: ', channelARN);

		// Get signaling channel endpoints
		const getSignalingChannelEndpointResponse = await kinesisVideoClient
			.getSignalingChannelEndpoint({
				ChannelARN: channelARN,
				SingleMasterChannelEndpointConfiguration: {
					Protocols: ['WSS', 'HTTPS'],
					Role: 'VIEWER',
				},
			})
			.promise();
		const endpointsByProtocol = getSignalingChannelEndpointResponse.ResourceEndpointList.reduce((endpoints, endpoint) => {
			endpoints[endpoint.Protocol] = endpoint.ResourceEndpoint;
			return endpoints;
		}, {});
		console.log('[VIEWER] Endpoints: ', endpointsByProtocol);

		const kinesisVideoSignalingChannelsClient = new AWS.KinesisVideoSignalingChannels({
			region: AWS.config.region,
			accessKeyId: AWS.config.credentials.accessKeyId,
			secretAccessKey: AWS.config.credentials.secretAccessKey,
			sessionToken: AWS.config.credentials.sessionToken,
			endpoint: endpointsByProtocol.HTTPS,
			correctClockSkew: true,
		});

		// Get ICE server configuration
		const getIceServerConfigResponse = await kinesisVideoSignalingChannelsClient
			.getIceServerConfig({
				ChannelARN: channelARN,
			})
			.promise();
		const iceServers = [];
		if (!formValues.natTraversalDisabled && !formValues.forceTURN) {
			iceServers.push({ urls: `stun:stun.kinesisvideo.${AWS.config.region}.amazonaws.com:443` });
		}
		if (!formValues.natTraversalDisabled) {
			getIceServerConfigResponse.IceServerList.forEach(iceServer =>
				iceServers.push({
					urls: iceServer.Uris,
					username: iceServer.Username,
					credential: iceServer.Password,
				}),
			);
		}
		console.log('[VIEWER] ICE servers: ', iceServers);

		// Create Signaling Client
		viewer.signalingClient = new SignalingClient({
			channelARN,
			channelEndpoint: endpointsByProtocol.WSS,
			clientId: formValues.clientId,
			role: 'VIEWER',
			region: AWS.config.region,
			credentials: {
				accessKeyId: AWS.config.credentials.accessKeyId,
				secretAccessKey: AWS.config.credentials.secretAccessKey,
				sessionToken: AWS.config.credentials.sessionToken,
			},
			systemClockOffset: kinesisVideoClient.config.systemClockOffset,
		});

		const resolution = formValues.widescreen ? { width: { ideal: 1280 }, height: { ideal: 720 } } : { width: { ideal: 640 }, height: { ideal: 480 } };
		const constraints = {
			video: formValues.sendVideo ? resolution : false,
			audio: formValues.sendAudio,
		};
		const configuration = {
			iceServers,
			iceTransportPolicy: formValues.forceTURN ? 'relay' : 'all',
		};
		viewer.peerConnection = new RTCPeerConnection(configuration);
		if (formValues.openDataChannel) {
			viewer.dataChannel = viewer.peerConnection.createDataChannel('kvsDataChannel');
			viewer.peerConnection.ondatachannel = event => {
				event.channel.onmessage = onRemoteDataMessage;
			};
		}

		// Poll for connection stats
		viewer.peerConnectionStatsInterval = setInterval(() => viewer.peerConnection.getStats().then(onStatsReport), 1000);

		viewer.signalingClient.on('open', async () => {
			console.log('[VIEWER] Connected to signaling service');
			this.setState({
				streamingLoader: false,
			});
			// Get a stream from the webcam, add it to the peer connection, and display it in the local view.
			// If no video/audio needed, no need to request for the sources. 
			// Otherwise, the browser will throw an error saying that either video or audio has to be enabled.
			if (formValues.sendVideo || formValues.sendAudio) {
				try {
					viewer.localStream = await navigator.mediaDevices.getUserMedia(constraints);
					viewer.localStream.getTracks().forEach(track => viewer.peerConnection.addTrack(track, viewer.localStream));
					if (localView.current) {
						localView.current.srcObject = viewer.localStream;
					}
				} catch (e) {
					console.error('[VIEWER] Could not find webcam');
					return;
				}
			}

			// Create an SDP offer to send to the master
			console.log('[VIEWER] Creating SDP offer');
			await viewer.peerConnection.setLocalDescription(
				await viewer.peerConnection.createOffer({
					offerToReceiveAudio: true,
					offerToReceiveVideo: true,
				}),
			);

			// When trickle ICE is enabled, send the offer now and then send ICE candidates as they are generated. Otherwise wait on the ICE candidates.
			if (formValues.useTrickleICE) {
				console.log('[VIEWER] Sending SDP offer');
				viewer.signalingClient.sendSdpOffer(viewer.peerConnection.localDescription);
			}
			console.log('[VIEWER] Generating ICE candidates');
		});

		viewer.signalingClient.on('sdpAnswer', async answer => {
			// Add the SDP answer to the peer connection
			console.log('[VIEWER] Received SDP answer');
			await viewer.peerConnection.setRemoteDescription(answer);
		});

		viewer.signalingClient.on('iceCandidate', candidate => {
			// Add the ICE candidate received from the MASTER to the peer connection
			console.log('[VIEWER] Received ICE candidate');
			viewer.peerConnection.addIceCandidate(candidate);
		});

		viewer.signalingClient.on('close', () => {
			console.log('[VIEWER] Disconnected from signaling channel');
		});

		viewer.signalingClient.on('error', error => {
			console.error('[VIEWER] Signaling client error: ', error);
		});

		// Send any ICE candidates to the other peer
		viewer.peerConnection.addEventListener('icecandidate', ({ candidate }) => {
			if (candidate) {
				console.log('[VIEWER] Generated ICE candidate');

				// When trickle ICE is enabled, send the ICE candidates as they are generated.
				if (formValues.useTrickleICE) {
					console.log('[VIEWER] Sending ICE candidate');
					viewer.signalingClient.sendIceCandidate(candidate);
				}
			} else {
				console.log('[VIEWER] All ICE candidates have been generated');

				// When trickle ICE is disabled, send the offer now that all the ICE candidates have ben generated.
				if (!formValues.useTrickleICE) {
					console.log('[VIEWER] Sending SDP offer');
					viewer.signalingClient.sendSdpOffer(viewer.peerConnection.localDescription);
				}
			}
		});

		// As remote tracks are received, add them to the remote view
		viewer.peerConnection.addEventListener('track', event => {
			console.log('[VIEWER] Received remote track');
			if (remoteView.srcObject) {
				return;
			}
			viewer.remoteStream = event.streams[0];
			if (remoteView.current) {
				remoteView.current.srcObject = viewer.remoteStream;
			}
		});

		console.log('[VIEWER] Starting viewer connection');
		viewer.signalingClient.open();
	}

	stopViewer() {
		console.log('[VIEWER] Stopping viewer connection');
		if (viewer.signalingClient) {
			viewer.signalingClient.close();
			viewer.signalingClient = null;
		}

		if (viewer.peerConnection) {
			viewer.peerConnection.close();
			viewer.peerConnection = null;
		}

		if (viewer.localStream) {
			viewer.localStream.getTracks().forEach(track => track.stop());
			viewer.localStream = null;
		}

		if (viewer.remoteStream) {
			viewer.remoteStream.getTracks().forEach(track => track.stop());
			viewer.remoteStream = null;
		}

		if (viewer.peerConnectionStatsInterval) {
			clearInterval(viewer.peerConnectionStatsInterval);
			viewer.peerConnectionStatsInterval = null;
		}

		if (viewer.localView && viewer.localView.current) {
			viewer.localView.current.srcObject = null;
		}

		if (viewer.remoteView && viewer.remoteView.current) {
			viewer.remoteView.current.srcObject = null;
		}

		if (viewer.dataChannel) {
			viewer.dataChannel = null;
		}
	}

	componentWillUnmount() {
		this._isMounted = false;
		socket.off(`event-buizz:event-streaming-actions-${this.state.event_id}-${this.state.attendee_id}`);
		socket.off(`event-buizz:request_to_speak_action_${this.state.event_id}_${this.state.agenda_id}`);
		window.removeEventListener("beforeunload", this.beforeUnload, false)
	}

	closeStreaming(e) {
		store.dispatch(GeneralAction.stream({}));
		this.closeConnection();
	}

	error() {
		confirmAlert({
			customUI: ({ onClose }) => {
				return (
					<div className='app-popup-wrapper'>
						<div className="app-popup-container">
							<div className="app-popup-header" style={{ backgroundColor: this.props.event.settings.primary_color }}>
								<h4>{this.props.event.labels.DESKTOP_APP_LABEL_NETWORK_ERROR_OCCUR}</h4>
							</div>
							<div className="app-popup-pane">
								<div className="gdpr-popup-sec">
									<p>{ReactHtmlParser(this.props.event.labels.DESKTOP_APP_LABEL_NETWORK_ERROR_OCCUR_INFO)}</p>
								</div>
							</div>
							<div className="app-popup-footer">
								<button
									style={{ backgroundColor: this.props.event.settings.primary_color }}
									className="btn btn-success"
									onClick={() => {
										onClose();
										this.reconnectConnection();
									}}
								>
									OK
							</button>
							</div>
						</div>
					</div>
				);
			}
		});
	}

	render() {
		if (Object.keys(this.props.stream).length) {
			return (
				<div className="video-player-wrapper video-live">
					{this.state.preLoader && <Loader fixed="true" />}
					<div className="ProgramVideoWrapperBottom" id="videoPlayer">
						<span className="btn_cross" onClick={this.closeStreaming.bind(this)}>
							<i className="material-icons">close</i>
						</span>
						{this.state.streamingLoader ? (
							<Loader />
						) : (
								<React.Fragment>
									{this.state.projector_mode === "moderator_camera" && (
										<video ref={this.remoteVideoref} controls={true} className="moderator-view" autoPlay={true} playsInline={true}></video>
									)}
									{this.state.projector_mode === "live_streaming" && this.state.video.url && (
										<iframe title="side-iframe" width="100%" src={this.state.video.url} frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
									)}
									<video ref={this.localVideoref} controls={true} className="attendee-view" autoPlay={true} playsInline={true} muted={true}></video>
								</React.Fragment>
							)}
					</div>
				</div>
			);
		} else {
			return (
				this.state.preLoader && <Loader fixed="true" />
			);
		}
	}
}

function mapStateToProps(state) {
	const { event, auth, stream } = state;
	return {
		event, auth, stream
	};
}

export default connect(mapStateToProps)(withRouter(KinesisStreamTest));