import * as React from "react";
import { NavLink } from 'react-router-dom';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";

class RegistrationInvitationSetting extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            attendee_reg_verification: 1,
            validate_attendee_invite: 1,

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,
            preLoader: false,
        };

        this.config = {
            htmlRemoveTags: ['script'],
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.setState({ preLoader: true });
        service.get(`${process.env.REACT_APP_URL}/attendee/settings`)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            if (this._isMounted) {
                                this.setState({
                                    attendee_reg_verification: response.data.settings.attendee_reg_verification,
                                    validate_attendee_invite: response.data.settings.validate_attendee_invite,
                                    preLoader: false
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    save = e => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        this.setState({ isLoader: type });
        service.put(`${process.env.REACT_APP_URL}/attendee/settings`, this.state)
            .then(
                response => {
                    if (response.success) {
                        this.setState({
                            'message': response.message,
                            'success': true,
                            isLoader: false,
                            errors: {}
                        });
                        if (type === "save-next") this.props.history.push('/event/invitation/report/app-invitation');
                    } else {
                        this.setState({
                            'message': response.message,
                            'success': false,
                            'isLoader': false,
                            'errors': response.errors
                        });
                    }
                },
                error => { }
            );
    }

    updateFlag = input => e => {
        this.setState({
            [input]: this.state[input] === 1 ? 0 : 1
        });
    };

    render() {
        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content third-step">
                            {this.state.message &&
                                <AlertMessage
                                    className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                    title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                    content={this.state.message}
                                    icon={this.state.success ? "check" : "info"}
                                />
                            }
                            {this.state.preLoader && <Loader />}
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <div style={{height: '100%'}}>
                                        <h1 className="section-title">{t("ATTENDEE_REGISTRATION_INVITATION_SETTINGS")}</h1>
                                        <div className="checkbox-row">
                                            <p>{t("ATTENDEE_REGISTRATION_VERIFICATION_SETTING_LABEL")}</p>
                                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('attendee_reg_verification')}
                                                defaultChecked={this.state.attendee_reg_verification} type="checkbox" /><span></span></label>
                                        </div>
                                        <div className="checkbox-row">
                                            <p>{t("ATTENDEE_VALIDATE_ATTENDEE_INVITE_SETTING_LABEL")}</p>
                                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('validate_attendee_invite')}
                                                defaultChecked={this.state.validate_attendee_invite} type="checkbox" /><span></span></label>
                                        </div>
                                    </div>
                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                                    <i className='material-icons'>remove_red_eye</i>
                                          {t('G_PREVIEW')}
                                        </NavLink>
                                        <NavLink className="btn btn-prev-step" to={`/event/invitation/report/registration-reminder-log`}><span className="material-icons">
                                            keyboard_backspace</span></NavLink>
                                        <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.save}>{this.state.isLoader === "save" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                                        </button>
                                        <button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.save}>{this.state.isLoader === "save-next" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                                        </button>
                                    </div>
                                </React.Fragment>
                            )}
                        </div>
                }
            </Translation>
        )
    }
}

export default RegistrationInvitationSetting;