import React from 'react';
import { preloadScript, createSession } from 'opentok-react';
import Publisher from '@app/sdks/vonage/utils/Publisher';
import ScreenShare from '@app/sdks/vonage/utils/ScreenShare';
import Subscriber from '@app/sdks/vonage/utils/Subscriber';
import socketIOClient from "socket.io-client";
import { connect } from 'react-redux';

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");
class Meeting extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            uid: (this.props.userRole === 'audience' ? 0 : this.props.auth.id),
            openSetting: false,
            minimize: true,
            connected: false,
            error: null,
            currentStream: {},
            shareStream: null,
            publisherShareStream: null,
            streams: []
        };

        this.sessionHelper = createSession({
            apiKey: this.props.apiKey,
            sessionId: this.props.sessionId,
            token: this.props.token,
            onStreamsUpdated: streams => {
                console.log("streams updated");

                if (this.props.vonage.currentStream) {
                    const currentStream = streams.filter(stream => Number(stream.name.split('|').shift()) === Number(this.props.vonage.currentStream.name.split('|').shift()));
                    const otherStreams = streams.filter(stream => Number(stream.name.split('|').shift()) !== Number(this.props.vonage.currentStream.name.split('|').shift()));
                    const allStreams = currentStream.concat(otherStreams);

                    this.setState({ streams: allStreams }, () => {
                        this.props.dispatch({ type: 'otherStreams', payload: allStreams, userRole: this.props.userRole });
                    });
                } else {
                    this.setState({ streams }, () => {
                        this.props.dispatch({ type: 'otherStreams', payload: streams, userRole: this.props.userRole });
                    });
                }

                //Filter share stream
                const shareStream = streams.find(stream => stream.videoType === 'screen');
                if (shareStream !== undefined) {
                    this.props.dispatch({ type: 'shareStream', payload: Number(shareStream.name.split('|').shift()) });
                    this.props.dispatch({ type: 'publisherShareStream', payload: null });
                } else {
                    this.props.dispatch({ type: 'shareStream', payload: null });
                }
            }
        });
    }

    onError = (err) => {
        this.setState({ error: `Failed to connect: ${err.message}` });
    }

    componentDidMount() {
        this.socket();
    }

    componentWillUnmount() {
        this.props.dispatch({ type: 'reset' });
        socket.off(`event-buizz:event-streaming-common-actions-${this.props.event.id}`);
        socket.off(`event-buizz:event-streaming-actions-${this.props.event.id}`);
        this.sessionHelper.disconnect();
    }

    componentDidUpdate() {
        if (this.props.vonage.currentStream && this.props.vonage.currentStream.streamId !== this.state.currentStream.streamId) {

            const currentStream = this.props.vonage.otherStreams.filter(stream => Number(stream.name.split('|').shift()) === Number(this.props.vonage.currentStream.name.split('|').shift()));
            const otherStreams = this.props.vonage.otherStreams.filter(stream => Number(stream.name.split('|').shift()) !== Number(this.props.vonage.currentStream.name.split('|').shift()));
            const allStreams = currentStream.concat(otherStreams);

            this.setState({
                currentStream: this.props.vonage.currentStream,
                streams: allStreams
            }, () => {
                this.props.dispatch({ type: 'otherStreams', payload: allStreams, userRole: this.props.userRole });
            })
        } else if (this.props.vonage.shareStream && this.props.vonage.shareStream !== this.state.shareStream) {
            this.setState({ shareStream: this.props.vonage.shareStream })
        } else if (this.props.vonage.publisherShareStream && this.props.vonage.publisherShareStream !== this.state.publisherShareStream) {
            this.setState({ publisherShareStream: this.props.vonage.publisherShareStream })
        }
    }

    socket() {
        socket.off(`event-buizz:event-streaming-common-actions-${this.props.event.id}`);
        socket.on(`event-buizz:event-streaming-common-actions-${this.props.event.id}`, data => {
            var json = JSON.parse(data.data_info);
            if (in_array(json.control, ["leave-meeting", "end-meeting"])) {
                if (this.props.video !== undefined && this.props.video.type === "agora-panel-disscussions") {
                    if ((json.attendee_id && Number(json.attendee_id) === Number(this.props.auth.id) && json.control === "leave-meeting") || json.control === "end-meeting") {
                        window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/vonage/join-video-meeting/${this.props.video.id}/${this.props.channel}/${Number(this.props.video.attachedAttendees) > 0 ? 'participant' : 'audience'}/0`;
                    }
                }
            }
        });

        socket.off(`event-buizz:event-streaming-actions-${this.props.event.id}`);
        socket.on(`event-buizz:event-streaming-actions-${this.props.event.id}`, data => {
            var json = JSON.parse(data.data_info);
            if (json.control === "presenter") {
                this.props.dispatch({ type: 'currentStreamById', payload: json.uid });
            }
        });
    }

    render() {
        
        return (
            <div className={`${this.props.video && in_array(this.props.video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? 'full-screen-player' : 'meeting'} ${in_array(this.props.video.type, ['agora-panel-disscussions']) ? 'panel-disscussions' : ''}`} style={{ backgroundImage: 'url(' + (this.props.video && in_array(this.props.video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) && this.props.video.thumbnail ? this.props.video.thumbnail : '') + ')' }}>
                <div className={`${this.props.video && in_array(this.props.video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? '' : 'current-view'}`}>
                    <div className={`${this.props.video && !in_array(this.props.video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? 'flex-container' : ''}`}>
                        <div className={`${this.props.video && !in_array(this.props.video.type, ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar']) ? (this.props.vonage.publisherShareStream || this.props.vonage.shareStream) ? 'share-stream-screen' : 'app-grid-layout-' + (this.props.userRole !== 'audience' ? this.props.vonage.otherStreams.length : (this.props.vonage.otherStreams.length - 1)) + ' z-index-5 custom-grid-layout' : ''} ${(this.props.vonage.publisherShareStream || this.props.vonage.shareStream) && this.state.minimize ? 'minimize' : ''} w-100 h-100 ${!in_array(this.props.video.type, ['agora-rooms']) ? 'iframe-wrapped' : ''}`}>
                            {this.props.userRole !== 'audience' && (
                                <Publisher
                                    session={this.sessionHelper.session}
                                    stream={this.props.vonage.localStream}
                                    main={(!this.props.vonage.publisherShareStream && this.props.vonage.localStream && this.state.currentStream && this.props.vonage.localStream.streamId === this.state.currentStream.streamId) || this.props.vonage.otherStreams.length === 0}
                                    showUid={true}
                                    host={this.props.userRole}
                                    event={this.props.event}
                                    agora={this.props.agora}
                                    auth={this.props.auth}
                                    video={this.props.video}
                                    channel={this.props.channel}
                                    history={this.props.history}
                                    uid={this.state.uid}
                                    name={this.props.auth && this.props.auth.first_name !== undefined ? this.props.auth.first_name + " " + this.props.auth.last_name : ''}
                                />
                            )}
                            {this.state.streams.map(stream => {
                                return (
                                    <Subscriber
                                        key={stream.id}
                                        session={this.sessionHelper.session}
                                        stream={stream}
                                        main={(this.state.currentStream && stream.id === this.state.currentStream.streamId)}
                                        showUid={true}
                                        host={this.props.userRole}
                                        event={this.props.event}
                                        agora={this.props.agora}
                                        auth={this.props.auth}
                                        video={this.props.video}
                                        channel={this.props.channel}
                                        history={this.props.history}
                                        uid={stream.name.split('|').shift()}
                                        name={stream.name.split('|').pop()}
                                    />
                                );
                            })}
                            {this.props.vonage.publisherShareStream && (
                                <ScreenShare
                                    session={this.sessionHelper.session}
                                    stream={this.props.vonage.localStream}
                                    main={false}
                                    showUid={true}
                                    host={this.props.userRole}
                                    event={this.props.event}
                                    agora={this.props.agora}
                                    auth={this.props.auth}
                                    video={this.props.video}
                                    channel={this.props.channel}
                                    history={this.props.history}
                                    uid={this.props.vonage.publisherShareStream}
                                    name={''}
                                />
                            )}
                        </div>
                    </div>
                    {(this.props.vonage.publisherShareStream || this.props.vonage.shareStream) && (
                        <span onClick={() => this.setState({ minimize: !this.state.minimize })} className={`op-minimize-screen material-icons ${this.state.minimize ? 'minimize' : ''}`}>double_arrow</span>
                    )}
                </div>
            </div>
        );
    }
}

function mapStateToProps(state) {
    const { agora, event, vonage } = state;
    return {
        agora, event, vonage
    };
}

export default connect(mapStateToProps)(preloadScript(Meeting));
