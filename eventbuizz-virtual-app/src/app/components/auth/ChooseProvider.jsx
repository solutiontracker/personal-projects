import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@app/modules/alerts/AlertMessage';
import Loader from '@app/modules/Loader';
import { service } from 'services/service';
import { store } from 'helpers';

class ChooseProvider extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            screen: 'choose-provider',
            provider: 'email',
            redirect: '',
            redirect_id: '',
            email: '',
            phone: '',
            authentication_id: (this.props.match.params.id !== undefined ? this.props.match.params.id : ''),

            preLoader: false,
        };

        this.handleRadioChange = this.handleRadioChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
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
        if (prevState.redirect !== this.state.redirect && this.state.redirect === "choose-provider") {
            this.props.history.push(`/event/${this.props.event.url}/choose-provider/${this.state.redirect_id}`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "verification") {
            this.props.history.push(`/event/${this.props.event.url}/verification/${this.state.redirect_id}`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "login") {
            this.props.history.push(`/event/${this.props.event.url}/login`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "dashboard") {
            this.props.history.push(`/event/${this.props.event.url}/lobby`);
        }
    }

    handleRadioChange(event) {
        this.setState({
            provider: event.target.value
        });
    }

    handleSubmit(e) {
        e.preventDefault();
        this.props.dispatch(AuthAction.verification(this.state.screen, this.state.provider, null, this.props.event.url, this.state.authentication_id));
    }

    componentDidMount() {
        this.loadAttendee();
    }

    loadAttendee() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/${this.props.event.url}/auth/verification/${this.state.authentication_id}`)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                preLoader: false,
                                email: response.data.email,
                                phone: response.data.phone,
                            });
                        }
                    }
                },
                error => { }
            );
    }

    componentWillUnmount() {
        this._isMounted = false;
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
                            <h3 style={{marginBottom: '7px'}}>{this.props.event.name}</h3>
                            <p style={{marginBottom: '33px'}}>{this.props.event.labels.DESKTOP_CHOOSE_SERVICE_PROVIDER_HEADING}</p>
                            <form name="form" onSubmit={this.handleSubmit}>
                                <div className="form-check">
                                    <input className="form-check-input" type="checkbox" name="sms" value="sms" onChange={this.handleRadioChange} checked={this.state.provider === "sms"} />
                                    <label>
                                        {this.state.phone}
                                    </label>
                                </div>
                                <div className="form-check">
                                    <input className="form-check-input" type="checkbox" name="email" value="email" onChange={this.handleRadioChange} checked={this.state.provider === "email"} />
                                    <label>
                                        {this.state.email}
                                    </label>
                                </div>
                                <br></br>
                                <div className="app-field-container">
                                    <Link to={`/event/${this.props.event.url}/login`} className="app-forget-pass">{`${this.props.event.labels.DESKTOP_APP_LABEL_GO_BACK_TO} ${this.props.event.labels.DESKTOP_APP_LABEL_LOGIN}`}</Link>
                                </div>
                                <button onClick={this.handleClick} style={{ backgroundColor: this.props.event.settings.primary_color }} className="btn btn-default btn-submit">{this.props.alert.type === "request" ? <span className="spinner-border spinner-border-sm"></span> : this.props.event.labels.GENERAL_SUBMIT}</button>
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

export default connect(mapStateToProps)(ChooseProvider);
