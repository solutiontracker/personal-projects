import * as React from 'react';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import Input from '@/app/forms/Input';
import DropDown from '@/app/forms/DropDown';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { GeneralService } from 'services/general-service';

class InviteAttendee extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            first_name: (this.props.editdata !== undefined ? this.props.editdata.first_name : ''),
            last_name: (this.props.editdata !== undefined ? this.props.editdata.last_name : ''),
            email: (this.props.editdata !== undefined ? this.props.editdata.email : ''),
            phone: (this.props.editdata !== undefined ? this.props.editdata.phone : ''),
            calling_code: (this.props.editdata !== undefined ? this.props.editdata.calling_code : ''),
            ss_number: "",
            old_ss_number: (this.props.editdata !== undefined ? this.props.editdata.ss_number : ''),
            allow_vote: (this.props.editdata !== undefined ? this.props.editdata.allow_vote : 0),
            ask_to_speak: (this.props.editdata !== undefined ? this.props.editdata.ask_to_speak : 0),
            country_code: 1,
            calling_codes: [],
            event_country_code: '',
            is_ss_number: ((this.props.editdata !== undefined ? this.props.editdata.ss_number : '')) ? true : false,

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            // Validation
            first_name_validate: (this.props.editdata !== undefined ? 'success' : ''),
            email_validate: (this.props.editdata !== undefined ? 'success' : ''),
        }
    }

    componentDidMount() {
        this._isMounted = true;
        this.metadata();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    metadata() {
        this.setState({ preLoader: true });
        GeneralService.metaData()
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                calling_codes: response.data.records.country_codes,
                                event_country_code: response.data.records.event_country_code,
                                calling_code: (!this.state.calling_code ? response.data.records.event_country_code : this.state.calling_code),
                            });
                        }
                    }
                },
                error => { }
            );
    }

    handleChangePhone = (input) => e => {
        this.setState({
            [input]: e.value
        })
    }

    handleCheckbox = (input) => e => {
        this.setState({
            [input]: (Number(this.state[input]) === 1 ? 0 : 1),
            change: true
        })
    }


    handleChange = (input, item, type) => e => {
        const pattern = e.target.getAttribute('pattern');
        if (pattern === '[0-9]*') {
            const validate = (e.target.validity.valid) ? e.target.value : this.state[input];
            this.setState({
                [input]: validate
            })
        } else {
            if (e.target.value === undefined) {
                this.setState({
                    [input]: []
                })
            } else {
                if (item && type) {
                    const { dispatch } = this.props;
                    const validate = dispatch(AuthAction.formValdiation(type, e.target.value));
                    if (validate.status) {
                        this.setState({
                            [input]: e.target.value,
                            [item]: 'success'
                        })
                    } else {
                        this.setState({
                            [input]: e.target.value,
                            [item]: 'error'
                        })
                    }
                } else {
                    this.setState({
                        [input]: e.target.value
                    })
                }
            }
        }
    }

    saveData = (e) => {
        const type = e.target.getAttribute('data-type');
        if (this.state.first_name_validate === 'error' || this.state.first_name_validate.length === 0) {
            this.setState({
                first_name_validate: 'error'
            })
        }
        if (this.state.email_validate === 'error' || this.state.email_validate.length === 0) {
            this.setState({
                email_validate: 'error'
            })
        }
        if (this.state.first_name_validate === 'success' && this.state.email_validate === 'success') {
            this.setState({ isLoader: type });
            if (this.props.editdata !== undefined) {
                service.put(`${process.env.REACT_APP_URL}/attendee/save-invitation/${this.props.editdata.id}`, this.state)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    'message': response.message,
                                    'success': true,
                                    isLoader: false,
                                    errors: {}
                                });
                                this.props.listing(1, true, type);
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
            } else {
                service.post(`${process.env.REACT_APP_URL}/attendee/save-invitation`, this.state)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    'message': response.message,
                                    'success': true,
                                    isLoader: false,
                                    errors: {}
                                });
                                this.props.listing(1, false, type);
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
        }
    }

    render() {
        this.getSelectedLabel = (item, id) => {
            if (item && item.length > 0 && id) {
                let obj = item.find(o => o.id.toString() === id.toString());
                return (obj ? obj.name : '');
            }
        }
        return (
            <Translation>
                {
                    t =>
                        <div className={`hotel-add-item ${this.props.editdata ? 'isGray' : ''}`}>
                            {this.state.message &&
                                <AlertMessage
                                    className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                    title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                    content={this.state.message}
                                    icon={this.state.success ? "check" : "info"}
                                />
                            }
                            <h4 className="component-heading">{(this.props.editdata !== undefined ? t("ATTENDEE_EDIT_TO_INVITE_LIST") : t("ATTENDEE_ADD_TO_GUEST_LIST"))}</h4>
                            <div className="row d-flex">
                                <div className="col-3">
                                    <Input
                                        className={this.state.first_name_validate}
                                        type='text'
                                        label={t('ATTENDEE_FIRST_NAME')}
                                        value={this.state.first_name}
                                        name='first_name'
                                        onChange={this.handleChange('first_name', 'first_name_validate', 'text')}
                                        required={true}
                                    />
                                    {this.state.errors.first_name &&
                                        <p className="error-message">{this.state.errors.first_name}</p>}
                                    {this.state.first_name_validate === 'error' &&
                                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                                </div>
                                <div className="col-3">
                                    <Input
                                        type='text'
                                        label={t('ATTENDEE_LAST_NAME')}
                                        name='last_name'
                                        value={this.state.last_name}
                                        onChange={this.handleChange('last_name')}
                                        required={false}
                                    />
                                    {this.state.errors.last_name &&
                                        <p className="error-message">{this.state.errors.last_name}</p>}
                                </div>
                                <div className="col-3 d-flex no-border-phone">
                                    <DropDown
                                        className=''
                                        label={false}
                                        listitems={this.state.calling_codes}
                                        selected={(this.state.calling_code ? this.state.calling_code : this.state.event_country_code)}
                                        selectedlabel={this.getSelectedLabel(this.state.calling_codes, (this.state.calling_code ? this.state.calling_code : this.state.event_country_code))}
                                        onChange={this.handleChangePhone('calling_code')}
                                        required={false}
                                    />
                                    <Input
                                        type='text'
                                        value={`${this.state.phone}`}
                                        label={t('ATTENDEE_PHONE')}
                                        pattern='[0-9]*'
                                        onChange={this.handleChange('phone')}
                                        required={false}
                                    />
                                    {this.state.errors.phone &&
                                        <p className="error-message">{this.state.errors.phone}</p>}
                                </div>
                                <div className="col-3">
                                    <Input
                                        className={this.state.email_validate}
                                        type='text'
                                        label={t('ATTENDEE_EMAIL')}
                                        name='email'
                                        value={this.state.email}
                                        onChange={this.handleChange('email', 'email_validate', 'email')}
                                        required={true}
                                    />
                                    {this.state.errors.email &&
                                        <p className="error-message">{this.state.errors.email}</p>}
                                    {this.state.email_validate === 'error' &&
                                        <p className="error-message">{t('EE_VALID_EMAIL')}</p>}
                                </div>
                                <div className="col-3 orginnal">
                                    <Input
                                        type='text'
                                        icon={this.state.is_ss_number ? 'mode_edit' : 'clear'}
                                        label={t('ATTENDEE_CPR_NUMBER')}
                                        name='ss_number'
                                        onChange={this.handleChange('ss_number')}
                                        onClick={() => this.setState({ is_ss_number: '' })}
                                        required={false}
                                        disabled={this.state.is_ss_number}
                                        value={this.state.is_ss_number ? 'xxxxxxxxxx' : this.state.ss_number}
                                    />
                                    {this.state.errors.ss_number &&
                                        <p className="error-message">{this.state.errors.ss_number}</p>}
                                </div>
                                <br />
                            </div>
                            <div className="row d-flex">
                                <div className="col-3 d-flex">
                                    <p>{t('ATTENDEE_ALLOW_VOTE')} </p>
                                    <label className="custom-checkbox-toggle float-right"><input onClick={this.handleCheckbox('allow_vote')}
                                        defaultChecked={Number(this.state.allow_vote) === 1 ? true : false}
                                        type="checkbox"
                                        name="" /><span></span></label>
                                </div>
                                <div className="col-3 d-flex">
                                    <p>{t('ATTENDEE_ASK_TO_SPEAK')} </p>
                                    <label className="custom-checkbox-toggle float-right"><input onClick={this.handleCheckbox('ask_to_speak')}
                                        defaultChecked={Number(this.state.ask_to_speak) === 1 ? true : false}
                                        type="checkbox"
                                        name="" /><span></span></label>
                                </div>
                            </div>
                            <div className="bottom-panel-button">
                                <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save" className="btn">{this.state.isLoader === "save" ?
                                    <span className="spinner-border spinner-border-sm"></span> : (this.props.editdata ? t('G_SAVE') : t("G_SAVE"))}</button>
                                {!this.props.editdata && (
                                    <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save-new" className="btn save-new">{this.state.isLoader === "save-new" ?
                                        <span className="spinner-border spinner-border-sm"></span> : t("G_SAVE_AND_ADD_ANOTHER")}</button>
                                )}
                                <button className="btn btn-cancel" onClick={this.props.datacancel}>{t('G_CANCEL')}</button>
                            </div>
                        </div>
                }
            </Translation>
        )
    }
}

function mapStateToProps(state) {
    const { alert } = state;
    return {
        alert
    };
}

export default connect(mapStateToProps)(InviteAttendee);