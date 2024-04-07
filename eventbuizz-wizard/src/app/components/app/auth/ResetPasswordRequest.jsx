import * as React from 'react';
import Img from 'react-image';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { initReactI18next } from "react-i18next";
import i18n from "i18next";
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css

const _languages = [i18n.t('LNG_ENGLISH'), i18n.t('LNG_DANISH'), 'Norwegian', 'German', 'Lithuanian', 'Finnish', 'Swedish', 'Dutch', 'Flemish'];
const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];
const _lang = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl', 'be'];

class ResetPasswordRequest extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            submitted: false,
            redirect: ''
        };

        this.handleChange = this.handleChange.bind(this);

        this.handleSubmit = this.handleSubmit.bind(this);
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

    handleSubmit(e) {
        e.preventDefault();
        this.setState({ submitted: true });
        const { email } = this.state;
        const { dispatch } = this.props;
        if (email) {
            confirmAlert({
                customUI: ({ onClose }) => {
                    return (
                        <Translation>
                            {
                                t =>
                                    <div className='app-main-popup'>
                                        <div className="app-header">
                                            <h4>{t('EE_RESET_PASSWORD')}</h4>
                                        </div>
                                        <div className="app-body">
                                            <p>{t('EE_RESET_PASSWORD_REQUEST_ALERT')}</p>
                                        </div>
                                        <div className="app-footer">
                                            <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                            <button className="btn btn-success"
                                                onClick={() => {
                                                    onClose();
                                                    dispatch(AuthAction.passwordRequest(email));
                                                    localStorage.setItem('reset-password-email', email);
                                                }}
                                            >
                                                {t('EE_RESET')}
                                            </button>
                                        </div>
                                    </div>
                            }
                        </Translation>
                    );
                }
            });
        }
    }

    switchLanguage = (id) => {
        localStorage.setItem('interface_language_id', id);
        i18n.use(initReactI18next).init({ lng: _lang[id - 1] });
        document.getElementById("language-switch").innerHTML = (localStorage.getItem('interface_language_id') ? _languages[localStorage.getItem('interface_language_id') - 1] : _languages[0]);
    }

    render() {
        const { email, submitted } = this.state;
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
                                            <h2 style={{ margin: '0 0 5px 0' }}>{t('FORGOT_PASSWORD_TITLE')}</h2>
                                            <p>{t('FORGOT_PASSWORD_MESSAGE')}</p>
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
                                                    <div
                                                        className={'form-row-box' + (submitted && !email ? ' has-error' : '')}>
                                                        <input type="text" name="email" value={email}
                                                            onChange={this.handleChange} />
                                                        <label className="title">{t('ENTER_EMAIL')}</label>
                                                    </div>
                                                    {submitted && !email &&
                                                        <div className="error-message">{t('EE_EMAIL_REQUIRED')}</div>
                                                    }
                                                    <div style={{ paddingTop: '0px' }}
                                                        className="form-row-box button-panel text-center">
                                                        <button
                                                            className="btn btn-primary">{this.props.alert.type === "request" ?
                                                                <span
                                                                    className="spinner-border spinner-border-sm"></span> : t('G_SEND')}</button>
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

export default connect(mapStateToProps)(ResetPasswordRequest);
