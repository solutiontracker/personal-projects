import React from 'react';
import { OTPublisher } from 'opentok-react';
import { connect } from 'react-redux';
import { withStyles } from '@material-ui/core/styles';
import Tooltip from '@material-ui/core/Tooltip';
import clsx from 'clsx'
import { service } from 'services/service';
import socketIOClient from "socket.io-client";
const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");

const styles = () => ({
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
});

class Publisher extends React.Component {

    _isMounted = false;

    constructor(props) {
        super(props);

        this.state = {
            uid: this.props.uid,
            error: null,
            audio: true,
            video: true,
            enableVideo: true,
            enableAudio: true,
            enableShareStream: true
        };

        this.publisherEventHandlers = {
            streamCreated: event => {
                this.props.dispatch({ type: 'addStream', payload: event.stream });
                this.startRecording();
                console.log('Publisher stream created!');
            },
            streamDestroyed: event => {
                event.preventDefault();
                console.log('Publisher stream destroyed!');
            }
        };
    }

    setAudio = () => {
        this.setState({ audio: !this.state.audio });
    }

    setVideo = () => {
        this.setState({ video: !this.state.video });
    }

    changeVideoSource = () => {
        if (!this.props.vonage.publisherShareStream) {
            this.props.dispatch({ type: 'publisherShareStream', payload: 1 });
        } else {
            this.props.dispatch({ type: 'publisherShareStream', payload: null });
        }
    }

    onError = (err) => {
        this.setState({ error: `Failed to publish: ${err.message}` });
    }

    componentDidMount() {
        this._isMounted = true;
        this.publishMeeting();
        this.socket();
        navigator.mediaDevices.addEventListener("devicechange", this.updateDeviceList)
    }

    updateDeviceList = async (event) => {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const audioInputs = devices.filter(device => device.kind === 'audioinput');
        const videos = devices.filter(device => device.kind === 'videoinput');
        if (audioInputs !== undefined && audioInputs.length > 0 && videos && videos.length > 0) {
            //window.location.reload();
        }
    }

    componentWillUnmount() {
        this._isMounted = false;
        navigator.mediaDevices.removeEventListener("devicechange", this.updateDeviceList)
    }

    publishMeeting() {
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/meeting-publish`, { uid: this.state.uid, channel: this.props.channel, videoType: this.props.video.type, userRole: this.props.host })
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data && response.data.meeting) {
                                this.setState({
                                    video: Number(response.data.meeting.video) === 1 || this.props.host === "host" ? true : false,
                                    audio: Number(response.data.meeting.audio) === 1 || this.props.host === "host" ? true : false,
                                    enableVideo: Number(response.data.meeting.video) === 1 || this.props.host === "host" ? true : false,
                                    enableAudio: Number(response.data.meeting.audio) === 1 || this.props.host === "host" ? true : false,
                                    enableShareStream: Number(response.data.meeting.share) === 1 || this.props.host === "host" ? true : false
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    startRecording() {
        this._isMounted = true;
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/opentok/recording/start-recording`, {video_id: this.props.video.id})
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            console.log(response)
                        }
                    }
                },
                error => { }
            );
    }

    socket() {
        //socket
        socket.off(`event-buizz:event-streaming-moderator-actions-${this.state.uid}`);
        socket.on(`event-buizz:event-streaming-moderator-actions-${this.state.uid}`, data => {
            var json = JSON.parse(data.data_info);
            if (json.actionBy === "moderator") {
                if (this._isMounted) {
                    if (json.control === "audio") {
                        this.setState({
                            audio: Number(json.value) === 1 ? true : false,
                            enableAudio: Number(json.value) === 1 ? true : false
                        });
                    } else if (json.control === "video") {
                        this.setState({
                            video: Number(json.value) === 1 ? true : false,
                            enableVideo: Number(json.value) === 1 ? true : false
                        });
                    } else if (json.control === "handle-share") {
                        this.setState({
                            enableShareStream: Number(json.value) === 1 ? true : false
                        });
                    }
                }
            }
        });
    }

    handleDoubleClick = (stream, uid) => e => {
        e.stopPropagation();
        if (this.props.host && this.props.host === "host") {
            service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/handle-presenter`, { uid: uid, channel: this.props.channel });
        }
    }

    render() {
        const { classes } = this.props;

        return (
            <div className={`stream-player grid-player ${(this.props.main ? 'main-stream-player-alt' : '')}`} id={`stream-player-${this.state.uid}`} onDoubleClick={this.handleDoubleClick(this.props.stream, Number(this.state.uid))}>
                {this.props.vonage.profile && (this.props.name || in_array(process.env.REACT_APP_ENVIRONMENT, ["local", "dev", "stage"]))
                    ? <div className="main-stream-profile">
                        {this.props.name && (
                            <span>{this.props.name}</span>
                        )}
                    </div>
                    : null}
                <OTPublisher
                    session={this.props.session}
                    properties={{
                        name: this.props.uid + " | " + this.props.name,
                        publishAudio: this.state.audio,
                        showControls: false,
                        publishVideo: this.state.video,
                        height: '100%',
                        width: '100%'
                    }}
                    eventHandlers={this.publisherEventHandlers}
                    style={{ height: '100%', width: '100%' }}
                    onError={this.onError}
                />
                <div className={classes.menuContainer}>
                    {(this.props.host === "host" || this.props.host === "participant") && <div className={`stream-uid`}>
                        <React.Fragment>
                            <Tooltip style={{ display: (this.state.enableVideo ? 'block' : 'none') }} title={this.props.vonage.muteVideo ? this.props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_OFF : this.props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_ON}>
                                <i onClick={this.setVideo} className={clsx(classes.customBtn, 'margin-right-19', this.state.video ? 'mute-video' : 'unmute-video')} />
                            </Tooltip>
                            <Tooltip style={{ display: (this.state.enableAudio ? 'block' : 'none') }} title={this.props.vonage.muteAudio ? this.props.event.labels.DESKTOP_APP_LABEL_MUTE_MEETING : this.props.event.labels.DESKTOP_APP_LABEL_UNMUTE_MEETING}>
                                <i onClick={this.setAudio} className={clsx(classes.customBtn, 'margin-right-19', this.state.audio ? 'mute-audio' : 'unmute-audio')} />
                            </Tooltip>
                            {this.state.enableShareStream && (
                                <Tooltip title={this.props.vonage.publisherShareStream ? this.props.event.labels.DESKTOP_APP_LABEL_HIDE_SHARE_CONTENT : this.props.event.labels.DESKTOP_APP_LABEL_SHARE_CONTENT}>
                                    <i onClick={this.changeVideoSource} className={clsx(classes.customBtn, 'margin-right-19', !this.props.vonage.publisherShareStream ? 'mute-screen-shot' : 'unmute-screen-shot')} />
                                </Tooltip>
                            )}
                            <Tooltip title={this.props.event.labels.DESKTOP_APP_LABEL_LEAVE_MEETING}>
                                <i onClick={() => {
                                    this.props.dispatch({ type: 'reset' });
                                    if (this.props.host === 'host') {
                                        if (this.props.video !== undefined && this.props.video.type === "agora-panel-disscussions") {
                                            this.props.history.push(`/event/${this.props.event.url}/vonage/join-video-meeting/${this.props.video.id}/${this.props.channel}/${this.props.host}/0`)
                                        } else {
                                            this.props.history.push(`/event/${this.props.event.url}/streaming`)
                                        }
                                    } else {
                                        if (this.props.video !== undefined && this.props.video.type === "agora-panel-disscussions") {
                                            this.props.history.push(`/event/${this.props.event.url}/vonage/join-video-meeting/${this.props.video.id}/${this.props.channel}/${this.props.host}/0`)
                                        } else {
                                            this.props.history.push(`/event/${this.props.event.url}/streaming`);
                                        }
                                    }
                                }} className={clsx(classes.customBtn, 'margin-right-19', 'leave-stream')} />
                            </Tooltip>
                        </React.Fragment>
                    </div>}
                </div>
            </div>
        );
    }
}

function mapStateToProps(state) {
    const { event, vonage } = state;
    return {
        event, vonage
    };
}

export default connect(mapStateToProps)(withStyles(styles)(Publisher));
