import React from 'react'
import Meeting from '@app/sdks/vonage/utils/Meeting';
import '@app/sdks/agora/assets/css/style.css';
import '@app/sdks/vonage/assets/css/style.css';
import { connect } from 'react-redux';
import { service } from 'services/service';
import socketIOClient from "socket.io-client";
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import ReactHtmlParser from 'react-html-parser';
import { Link } from 'react-router-dom';

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

const in_array = require("in_array");

class Index extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            preLoader: true,
            video_id: this.props.match.params.video_id,
            channel: this.props.match.params.channel,
            joined: this.props.match.params.joined,
            role: this.props.match.params.role,
            attendee_id: (this.props.auth.data && this.props.auth.data.user !== undefined ? this.props.auth.data.user.id : 0),
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
                            }, () => {
                                if (this.state.video && in_array(this.state.video.type, ["agora-panel-disscussions"])) {
                                    //socket
                                    socket.off(`event-buizz:request_to_speak_action_${this.props.event.id}_${this.state.video.agenda_id}`);
                                    socket.on(`event-buizz:request_to_speak_action_${this.props.event.id}_${this.state.video.agenda_id}`, this.getData);
                                }
                            });
                        }
                    }
                },
                error => { }
            );
    }

    getData = data => {
        var json = JSON.parse(data.raw_data);
        if (Number(json.current_attendee) === Number(this.props.auth.data.user.id)) {
            if (json.current_action === "stop") {
                window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/vonage/join-video-meeting/${this.state.video_id}/${this.state.channel}/audience/1`;
            } else if (json.current_action === "live") {
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
                                            }}
                                        >
                                            {this.props.event.labels.DESKTOP_APP_LABEL_CANCEL || 'Cancel'}
                                        </button>
                                        <button
                                            style={{ backgroundColor: this.props.event.settings.primary_color }}
                                            className="btn btn-success"
                                            onClick={() => {
                                                onClose();
                                                window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/vonage/join-video-meeting/${this.state.video_id}/${this.state.channel}/participant/1`;
                                            }}
                                        >
                                            {this.props.event.labels.DESKTOP_APP_LABEL_GO_LIVE || 'Live'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        );
                    }
                });
            } else if (json.current_action === "reload") {
                window.location.href = `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/agora/join-video-meeting/${this.state.video_id}/${this.state.channel}/participant/1`;
            }
        }
    };

    static getDerivedStateFromProps(props, state) {
        if (props.match.params.joined !== undefined && props.match.params.joined !== state.joined) {
            return {
                joined: props.match.params.joined
            };
        } else if (props.match.params.role !== undefined && props.match.params.role !== state.role) {
            return {
                role: props.match.params.role
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentWillUnmount() {
        this._isMounted = false;
        if (this.state.video && in_array(this.state.video.type, ["agora-panel-disscussions"])) {
            socket.off(`event-buizz:request_to_speak_action_${this.props.event.id}_${this.state.video.agenda_id}`);
        }
    }

    render() {
        return (
            this.state.video && ((this.state.video.type !== "agora-rooms" && Number(this.state.joined) === 1) || (this.state.video.type === "agora-rooms")) ? (
                <React.Fragment>
                    {this.state.token && this.state.apiKey && (
                        <React.Fragment>
                            <Meeting history={this.props.history} event={this.props.event} auth={this.props.auth.data.user} userRole={this.state.role} name="name" channel={`${this.state.channel}`} token={this.state.token} apiKey={this.state.apiKey} video={this.state.video} video_id={this.state.video_id} sessionId={`${this.state.sessionId}`} vonage={`${this.props.vonage}`} />
                        </React.Fragment>
                    )}
                </React.Fragment>
            ) : (
                <div className={`${this.state.userRole !== 'audience' ? 'opentok-join-meeting' : 'opentok-join-meeting-audience'} w-100 h-100 d-flex align-items-center justify-content-center`}>
                    <Link style={{ backgroundColor: this.props.event.settings.primary_color, color: '#fff', minWidth: '150px' }} to={`/event/${this.props.event.url}/vonage/join-video-meeting/${this.state.video_id}/${this.state.channel}/${this.state.role}/1`} className="btn">{this.props.event.labels.DESKTOP_APP_LABEL_JOIN}</Link>
                </div>
            )
        )
    }
}

function mapStateToProps(state) {
    const { event, auth, vonage } = state;
    return {
        event, auth, vonage
    };
}

export default connect(mapStateToProps)(Index);