import React, { Component } from 'react';
import { connect } from 'react-redux';
import AlertMessage from '@app/modules/alerts/AlertMessage';
import Loader from '@app/modules/Loader';
import { service } from 'services/service';
import { store } from 'helpers';
import { AuthAction } from 'actions/auth/auth-action';

class CprLogin extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            event: this.props.event,
            parameters: '',
            baseUrl: '',
            iframeUrl: '',
            redirect: '',
            redirect_id: '',

            preLoader: false,
        };
    }

    static getDerivedStateFromProps(props, state) {
        if (props.alert.redirect !== state.redirect && props.alert.redirect !== undefined) {
            return {
                redirect: props.alert.redirect,
                redirect_id: props.alert.redirect_id
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.redirect !== this.state.redirect && this.state.redirect === "cpr-verification") {
            this.props.history.push(`/event/${this.props.event.url}/cpr-verification/${this.state.redirect_id}`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "login") {
            this.props.history.push(`/event/${this.props.event.url}/login`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "dashboard") {
            this.props.history.push(`/event/${this.props.event.url}/lobby`);
        }
    }

    componentDidMount() {
        this.loadIframe();
    }

    loadIframe() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/${this.props.event.url}/auth/cpr-login`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                preLoader: false,
                                iframeUrl: response.data.iframeUrl,
                                baseUrl: response.data.baseUrl,
                                parameters: response.data.parameters,
                            }, () => {
                                if (window.addEventListener) {
                                    window.addEventListener("message", this.onNemIDMessage);
                                } else if (window.attachEvent) {
                                    window.attachEvent("onmessage", this.onNemIDMessage);
                                }
                            });
                        }
                    }
                },
                error => { }
            );
    }

    onNemIDMessage = e => {
        const { dispatch } = this.props;
        var win = document.getElementById("nemid_iframe").contentWindow, postMessage = {}, message;
        try {
            message = JSON.parse(e.data);
            console.log(e.data);
            if (message.command === "SendParameters") {
                postMessage.command = "parameters";
                postMessage.content = `{${this.state.parameters}}`;
                win.postMessage(JSON.stringify(postMessage), this.state.baseUrl);
            }
            if (message.command === "changeResponseAndSubmit") {
                dispatch(AuthAction.nemIDAuthentication(message.content, this.state.event.url));
            }
        } catch (e) { }
    }

    componentWillUnmount() {
        this._isMounted = false;
        if (window.addEventListener) {
            window.removeEventListener("message", this.onNemIDMessage)
        } else if (window.attachEvent) {
            window.detachEvent("onmessage", this.onNemIDMessage);
        }
        store.dispatch({ type: "success", "redirect": "", "authentication_id": "", "message": this.props.alert.message, "success": this.props.alert.success });
    }

    render() {
        return (
            <React.Fragment>
                {this.state.preLoader ? (
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
                            <h3 style={{ marginBottom: '7px' }}>{this.props.event.name}</h3>
                            {this.state.iframeUrl && (
                                <React.Fragment>
                                    <iframe id="nemid_iframe" title="NemID" allowFullScreen scrolling="no" frameBorder="0" style={{ 'width': '320px', 'height': '460px', 'border': '0' }} src={this.state.iframeUrl}></iframe>
                                </React.Fragment>
                            )}
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

export default connect(mapStateToProps)(CprLogin);
