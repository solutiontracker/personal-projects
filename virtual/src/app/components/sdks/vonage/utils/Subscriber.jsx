import React from 'react';
import { OTSubscriber } from 'opentok-react';
import { connect } from 'react-redux';
import { withStyles } from '@material-ui/core/styles';
import Tooltip from '@material-ui/core/Tooltip';
import clsx from 'clsx';
import { service } from 'services/service';

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

class Subscriber extends React.Component {

    _isMounted = false;

    constructor(props) {
        super(props);

        this.state = {
            uid: this.props.uid,
            error: null,
            audio: true,
            video: true,
            audioButton: (this.props.host === "audience" ? true : false),
            meeting: {
                audio: 1,
                video: 1,
                share: 1
            },
            videoSource: 'camera'
        };

        this.subscriberEventHandlers = {
            disconnected: event => {
                console.log('Subscriber video disabled!');
            },
            connected: event => {
                console.log('Stream connected!');
            },
            destroyed: event => {
                console.log('Subscriber video enabled!');
            },
            audioBlocked: event => {
                console.log('Subscriber video enabled!');
            },
            audioUnblocked: event => {
                console.log("Subscriber audio is unblocked.")
            }
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.fetchAttendee();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    fetchAttendee() {
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/attendee/detail/${this.state.uid}`, { channel: this.props.channel })
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            if (response.data.meeting) {
                                this.setState({
                                    meeting: response.data.meeting
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    handleMic = (e) => {
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/handle-mic`, { uid: e.target.getAttribute('uid'), value: e.target.getAttribute('value'), actionBy: 'moderator', channel: this.props.channel })
            .then(
                response => {
                    if (response.success) {
                        document.getElementById("streaming-mic-uid-" + Number(response.uid)).classList.remove('mute-audio', 'unmute-audio');
                        document.getElementById("streaming-mic-uid-" + Number(response.uid)).classList.add(Number(response.value) === 1 ? "mute-audio" : 'unmute-audio');
                        document.getElementById("streaming-mic-uid-" + Number(response.uid)).setAttribute('value', Number(response.value) === 1 ? 0 : 1);
                    }
                },
                error => { }
            );
    }

    handleVid = (e) => {
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/handle-vid`, { uid: e.target.getAttribute('uid'), value: e.target.getAttribute('value'), actionBy: 'moderator', channel: this.props.channel })
            .then(
                response => {
                    if (response.success) {
                        document.getElementById("streaming-vid-uid-" + Number(response.uid)).classList.remove('mute-video', 'unmute-video');
                        document.getElementById("streaming-vid-uid-" + Number(response.uid)).classList.add(Number(response.value) === 1 ? "mute-video" : 'unmute-video');
                        document.getElementById("streaming-vid-uid-" + Number(response.uid)).setAttribute('value', Number(response.value) === 1 ? 0 : 1);
                    }
                },
                error => { }
            );
    }

    handleShare = (e) => {
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/handle-share`, { uid: e.target.getAttribute('uid'), value: e.target.getAttribute('value'), actionBy: 'moderator', channel: this.props.channel })
            .then(
                response => {
                    if (response.success) {
                        document.getElementById("streaming-share-uid-" + Number(response.uid)).classList.remove('mute-screen-shot', 'unmute-screen-shot');
                        document.getElementById("streaming-share-uid-" + Number(response.uid)).classList.add(Number(response.value) === 1 ? "unmute-screen-shot" : 'mute-screen-shot');
                        document.getElementById("streaming-share-uid-" + Number(response.uid)).setAttribute('value', Number(response.value) === 1 ? 0 : 1);
                    }
                },
                error => { }
            );
    }

    onError = (err) => {
        this.setState({ error: `Failed to subscribe: ${err.message}` });
    }

    handleDoubleClick = (stream, uid) => e => {
        e.stopPropagation();
        if (this.props.host && this.props.host === "host") {
            service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/agora/handle-presenter`, { uid: uid, channel: this.props.channel });
        }
    }

    handleSubscriberAudio = (e) => {

        var videos = document.getElementsByTagName("video");

        for (var i = 0, len = videos.length; i < len; i += 1) {
            videos[i].click();
        }

        this.setState({
            audioButton: false
        });
    }

    render() {
        const { classes } = this.props;

        var preferredFrameRate, preferredResolution;

        if (this.props.vonage.otherStreams.length > 8) {
            preferredFrameRate = 7;
            preferredResolution = { width: 320, height: 240 };
        } else if (this.props.vonage.otherStreams.length > 3) {
            preferredFrameRate = 15;
            preferredResolution = { width: 320, height: 240 };
        } else if (this.props.vonage.otherStreams.length > 1) {
            preferredFrameRate = 30;
            preferredResolution = { width: 640, height: 480 };
        } else {
            preferredFrameRate = 30;
            preferredResolution = { width: 1280, height: 720 };
        }

        console.log(this.props.vonage.otherStreams.length)

        return (
            <div className={`stream-player grid-player ${this.state.audioButton ? 'ipad-safari' : ''} ${(Number(this.props.vonage.shareStream) === Number(this.state.uid) ? 'share-stream-thumb' : '')} ${(this.props.main ? 'main-stream-player-alt' : '')}`} id={`stream-player-${this.state.uid}`} onDoubleClick={this.handleDoubleClick(this.props.stream, Number(this.state.uid))}>
                {this.props.vonage.profile && (this.props.name || in_array(process.env.REACT_APP_ENVIRONMENT, ["local", "dev", "stage"]))
                    ? <div className="main-stream-profile">
                        {this.props.name && (
                            <span>{this.props.name}</span>
                        )}
                    </div>
                    : null}
                <OTSubscriber
                    key={this.props.key}
                    session={this.props.session}
                    stream={this.props.stream}
                    properties={{
                        showControls: false,
                        preferredFrameRate: preferredFrameRate,
                        preferredResolution: preferredResolution
                    }}
                    style={{ height: '100%', width: '100%' }}
                    eventHandlers={this.subscriberEventHandlers}
                    onError={this.onError}
                />
                <div className={classes.menuContainer}>
                    {this.props.host && this.props.host === "host" && !in_array(Number(this.state.uid), [Number(this.props.vonage.shareStream)]) ? (
                        <div className={`stream-uid`}>
                            <Tooltip title={(Number(this.state.meeting.audio) === 1 ? this.props.event.labels.DESKTOP_APP_LABEL_MUTE_MEETING : this.props.event.labels.DESKTOP_APP_LABEL_UNMUTE_MEETING)}>
                                <i id={`streaming-mic-uid-${Number(this.state.uid)}`} value={Number(this.state.meeting.audio) === 1 ? 0 : 1} uid={this.state.uid} onClick={this.handleMic} className={clsx(classes.customBtn, 'margin-right-19', (Number(this.state.meeting.audio) === 1 ? 'mute-audio' : 'unmute-audio'))} />
                            </Tooltip>
                            <Tooltip title={(Number(this.state.meeting.video) === 1 ? this.props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_OFF : this.props.event.labels.DESKTOP_APP_LABEL_TURN_CAMERA_ON)}>
                                <i id={`streaming-vid-uid-${Number(this.state.uid)}`} value={Number(this.state.meeting.video) === 1 ? 0 : 1} uid={this.state.uid} onClick={this.handleVid} className={clsx(classes.customBtn, 'margin-right-19', (Number(this.state.meeting.video) === 1 ? 'mute-video' : 'unmute-video'))} />
                            </Tooltip>
                            <Tooltip title={(Number(this.state.meeting.share) === 1 ? this.props.event.labels.DESKTOP_APP_LABEL_HIDE_SHARE_CONTENT : this.props.event.labels.DESKTOP_APP_LABEL_SHARE_CONTENT)}>
                                <i id={`streaming-share-uid-${Number(this.state.uid)}`} value={Number(this.state.meeting.share) === 1 ? 0 : 1} uid={this.state.uid} onClick={this.handleShare} className={clsx(classes.customBtn, 'margin-right-19', (Number(this.state.meeting.share) === 1 ? 'unmute-screen-shot' : 'mute-screen-shot'))} />
                            </Tooltip>
                        </div>
                    ) : null}
                    {this.state.audioButton && (
                        <div className={`stream-uid`}>
                            <Tooltip title={this.props.event.labels.DESKTOP_APP_LABEL_UNMUTE_MEETING}>
                                <i onClick={this.handleSubscriberAudio} className={clsx(classes.customBtn, 'margin-right-19', 'unmute-speaker')} />
                            </Tooltip>
                        </div>
                    )}
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

export default connect(mapStateToProps)(withStyles(styles)(Subscriber));
