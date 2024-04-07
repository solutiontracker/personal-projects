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
      showChat: false,
      count_chat_messages: 0,
      sort_number: '',
      stream_chat_history: '',
      event_id: this.props.event.id,
      agenda_id: '',
      action: 'close-channel-connection',
      attendee_id: (this.props.auth && this.props.auth.data ? this.props.auth.data.user.id : ''),
      preLoader: false,
      streamingLoader: false
    };

    this.localVideoref = React.createRef();
    this.remoteVideoref = React.createRef();
    this.chatContentRef = React.createRef()

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
    }
    // Return null to indicate no change to state.
    return null;
  }

  componentDidMount() {
    this.streamingAction();
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.attendee_id !== this.state.attendee_id) {
      this.streamingAction();
    }

    window.addEventListener("beforeunload", this.beforeUnload, false)
  }

  beforeUnload = e => {
    e.preventDefault();
    this.closeConnection();
  };

  streamingAction() {
    //streaming actions
    socket.off(`event-buizz:event-streaming-actions-${this.state.event_id}-${this.state.attendee_id}`);
    socket.on(`event-buizz:event-streaming-actions-${this.state.event_id}-${this.state.attendee_id}`, data => {
      var json = JSON.parse(data.data_info);
      if (json.action === "send-chat-message") {
        this.setState({
          count_chat_messages: (Number(this.state.count_chat_messages) + 1),
          stream_chat_history: this.state.stream_chat_history + '' + json.content
        }, () => {
          this.chatContentRef.current.scrollTop = (this.chatContentRef !== undefined && this.chatContentRef.current !== undefined && this.chatContentRef.current.scrollHeight !== undefined ? this.chatContentRef.current.scrollHeight : 0);
        });
      } else if (json.action === "start-streaming") {
        this.setState({
          count_chat_messages: json.count_chat_messages,
          sort_number: "You are " + json.sort_number + " in queue",
          stream_chat_history: json.stream_chat_history,
          channelName: json.channel_name,
          clientId: json.channel_name,
          agenda_id: json.agenda_id,
        }, () => {
          confirmAlert({
            customUI: ({ onClose }) => {
              return (
                <div className="app-popup-wrapper">
                  <div className="app-popup-container">
                    <div style={{ backgroundColor: this.props.event.settings.primary_color }} className="app-popup-header">
                      {this.props.event.labels.DESKTOP_APP_LABEL_TECHNICIAN_CALLING}
                    </div>
                    <div className="app-popup-pane">
                      <div className="gdpr-popup-sec">
                        <p>{ReactHtmlParser(this.props.event.labels.DESKTOP_APP_LABEL_REQUESTED_FOR_VIDEO_CHECKING_CONFIRMATION_ALERT)}</p>
                      </div>
                    </div>
                    <div className="app-popup-footer">
                      <button
                        style={{ backgroundColor: this.props.event.settings.primary_color }}
                        className="btn btn-cancel"
                        onClick={() => {
                          onClose();
                          this.closeConnection();
                        }}
                      >
                        {this.props.event.labels.DESKTOP_APP_LABEL_REJECT_VIDEO_CALL || 'Reject'}
                      </button>
                      <button
                        style={{ backgroundColor: this.props.event.settings.primary_color }}
                        className="btn btn-success"
                        onClick={() => {
                          onClose();
                          this.setState({
                            streamingLoader: true
                          }, () => {
                            this.startViewer(this.localVideoref, this.remoteVideoref, this.getFormValues(), null, event => { });
                          })
                        }}
                      >
                        {this.props.event.labels.DESKTOP_APP_LABEL_ACCEPT_VIDEO_CALL || 'Accept'}
                      </button>
                    </div>
                  </div>
                </div>
              );
            }
          });
        });
      } else if (json.action === "stop-streaming") {
        this.stopViewer();
        this.setState({
          showChat: false
        });
      }
    });
  }

  closeConnection() {
    this._isMounted = true;
    this.setState({ preLoader: true, action: 'close-channel-connection' }, () => {
      service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/myturnlist/streaming-actions`, this.state)
        .then(
          response => {
            if (response.success) {
              if (this._isMounted) {
                this.setState({
                  preLoader: false,
                });
              }
            }
          },
          error => { }
        );
    });
  }

  sendMessage() {
    this._isMounted = true;
    if (this.state.message) {
      this.setState({ action: "send-chat-message" }, () => {
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/myturnlist/streaming-actions`, this.state)
          .then(
            response => {
              if (response.success) {
                if (this._isMounted) {
                  this.setState({
                    message: '',
                    preLoader: false
                  });
                }
              }
            },
            error => { }
          );
      });
    }
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
        showChat: true,
        streamingLoader: false,
      }, () => {
        this.chatContentRef.current.scrollTop = this.chatContentRef.current.scrollHeight;
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
    if (this.state.showChat) {
      this.closeConnection();
    }

    window.removeEventListener("beforeunload", this.beforeUnload, false)
  }

  render() {
    return (
      <React.Fragment>
        <style dangerouslySetInnerHTML={{
          __html: `
              .bottomchatpanel span.btnmimize::before {
                background: ${this.props.event.settings.primary_color} !important;
              }
          `}} />
        {this.state.streamingLoader && <Loader className="fixed" heading='Connecting...' />}
        {this.state.preLoader && <Loader fixed="true" />}
        {this.state.showChat && (
          <div id="bottomuserchat">
            <div className="bottomchatpanel">
              <span className="btn_chat">
                <span>{this.state.count_chat_messages}</span>
                <span style={{ color: this.props.event.settings.primary_color }} className="material-icons">chat</span>
              </span>
              <span onClick={this.props.onClick} className="btnmimize"></span>
            </div>
            <div className={`row ${this.props.count ? 'd-flex' : 'd-none'}`}>
              <div className="col-8">
                <div className="captionboxchat d-flex">
                  <div className="userpicture">
                    <figure className="captionboxchat-video">
                      <video ref={this.remoteVideoref} controls={true} className="moderator-view" autoPlay={true} playsInline={true}></video>
                    </figure>
                  </div>
                  <div className="usercaptionarea">
                    <div className="userpicture">
                      <figure style={{ maxWidth: '60%', width: '100%', paddingBottom: '34%' }} className="captionboxchat-video">
                        <video ref={this.localVideoref} controls={true} className="attendee-view" autoPlay={true} playsInline={true} muted={true}></video>
                      </figure>
                    </div>
                    <h4>{this.state.sort_number}</h4>
                    <p className="status"><span>Connected</span></p>
                  </div>
                </div>
              </div>
              <div className="col-4 pr-0">
                <div id="chatbox">
                  <h5>Chat with the moderator</h5>
                  <div id="chathistory">
                    <div ref={this.chatContentRef} id="chatcontent">
                      {ReactHtmlParser(this.state.stream_chat_history)}
                    </div>
                  </div>
                  <div id="chattyping">
                    <textarea cols="30" value={this.state.message} name="message" rows="10" placeholder="Type something" onChange={event => { this.setState({ message: event.target.value }) }} onKeyPress={event => {
                      if (event.key === 'Enter' && !event.key !== 'shiftKey') {
                        this.sendMessage();
                      }
                    }}></textarea>
                    <button className="send-message" onClick={event => {
                      this.sendMessage();
                    }}>
                      <span style={{ color: this.props.event.settings.primary_color }} className="material-icons">
                        send
                          </span>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        )}
      </React.Fragment>
    );
  }
}

function mapStateToProps(state) {
  const { event, auth } = state;
  return {
    event, auth
  };
}

export default connect(mapStateToProps)(KinesisStreamTest);