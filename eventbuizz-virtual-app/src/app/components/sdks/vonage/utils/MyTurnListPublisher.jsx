import React from 'react';
import { OTPublisher } from 'opentok-react';
import { connect } from 'react-redux';

class Publisher extends React.Component {

    _isMounted = false;

    constructor(props) {
        super(props);

        this.state = {
            uid: this.props.uid,
            error: null,
            audio: true,
            video: true,
            videoSource: 'camera',
        };

        this.publisherEventHandlers = {
            streamCreated: event => {
                this.props.dispatch({ type: 'addStream', payload: event.stream });
                console.log('Publisher stream created!');
            },
            streamDestroyed: event => {
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
        (this.state.videoSource !== 'camera') ? this.setState({ videoSource: 'camera' }) : this.setState({ videoSource: 'screen' })
    }

    onError = (err) => {
        this.setState({ error: `Failed to publish: ${err.message}` });
    }

    componentDidMount() {
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

    render() {
        return (
            <div className={`attendee-view`} id={`stream-player-${this.state.uid}`}>
                <OTPublisher
                    session={this.props.session}
                    properties={{
                        name: this.props.uid + " | " + this.props.name,
                        publishAudio: this.state.audio,
                        publishVideo: this.state.video,
                        videoSource: this.state.videoSource === 'screen' ? 'screen' : undefined,
                        resolution: '1280x720',
                        height: '100%',
                        width: '100%'
                    }}
                    eventHandlers={this.publisherEventHandlers}
                    style={{ height: '100%', width: '100%' }}
                    onError={this.onError}
                />
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

export default connect(mapStateToProps)(Publisher);
