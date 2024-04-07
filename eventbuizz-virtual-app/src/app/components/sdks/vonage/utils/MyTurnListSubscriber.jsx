import React from 'react';
import { OTSubscriber } from 'opentok-react';
import { connect } from 'react-redux';

class Subscriber extends React.Component {

    _isMounted = false;

    constructor(props) {
        super(props);

        this.state = {
            uid: this.props.uid,
            error: null,
            audio: true,
            video: true,
            videoSource: 'camera'
        };

        this.subscriberEventHandlers = {
            disconnected: event => {
                console.log('Subscriber video disabled!');
            },
            connected: event => {
                if (event.target.stream && event.target.stream.videoType === 'screen') {
                    this.props.dispatch({ type: 'shareStream', payload: Number(event.target.stream.name.split('|').shift()) });
                }
                console.log('Stream connected!');
            },
            destroyed: event => {
                console.log('Subscriber video enabled!');
            }
        };
    }

    componentDidMount() {
        this._isMounted = true;
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    onError = (err) => {
        this.setState({ error: `Failed to subscribe: ${err.message}` });
    }

    render() {
        return (
            <div className={`moderator-view`} id={`stream-player-${this.state.uid}`}>
                <OTSubscriber
                    key={this.props.key}
                    session={this.props.session}
                    stream={this.props.stream}
                    properties={{
                        showControls: false,
                        preferredFrameRate: 30,
                        preferredResolution: { width: 1280, height: 720 },
                        subscribeToAudio: this.state.audio,
                        subscribeToVideo: this.state.video
                    }}
                    style={{ height: '100%', width: '100%' }}
                    eventHandlers={this.subscriberEventHandlers}
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

export default connect(mapStateToProps)(Subscriber);
