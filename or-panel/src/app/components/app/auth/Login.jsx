import * as React from 'react';
import Img from 'react-image';
import { Link } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { confirmAlert } from 'react-confirm-alert'; // Import
import { initReactI18next } from "react-i18next";
import i18n from "i18next";
import Loader from '@/app/forms/Loader';


const _languages = [i18n.t('LNG_ENGLISH'), i18n.t('LNG_DANISH'), 'Norwegian', 'German', 'Lithuanian', 'Finnish', 'Swedish', 'Dutch', 'Flemish'];
const languages = [{ id: 1, name: "English" }, { id: 2, name: "Danish" }];
const _lang = ['en', 'da', 'no', 'de', 'lt', 'fi', 'se', 'nl', 'be'];

class Login extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            password: '',
            submitted: false,
            checkbox: false,
            showpassword: false,
            isloaded: false,
            redirect: '',

            token: '',
            preLoader: false,
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        let token = this.props.match.params.token;
        if (token !== undefined) {
            this.setState({
                token: token
            }, () => {
                this.autologin(token);
            });

        }
    }

    autologin(token) {
        const { dispatch } = this.props;
        localStorage.removeItem('eventBuizz');
        dispatch(AuthAction.autoLogin(token));
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
        } else if (this.props.alert !== prevProps.alert) {
            if (this.props.alert.logged) {
                confirmAlert({
                    customUI: ({ onClose }) => {
                        const { email, password } = this.state;
                        const { dispatch } = this.props;
                        return (
                            <Translation>
                                {
                                    t =>
                                        <div className='app-main-popup'>
                                            <div className="app-header">
                                                <h4>{t('G_WARNING')}</h4>
                                            </div>
                                            <div className="app-body">
                                                <p dangerouslySetInnerHTML={{ __html: this.props.alert.message }}></p>
                                            </div>
                                            <div className="app-footer">
                                                <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                                <button className="btn btn-success"
                                                    onClick={() => {
                                                        onClose();
                                                        dispatch(AuthAction.login(email, password, true));
                                                    }}
                                                >
                                                    {t('G_DISCONNECT')}
                                                </button>
                                            </div>
                                        </div>
                                }
                            </Translation>
                        );
                    }
                });
            } else {
                let data = localStorage.getItem('eventBuizz');
                if (data && data !== undefined && data !== null) {
                    this.props.history.push('/');
                }
            }
        }
    }

    handleChangeInput = input => e => {
        e.preventDefault();
        this.setState({
            [input]: this.state[input] === false ? true : false
        })
    };

    handleChange(e) {
        const { name, value } = e.target;
        this.setState({ [name]: value });
    }

    handleSubmit(e) {
        e.preventDefault();
        this.setState({
            isloaded: true
        })
        this.setState({ submitted: true });
        const { email, password } = this.state;
        const { dispatch } = this.props;
        if (email && password) {
            dispatch(AuthAction.login(email, password));
        }
        this.setState({
            isloaded: false
        })
    }

    switchLanguage = (id) => {
        localStorage.setItem('interface_language_id', id);
        i18n.use(initReactI18next).init({ lng: _lang[id - 1] });
        document.getElementById("language-switch").innerHTML = (localStorage.getItem('interface_language_id') ? _languages[localStorage.getItem('interface_language_id') - 1] : _languages[0]);
    }

    render() {
        const { email, password, submitted } = this.state;
        return (
            <Translation>
                {
                    t => <div className="container-box">
                        <div className="row">
                            {this.props.alert.type === "request" && this.state.token ? (
                                <Loader fixed="true" />
                            ) : (
                                    <React.Fragment>
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
                                                    {this.props.alert.message &&
                                                        <AlertMessage
                                                            className={this.props.alert.class}
                                                            title={`${this.props.alert.success ? '' : t('EE_OCCURRED')}`}
                                                            content={this.props.alert.message}
                                                            icon={`${this.props.alert.success ? 'check' : 'info'}`}
                                                        />
                                                    }
                                                    {this.props.alert.message && <p></p>}
                                                    <h2>{t('SIGN_IN')}</h2>
                                                    <p></p>
                                                    <form name="form" onSubmit={this.handleSubmit}>
                                                        <div className="form-area-signup">
                                                            <div className={'form-row-box' + (submitted && !email ? ' has-error' : '')}>
                                                                <input type="text" name="email" value={email} onChange={this.handleChange} className={`${this.state.email ? 'ieHack' : ''}`} />
                                                                <label className="title">{t('ENTER_EMAIL')}</label>
                                                            </div>
                                                            {submitted && !email &&
                                                                <div className="error-message">{t('EE_EMAIL_REQUIRED')}</div>
                                                            }
                                                            <div className={'form-row-box' + (submitted && !password ? ' has-error' : '')}>
                                                                <span onClick={this.handleChangeInput('showpassword')} className="icon-eye"><Img src={this.state.showpassword ? 'images/icon-eye.svg' : 'images/close-eye.svg'} className="illustration" /></span>
                                                                <input type={this.state.showpassword ? 'text' : 'password'} name="password" value={password} onChange={this.handleChange} className={`${this.state.password ? 'ieHack' : ''}`} />
                                                                <label className="title">{t('PASSWORD')}</label>
                                                            </div>
                                                            {submitted && !password &&
                                                                <div className="error-message">{t('EE_PASSWORD_REQUIRED')}</div>
                                                            }
                                                            <div className="login-others clearfix">
                                                                <label onClick={this.handleChangeInput('checkbox')}><i className={`${this.state.checkbox && 'check_box'} material-icons`}>{this.state.checkbox ? 'check_box' : 'check_box_outline_blank'}</i>{t('REMEMBER_ME')}</label>
                                                                <Link to="/reset-password-request" onClick={() => this.props.dispatch({ type: 'alert-clear', redirect: '/reset-password-request' })}>{t('FORGOT_PASSWORD')}</Link>
                                                            </div>
                                                            <div className="form-row-box button-panel">
                                                                <button className="btn btn-primary">{this.props.alert.type === "request" ? <span className="spinner-border spinner-border-sm"></span> : t('SIGN_IN')}</button>
                                                                {/* <div className="other-link">{t('DONT_HAVE_AN_ACCOUNT')}  <a style={{ cursor: 'pointer' }} href="">{t('CREATE_ONE_NOW')}</a>.</div> */}
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </React.Fragment>
                                )}
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

export default connect(mapStateToProps)(Login);
