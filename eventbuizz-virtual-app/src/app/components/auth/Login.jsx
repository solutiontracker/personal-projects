import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@app/modules/alerts/AlertMessage';
import Loader from '@app/modules/Loader';
import SimpleReactValidator from 'simple-react-validator';
import { store } from 'helpers';
import { service } from 'services/service';
import '@app/auth/assets/css/style.css';
class Login extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      event: this.props.event,
      email: '',
      password: '',
      submitted: false,
      checkbox: false,
      showpassword: false,
      redirect: '',
      redirect_id: '',
    };

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);

    this.validator = new SimpleReactValidator({
      element: message => <p className="error-message">{message}</p>,
      messages: {
        required: this.state.event.labels.DESKTOP_APP_LABEL_FIELD_REQUIRED,
        email: this.state.event.labels.GENERAL_VALID_ENTER_EMAIL_MSG,
        min: this.state.event.labels.DESKTOP_APP_LABEL_PASSWORD_MIN_LENGTH
      },
    })
  }

  componentDidMount() {
    this._isMounted = true;
    this.loadEvent();
  }

  loadEvent() {
    service.get(`${process.env.REACT_APP_URL}/${this.state.event.url}`)
      .then(
        response => {
          if (response.success) {
            this.setState({
              event: response.event
            });
          }
        },
        error => { }
      );
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
      this.props.history.push(`/event/${this.state.event.url}/choose-provider/${this.state.redirect_id}`);
    } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "verification") {
      this.props.history.push(`/event/${this.state.event.url}/verification/${this.state.redirect_id}`);
    } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "login") {
      this.props.history.push(`/event/${this.state.event.url}/login`);
    } else if (prevState.redirect !== this.state.redirect && this.state.redirect === "dashboard") {
      this.props.history.push(`/event/${this.state.event.url}/lobby`);
    }
  }

  handleChangeInput = input => e => {
    e.preventDefault();
    this.setState({
      [input]: this.state[input] === false ? true : false
    })
  };

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
    if (this.validator.allValid()) {
      this.setState({ submitted: true });
      this.props.dispatch(AuthAction.login(this.state.email, this.state.password, this.state.event.url));
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
    const { email, password, submitted } = this.state;
    return (
      <React.Fragment>
        {this.props.alert.type === "request" ? (
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
              <h3>{this.state.event.name}</h3>
              <form name="form" onSubmit={this.handleSubmit}>
                <div className="app-field-container">
                  {Number(this.state.event.attendee_settings.cpr) === 1 && (
                    <React.Fragment>
                      <Link to={`/event/${this.state.event.url}/cpr-login`} style={{ backgroundColor: this.state.event.settings.primary_color }} className="btn btn-default btn-submit">{this.props.alert.type === "request" ? <span className="spinner-border spinner-border-sm"></span> : this.state.event.labels.GENERAL_NEM_ID_LOGIN}</Link>
                      {Number(this.state.event.attendee_settings.email_enable) === 1 && (
                        <span className="omb_spanOr">{this.state.event.labels.GENERAL_OR_LABEL || 'OR'}</span>
                      )}
                    </React.Fragment>
                  )}
                </div>
                {Number(this.state.event.attendee_settings.email_enable) === 1 && (
                  <React.Fragment>
                    <div className={'app-field-container' + (submitted && !email ? ' error' : '')}>
                      <input type="text" placeholder={this.state.event.labels.GENERAL_EMAIL} name="email" value={email} onChange={this.handleChange} />
                      {this.validator.message('email', this.state.email, 'required|email')}
                    </div>
                    {Number(this.state.event.attendee_settings.hide_password) === 0 && Number(this.state.event.attendee_settings.registration_password) === 0 && Number(this.state.event.attendee_settings.authentication) === 0 && (
                      <div className={'app-field-container' + (submitted && !password ? ' error' : '')}>
                        <input type='password' placeholder={this.state.event.labels.GENERAL_PASSWORD} name="password" value={password} onChange={this.handleChange} />
                        {this.validator.message('password', this.state.password, 'required|min:6')}
                      </div>
                    )}
                    <div className="app-field-container">
                      <label className="label-checkbox" onClick={this.handleChangeInput('checkbox')}><i className={`${this.state.checkbox && 'check_box'} material-icons`}>{this.state.checkbox ? 'check_box' : 'check_box_outline_blank'}</i>{this.state.event.labels.EVENTSITE_REMEMBER_ME}</label>
                    </div>
                    {Number(this.state.event.attendee_settings.default_password_label) === 1 && this.state.event.attendee_settings.default_password && Number(this.state.event.attendee_settings.authentication) === 0 && (
                      <div className="app-field-container">
                        <label>{this.state.event.labels.EVENTSITE_DEFAULT_PASSWORD} {this.state.event.attendee_settings.default_password}</label>
                      </div>
                    )}
                    {Number(this.state.event.attendee_settings.hide_password) === 0 && Number(this.state.event.attendee_settings.forgot_link) === 0 && Number(this.state.event.attendee_settings.authentication) === 0 && (
                      <div className="app-field-container">
                        <Link to={`/event/${this.state.event.url}/reset-password-request`} className="app-forget-pass">{this.state.event.labels.EVENTSITE_FORGOT_PASSWORD}</Link>
                      </div>
                    )}
                    <button onClick={this.handleClick} style={{ backgroundColor: this.state.event.settings.primary_color }} className="btn btn-default btn-submit">{this.props.alert.type === "request" ? <span className="spinner-border spinner-border-sm"></span> : this.state.event.labels.GENERAL_SIGN_IN}</button>
                  </React.Fragment>
                )}
              </form>
            </div>
          )}
      </React.Fragment>
    )
  }
}

function mapStateToProps(state) {
  const { alert, event } = state;
  return {
    alert, event
  };
}

export default connect(mapStateToProps)(Login);