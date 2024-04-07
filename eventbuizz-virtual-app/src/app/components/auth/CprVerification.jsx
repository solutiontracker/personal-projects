import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import AlertMessage from '@app/modules/alerts/AlertMessage';
import Loader from '@app/modules/Loader';
import { store } from 'helpers';
import SimpleReactValidator from 'simple-react-validator';
import { AuthAction } from 'actions/auth/auth-action';

class Verification extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            event: this.props.event,
            cpr: '',
            pid: (this.props.match.params.pid !== undefined ? this.props.match.params.pid : ''),
            preLoader: false,
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);

        this.validator = new SimpleReactValidator({
            element: message => <p className="error-message">{message}</p>,
            messages: {
                required: this.state.event.labels.DESKTOP_APP_LABEL_FIELD_REQUIRED,
            },
        })
    }

    static getDerivedStateFromProps(props, state) {
        if (props.alert.redirect !== state.redirect && props.alert.redirect !== undefined) {
            return {
                redirect: props.alert.redirect,
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.redirect !== this.state.redirect && this.state.redirect === "cpr-login") {
            this.props.history.push(`/event/${this.props.event.url}/cpr-login`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "login") {
            this.props.history.push(`/event/${this.props.event.url}/login`);
        } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "dashboard") {
            this.props.history.push(`/event/${this.props.event.url}/lobby`);
        }
    }

    handleChange(e) {
        if (e.target.value !== '') {
            e.target.classList.add('ieHack')
        } else {
            e.target.classList.remove('ieHack')
        }
        const { name, value } = e.target;
        this.setState({ [name]: value });
    }

    handleSubmit(e) {
        e.preventDefault();
        const { dispatch } = this.props;
        if (this.validator.allValid()) {
            dispatch(AuthAction.cprVerification(this.state.cpr, this.state.pid, this.state.event.url));
        } else {
            this.validator.showMessages();
            this.forceUpdate();
        }
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
                            <h3 style={{ marginBottom: '7px' }}>{this.props.event.name}</h3>
                            <p style={{ marginBottom: '33px' }}>{this.props.event.labels.DESKTOP_VERIFICATION_SCREEN_HEADING}</p>
                            <form onSubmit={this.handleSubmit}>
                                <div className={'app-field-container'}>
                                    <input type="text" placeholder={this.state.event.labels.GENERAL_NEM_ID_PLACEHOLDER} name="cpr" value={this.state.cpr} onChange={this.handleChange} />
                                    {this.validator.message('cpr', this.state.cpr, 'required')}
                                </div>
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
