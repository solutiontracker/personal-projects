import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@app/modules/alerts/AlertMessage';
import Loader from '@app/modules/Loader';
import SimpleReactValidator from 'simple-react-validator';
import { store } from 'helpers';
class ResetPasswordRequest extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      email: '',
      submitted: false,
      redirect: '',
      redirect_id: '',
    };

    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);

    this.validator = new SimpleReactValidator({
      element: message => <p className="error-message">{message}</p>,
      messages: {
        required: this.props.event.labels.DESKTOP_APP_LABEL_FIELD_REQUIRED,
        email: this.props.event.labels.GENERAL_VALID_ENTER_EMAIL_MSG,
      },
    })
  }

  componentDidMount() {
    this._isMounted = true;
    this.validator.showMessages();
    this.forceUpdate();
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
      this.props.dispatch(AuthAction.passwordRequest(this.state.email, this.props.event.url));
      localStorage.setItem('reset-password-email', this.state.email);
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
    store.dispatch({ type: "success", "redirect": "", "authentication_id": "", "message": "" });
  }

  render() {
    const { email, submitted } = this.state;
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
              <h3>{this.props.event.name}</h3>
              <form name="form" onSubmit={this.handleSubmit}>
                <div className={'app-field-container' + (submitted && !email ? ' error' : '')}>
                  <input type="text" placeholder={this.props.event.labels.GENERAL_EMAIL} name="email" value={email} onChange={this.handleChange} />
                  {this.validator.message('email', this.state.email, 'required|email')}
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
    )
  }
}

function mapStateToProps(state) {
  const { alert, event } = state;
  return {
    alert, event
  };
}

export default connect(mapStateToProps)(ResetPasswordRequest);