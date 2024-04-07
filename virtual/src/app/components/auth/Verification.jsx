import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@app/modules/alerts/AlertMessage';
import Loader from '@app/modules/Loader';
import { service } from 'services/service';
import ReactCodeInput from 'react-verification-code-input';
import Countdown from "react-countdown";
import { AuthService } from 'services/auth/auth-service';
import { store } from 'helpers';
class Verification extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            screen: 'verification',
            provider: 'email',
            redirect: '',
            redirect_id: '',
            code: '',
            ms: '',
            type: '',
            authentication_id: (this.props.match.params.id !== undefined ? this.props.match.params.id : ''),

            preLoader: false,
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.resend = this.resend.bind(this);
    }

    static getDerivedStateFromProps(props, state) {
        if (props.alert.redirect !== state.redirect && props.alert.redirect !== undefined) {
            return {
                redirect: props.alert.redirect,
                redirect_id: props.alert.redirect_id
            };
        } else if (props.alert.ms !== state.ms && props.alert.ms !== undefined) {
            return {
                ms: props.alert.ms
            };
        } else if (props.alert.type !== state.type && props.alert.type !== undefined) {
            return {
                type: props.alert.type
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.redirect !== this.state.redirect && this.state.redirect === "choose-provider") {
            this.props.history.push(`/event/${this.props.event.url}/choose-provider/${this.state.redirect_id}`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "verification") {
            this.props.history.push(`/event/${this.props.event.url}/verification/${this.state.redirect_id}`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "login") {
            this.props.history.push(`/event/${this.props.event.url}/login`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "dashboard") {
            this.props.history.push(`/event/${this.props.event.url}/lobby`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "reset-password") {
            this.props.history.push(`/event/${this.props.event.url}/reset-password`);
        }
    }

    handleChange(code) {
        this.setState({
            code: code
        });
    }

    resend(e) {
        e.preventDefault();
        this.setState({ preLoader: true });
        AuthService.verification("resend", this.state.provider, null, this.props.event.url, this.state.authentication_id)
            .then(
                response => {
                    if (response.success) {
                        this.setState({
                            preLoader: false,
                            ms: (response.data !== undefined ? response.data.ms : 0),
                        });
                        this.props.dispatch({ type: "success", message: response.message });
                    } else {
                        this.props.dispatch({ type: "error", message: response.message });
                    }
                },
                error => {
                }
            );
    }

    handleSubmit(e) {
        e.preventDefault();
        this.props.dispatch(AuthAction.verification(this.state.screen, this.state.provider, this.state.code, this.props.event.url, this.state.authentication_id));
    }

    componentDidMount() {
        this.loadAttendee();
    }

    loadAttendee() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/${this.props.event.url}/auth/verification/${this.state.authentication_id}?screen=${this.state.screen}`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                preLoader: false,
                                ms: (response.data !== undefined ? response.data.ms : 0),
                            });
                        }
                    }
                },
                error => { }
            );
    }

    componentWillUnmount() {
        this._isMounted = false;
        store.dispatch({ type: "success", "redirect": "", "authentication_id": "", "message": "" });
    }

    render() {
        return (
            <React.Fragment>
                {this.state.preLoader || this.props.alert.type === "request" ? (
                    <Loader />
                ) : (
                        <div className="app-login-area">
                            {this.props.alert.message &&
                                <AlertMessage
                                    className={this.props.alert.class}
                                    title={`${this.props.alert.success ? '' : 'Sorry, an error has occurred.'}`}
                                    content={this.props.alert.message}
                                    icon={`${this.props.alert.success ? 'check' : 'info'}`}
                                />
                            }
                            <h3 style={{marginBottom: '7px'}}>{this.props.event.labels.EVENTSITE_AUTHENTICATION_CODE_REQUIRED}</h3>
                            <p style={{marginBottom: '33px'}}>{this.props.event.labels.EVENTSITE_AUTHENTICATION_EMAIL_CODE_SEND_MSG}</p>
                            <form onSubmit={this.handleSubmit}>
                                <ReactCodeInput type='number' fields={6} onChange={this.handleChange} fieldHeight={40} fieldWidth={50} />
                                <Countdown
                                    date={Date.now() + Number(this.state.ms)}
                                    renderer={({ hours, minutes, seconds, completed }) => {
                                        if (completed) {
                                            return (
                                                <span>
                                                    {Number(minutes) < 4 && (
                                                        <a style={{ color: this.props.event.settings.primary_color, cursor: 'pointer' }} onClick={this.resend}> {this.props.event.labels.GENERAL_RESEND || 'Resend'}</a>
                                                    )}
                                                </span>
                                            );
                                        } else {
                                            return (
                                                <div style={{paddingTop: '8px' }}>
                                                    {this.props.event.labels.EVENTSITE_TIME_LEFT} = {minutes}:{seconds}
                                                    {Number(minutes) < 4 && (
                                                        <a style={{ color: this.props.event.settings.primary_color, cursor: 'pointer' }} onClick={this.resend}> {this.props.event.labels.GENERAL_RESEND || 'Resend'}</a>
                                                    )}
                                                </div>
                                            );
                                        }
                                    }}
                                />
                                <br /><br />
                                <div className="app-field-container">
                                    <Link to={`/event/${this.props.event.url}/login`} className="app-forget-pass">{`${this.props.event.labels.DESKTOP_APP_LABEL_GO_BACK_TO} ${this.props.event.labels.DESKTOP_APP_LABEL_LOGIN}`}</Link>
                                </div>
                                <button onClick={this.handleClick} style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-default btn-submit">{this.props.event.labels.GENERAL_SIGN_IN}</button>
                                <br /><br />
                            </form>
                        </div>
                    )}
            </React.Fragment>
        );
    }
}

function mapStateToProps(state) {
    const { alert, event } = state;
    return {
        alert, event
    };
}

export default connect(mapStateToProps)(Verification);
