import * as React from 'react';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import Input from '@/app/forms/Input';
import DropDown from '@/app/forms/DropDown';
import { AttendeeService } from 'services/attendee/attendee-service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class FormWidget extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            initial: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.initial : ''),
            title: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.title : ''),
            first_name: (this.props.editdata !== undefined ? this.props.editdata.first_name : ''),
            last_name: (this.props.editdata !== undefined ? this.props.editdata.last_name : ''),
            email: (this.props.editdata !== undefined ? this.props.editdata.email : ''),
            phone: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.phone : ''),
            company_name: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.company_name : ''),
            department: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.department : ''),
            delegate_number: (this.props.editdata !== undefined ? this.props.editdata.attendee_detail.delegate_number : ''),
            program_id: (this.props.editdata !== undefined ? this.props.editdata.program_id : ''),
            speaker: '1',
            display: true,
            programs: [],

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            // Validation
            first_name_validate: (this.props.editdata !== undefined ? 'success' : ''),
            email_validate: (this.props.editdata !== undefined ? 'success' : ''),
            program_id_validate: (this.props.editdata !== undefined ? 'success' : ''),

            change: false

        }
    }

    componentDidMount() {
        this.programs();
    }

    handleChange = (input, item, type) => e => {
        if (type === 'select') {
            this.setState({
                [input]: e.value,
                [item]: 'success',
                change: true
            })
        } else {

            if (e.target.value === undefined) {
                this.setState({
                    [input]: [],
                    change: true
                })
            } else {
                if (item && type) {
                    const { dispatch } = this.props;
                    const validate = dispatch(AuthAction.formValdiation(type, e.target.value));
                    if (validate.status) {
                        this.setState({
                            [input]: e.target.value,
                            [item]: 'success',
                            change: true
                        })
                    } else {
                        this.setState({
                            [input]: e.target.value,
                            [item]: 'error',
                            change: true
                        })
                    }
                } else {
                    this.setState({
                        [input]: e.target.value,
                        change: true
                    })
                }
            }
        }
    }

    saveData = (e) => {
        e.preventDefault();
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
        if (this.state.program_id_validate === 'error' || this.state.program_id_validate.length === 0) {
            this.setState({
                program_id_validate: 'error'
            })
        }
        if (this.state.first_name_validate === 'success' && this.state.email_validate === 'success' && this.state.program_id_validate === 'success') {
            this.setState({ isLoader: type });
            if (this.props.editdata !== undefined) {
                AttendeeService.update(this.props.editdata.id, this.state)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    'message': response.message,
                                    'success': true,
                                    isLoader: false,
                                    errors: {},
                                    change: false
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
                AttendeeService.create(this.state)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    'message': response.message,
                                    'success': true,
                                    isLoader: false,
                                    errors: {},
                                    change: false
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

    programs = () => {
        AttendeeService.programs()
            .then(
                response => {
                    if (response.success) {
                        this.setState({
                            programs: response.data
                        });
                    }
                },
                error => { }
            );
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
                            <ConfirmationModal update={this.state.change} />
                            {this.state.message &&
                                <AlertMessage
                                    className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                    title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                    content={this.state.message}
                                    icon={this.state.success ? "check" : "info"}
                                />
                            }
                            <h4 className="component-heading">{(this.props.editdata !== undefined ? t('SPEAKER_EDIT') : t('SPEAKER_ADD'))}</h4>
                            <div className="row d-flex">
                                <div className="col-3">
                                    <Input
                                        className={this.state.first_name_validate}
                                        type='text'
                                        label={t('SPEAKER_FIRST_NAME')}
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
                                        label={t('SPEAKER_LAST_NAME')}
                                        name='last_name'
                                        value={this.state.last_name}
                                        onChange={this.handleChange('last_name')}
                                        required={false}
                                    />
                                    {this.state.errors.last_name &&
                                        <p className="error-message">{this.state.errors.last_name}</p>}
                                </div>
                                <div className="col-3">
                                    <Input
                                        className={this.state.email_validate}
                                        type='text'
                                        label={t('SPEAKER_EMAIL')}
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
                                <div className="col-3">
                                    <DropDown
                                        className={this.state.program_id_validate}
                                        label={t('SPEAKER_ASSIGN_PROGRAM')}
                                        name='program_id'
                                        selected={this.state.program_id ? this.state.program_id : ''}
                                        selectedlabel={this.getSelectedLabel(this.state.programs, this.state.program_id ? this.state.program_id : '')}
                                        listitems={this.state.programs}
                                        required={true}
                                        onChange={this.handleChange('program_id', 'program_id_validate', 'select')}
                                    />
                                    {this.state.errors.program_id &&
                                        <p className="error-message">{this.state.errors.program_id}</p>}
                                    {this.state.program_id_validate === 'error' &&
                                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                                </div>
                            </div>
                            <div className="bottom-panel-button">
                                <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save" className="btn">{this.state.isLoader === "save" ?
                                    <span className="spinner-border spinner-border-sm"></span> : (this.props.editdata ? t('G_SAVE') : t('G_SAVE'))}</button>
                                {!this.props.editdata && (
                                    <button disabled={this.state.isLoader ? true : false} onClick={this.saveData} data-type="save-new" className="btn save-new">{this.state.isLoader === "save-new" ?
                                        <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_AND_ADD_ANOTHER')}</button>
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

export default connect(mapStateToProps)(withRouter(FormWidget));