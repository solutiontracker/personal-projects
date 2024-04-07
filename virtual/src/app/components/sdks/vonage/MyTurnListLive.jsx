import React from 'react'
import MyTurnListMeeting from '@app/sdks/vonage/utils/MyTurnListMeeting'
import '@app/sdks/agora/assets/css/style.css'
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';

class MyTurnListLive extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            preLoader: true,
            channel: this.props.match.params.channel,
            role: "participant",
            token: "",
            apiKey: "",
            sessionId: "",
            video: {},
        };
    }

    componentDidMount() {
        this.props.dispatch({ type: 'reset' });
        this.loadData();
    }

    loadData() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/opentok/create/token`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                token: response.data.token,
                                apiKey: response.data.apiKey,
                                sessionId: response.data.sessionId,
                                video: response.data.video,
                                preLoader: false,
                            });
                        }
                    }
                },
                error => { }
            );
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    render() {
        return (
            <React.Fragment>
                {this.state.preLoader && <Loader fixed="true" />}
                <React.Fragment>
                    {this.state.token && this.state.apiKey && (
                        <React.Fragment>
                            <MyTurnListMeeting history={this.props.history} event={this.props.event} auth={this.props.auth.data.user} userRole={this.state.role} name="name" channelName={`${this.state.channel}`} token={this.state.token} apiKey={this.state.apiKey} sessionId={this.state.sessionId} video={this.state.video} />
                        </React.Fragment>
                    )}
                </React.Fragment>
            </React.Fragment>
        )
    }
}

function mapStateToProps(state) {
    const { event, auth } = state;
    return {
        event, auth
    };
}

export default connect(mapStateToProps)(MyTurnListLive);