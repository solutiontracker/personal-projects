import React from 'react';
import { preloadScript, createSession } from 'opentok-react';
import MyTurnListPublisher from '@app/sdks/vonage/utils/MyTurnListPublisher';
import MyTurnListSubscriber from '@app/sdks/vonage/utils/MyTurnListSubscriber';
import { connect } from 'react-redux';

class MyTurnListMeeting extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            uid: (this.props.userRole === 'audience' ? 0 : this.props.auth.id),
            openSetting: false,
            minimize: true,
            connected: false,
            error: null,
            currentStream: {},
            streams: []
        };

        this.sessionHelper = createSession({
            apiKey: this.props.apiKey,
            sessionId: this.props.sessionId,
            token: this.props.token,
            onStreamsUpdated: streams => {
                this.setState({ streams }, () => {
                    this.props.dispatch({ type: 'otherStreams', payload: streams, userRole: this.props.userRole });
                });
            }
        });
    }

    onError = (err) => {
        this.setState({ error: `Failed to connect: ${err.message}` });
    }

    componentWillUnmount() {
        this.sessionHelper.disconnect();
    }


    render() {
        return (
            <div className={`full-screen-player request-to-speak`}>
                <div className={`w-100 h-100`}>
                    <MyTurnListPublisher
                        session={this.sessionHelper.session}
                        main={(this.props.vonage.localStream && this.state.currentStream && this.props.vonage.localStream.streamId === this.state.currentStream.streamId) || this.props.vonage.otherStreams.length === 0}
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
                    {this.state.streams.map(stream => {
                        return (
                            <MyTurnListSubscriber
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

export default connect(mapStateToProps)(preloadScript(MyTurnListMeeting));
