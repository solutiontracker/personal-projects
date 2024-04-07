import React, { Component } from 'react';
import { connect } from 'react-redux';
import { service } from 'services/service';
import Loader from '@app/modules/Loader';
import { confirmAlert } from 'react-confirm-alert';
import 'react-confirm-alert/src/react-confirm-alert.css';
import socketIOClient from "socket.io-client";
import ReactHtmlParser from 'react-html-parser';
import { GeneralAction } from 'actions/general-action';
import { store } from 'helpers';
import { withRouter } from "react-router-dom";

const socket = socketIOClient(process.env.REACT_APP_SOCKET_SERVER);

class KinesisStreamTest extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            streamingProvider: this.props.event.settings.streaming_service === "vonage" ? "vonage" : "agora",
            video: (this.props.video !== undefined && this.props.video ? this.props.video : ""),
            streamingUrl: '',
            event_id: this.props.event.id,
            agenda_id: (this.props.match.params.request_to_speak_program_id || ''),
            program_id: (this.props.match.params.program_id || ''),
            action: 'close-channel-connection',
            attendee_id: (this.props.auth && this.props.auth.data ? this.props.auth.data.user.id : ''),
            streamingLoader: true,
            preLoader: false,
            popup: false
        };
    }

    static getDerivedStateFromProps(props, state) {
        if (props.auth && props.auth.data && props.auth.data.user.id !== state.attendee_id && props.auth.data.user.id !== undefined) {
            socket.off(`event-buizz:request_to_speak_action_${state.event_id}_${state.attendee_id}`);
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
    };

    loadAttendee() {
        this._isMounted = true;
        this.setState({ streamingLoader: true });
        service.post(`${process.env.REACT_APP_URL}/${this.props.event.url}/myturnlist/speaking-attendee`, this.state)
            .then(
                response => {
                    if (response.success && response.data.attendee) {
                        if (this._isMounted) {
                            this.setState({
                                streamingUrl: `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/${this.state.streamingProvider}/myturnlist/live/Eventbuizz-${this.state.event_id}-${this.state.agenda_id}`,
                                streamingLoader: false,
                            }, () => {
                                store.dispatch(GeneralAction.stream(response.data));
                            })
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
        socket.off(`event-buizz:request_to_speak_action_${this.state.event_id}_${this.state.attendee_id}`);
        socket.on(`event-buizz:request_to_speak_action_${this.state.event_id}_${this.state.attendee_id}`, data => {
            var json = JSON.parse(data.raw_data);
            if (json.current_action === "live") {
                this.setState({
                    popup: true
                }, () => {
                    store.dispatch(GeneralAction.stream({}));
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
                                                            streamingUrl: `${process.env.REACT_APP_BASE_URL}/event/${this.props.event.url}/${this.state.streamingProvider}/myturnlist/live/Eventbuizz-${this.state.event_id}-${json.agenda_id}`,
                                                            agenda_id: json.agenda_id,
                                                            projector_mode: (json.event_settings !== undefined ? json.event_settings.projector_mode : this.state.projector_mode),
                                                            streamingLoader: false,
                                                        }, () => {
                                                            //take it to live screen
                                                            if (this.state.video.current_action !== undefined) {
                                                                this.props.history.push(`/event/${this.props.event.url}/streaming-live/${this.state.video.agenda_id}/${json.agenda_id}/${this.state.video.current_video}`);
                                                            } else {
                                                                this.props.history.push(`/event/${this.props.event.url}/streaming-live/${json.agenda_id}/${json.agenda_id}`);
                                                            }

                                                            store.dispatch(GeneralAction.stream(json));
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
            } else if (json.current_action === "stop") {
                this.setState({
                    popup: false
                }, () => {
                    store.dispatch(GeneralAction.stream({}));
                })
            }
        });
    }

    componentWillUnmount() {
        this._isMounted = false;
        socket.off(`event-buizz:request_to_speak_action_${this.state.event_id}_${this.state.attendee_id}`);
        window.removeEventListener("beforeunload", this.beforeUnload, false)
    }

    closeStreaming(e) {
        store.dispatch(GeneralAction.stream({}));
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
                                    {this.state.streamingUrl && (
                                        <iframe title="side-iframe" width="100%" src={this.state.streamingUrl} frameBorder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowFullScreen></iframe>
                                    )}
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
    const { event, auth, stream, video } = state;
    return {
        event, auth, stream, video
    };
}

export default connect(mapStateToProps)(withRouter(KinesisStreamTest));