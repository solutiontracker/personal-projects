import * as React from 'react';
import Img from 'react-image';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { initReactI18next } from "react-i18next";
import i18n from "i18next";

const _languages = [i18n.t('LNG_ENGLISH'), i18n.t('LNG_DANISH'), 'Norwegian', 'German', 'Lithuanian', 'Finnish', 'Swedish', 'Dutch', 'Flemish'];
const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];
const _lang = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl', 'be'];
class ResetPassword extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            email: localStorage.getItem('reset-password-email'),
            password: '',
            password_confirmation: '',
            token: '',
            submitted: false,
            redirect: ''
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }


    static getDerivedStateFromProps(props, state) {
        if (props.alert.redirect !== state.redirect && props.alert.redirect !== undefined) {
            return {
                redirect: props.alert.redirect
            };
        }
        // Return null to indicate no change to state.
        return null;
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.redirect !== this.state.redirect) {
            this.props.history.push(this.state.redirect);
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

        this.setState({ submitted: true });
        const { email, password, password_confirmation, token } = this.state;
        const { dispatch } = this.props;
        if (email && password && password_confirmation && token) {
            dispatch(AuthAction.passwordReset(email, password, password_confirmation, token));
        }
    }

    switchLanguage = (id) => {
        localStorage.setItem('interface_language_id', id);
        i18n.use(initReactI18next).init({ lng: _lang[id - 1] });
        document.getElementById("language-switch").innerHTML = (localStorage.getItem('interface_language_id') ? _languages[localStorage.getItem('interface_language_id') - 1] : _languages[0]);
    }

    render() {
        const { email, password, password_confirmation, token, submitted } = this.state;
        return (
            <Translation>
                {
                    t =>
                        <div className="container-box">
                            <div className="row">
                                <div className="col-6">
                                    <div className="left-signup">
                                        <Img src={require("img/logos.svg")} width="182px" className="logos" />
                                        <div className="text-block">
                                            <h4>{t('WELCOME_TO_PLUG_AND_PLAY')}</h4>
                                            <p>{t('MINIMIZE_YOUR_EFFORTS_AND_MAXIMIZE_THE_RESULTS')}</p>
                                            <ul>
                                                <li>{t('CREATE_YOUR_OWN_EVENT_IN_A_FEW_CLICKS')}</li>
                                                <li>{t('SORT_OUT_EVENT_REGISTRATION_IN_NO_TIME')}</li>
                                                <li>{t('GET_YOUR_OWN_CUSTOMIZED_EVENT_APP')}</li>
                                                <li>{t(('FEEL_SAFE_WITH_OUR_STEP_BY_STEP_NAVIGATION'))}</li>
                                            </ul>
                                        </div>
                                        <Img src={require("img/illustration.svg")} className="illustration" />
                                    </div>
                                </div>
                                <div className="col-6">
                                    <div className="right-section-blank">
                                        <ul className="main-navigation">
                                            <li>
                                                <a href="#!"><i className="icons"><Img src={require("img/icon_globe.svg")} /></i><span id="language-switch">{(localStorage.getItem('interface_language_id') ? _languages[localStorage.getItem('interface_language_id') - 1] : _languages[0])}</span></a>
                                                <ul>
                                                    {languages.map((value, key) => {
                                                        return (
                                                            <li key={key} onClick={() => this.switchLanguage(value.id)}>
                                                                <a>{value.name}</a>
                                                            </li>
                                                        );
                                                    })}
                                                </ul>
                                            </li>
                                        </ul>
                                        <div className="right-formarea">
                                            <h2>{t('RESET_PASSWORD')}</h2>
                                            {this.props.alert.message &&
                                                <AlertMessage
                                                    className={this.props.alert.class}
                                                    title={`${this.props.alert.success ? '' : t('EE_OCCURRED')}`}
                                                    content={this.props.alert.message}
                                                    icon={`${this.props.alert.success ? 'check' : 'info'}`}
                                                />
                                            }
                                            <form name="form" onSubmit={this.handleSubmit}>
                                                <div className="form-area-signup">
                                                    <input type="hidden" name="email" value={email}
                                                        onChange={this.handleChange} />
                                                    {submitted && !email &&
                                                        <div className="error-message">{t('EE_EMAIL_REQUIRED')}</div>
                                                    }
                                                    <div
                                                        className={'form-row-box' + (submitted && !password ? ' has-error' : '')}>
                                                        <input type="password"  name="password"
                                                            value={password} onChange={this.handleChange} />
                                                        <label className="title">{t('RESET_ENTER_NEW_PASSWORD')}</label>
                                                    </div>
                                                    {submitted && !password &&
                                                        <div className="error-message">{t('EE_PASSWORD_REQUIRED')}</div>
                                                    }
                                                    <div
                                                        className={'form-row-box' + (submitted && !password_confirmation ? ' has-error' : '')}>
                                                        <input type="password" name="password_confirmation" value={password_confirmation}
                                                            onChange={this.handleChange} />
                                                        <label className="title">{t('RESET_CONFIRM_NEW_PASSWORD')}</label>
                                                    </div>
                                                    {submitted && !password_confirmation &&
                                                        <div className="error-message">{t('EE_CONFIRM_PASSWORD_REQUIRED')}</div>
                                                    }
                                                    <div
                                                        className={'form-row-box' + (submitted && !token ? ' has-error' : '')}>
                                                        <input type="text" name="token" value={token}
                                                            onChange={this.handleChange} />
                                                        <label className="title">{t('RESET_ENTER_VERIFICATION_CODE')}</label>
                                                    </div>
                                                    {submitted && !token &&
                                                        <div className="error-message">{t('EE_CODE')}</div>
                                                    }
                                                    <div className="form-row-box button-panel">
                                                        <button
                                                            className="btn btn-primary">{this.props.alert.type === "request" ?
                                                                <span
                                                                    className="spinner-border spinner-border-sm"></span> : t('G_SUBMIT')}</button>
                                                        <div className="other-link"><span>{t('EE_ALREADY_ACCOUNT')}</span> <a style={{ cursor: 'pointer', color: '#ffffff', fontWeight: '600', textDecoration: 'underline' }} onClick={() => this.props.dispatch({ type: 'alert-clear', redirect: '/login' })}>{t('SIGN_IN')}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                }
            </Translation>
        );
    }
}

function mapStateToProps(state) {
    const { alert } = state;
    return {
        alert
    };
}

export default connect(mapStateToProps)(ResetPassword);
