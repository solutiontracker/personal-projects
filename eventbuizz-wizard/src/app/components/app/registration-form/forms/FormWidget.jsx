import * as React from 'react';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import 'react-phone-input-2/lib/style.css'
import { Translation } from "react-i18next";

class FormWidget extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            name: '',
            display: true,

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            // Valdiation
            name_validate: ''
        }
    }

    componentDidMount() {
        this._isMounted = true;
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    handleChange = (input, item, type) => e => {
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

    saveData = (e) => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        if (this.state.name_validate === 'error' || this.state.name_validate.length === 0) {
            this.setState({
                name_validate: 'error'
            })
        }
        if (this.state.name_validate === 'success') {
            this.setState({ isLoader: type });
            service.post(`${process.env.REACT_APP_URL}/attendee/attendee-type`, this.state)
                .then(
                    response => {
                        if (this._isMounted) {
                            if (response.success) {
                                this.setState({
                                    message: response.message,
                                    success: true,
                                    isLoader: false,
                                    errors: {}
                                });
                                this.props.listing('attendee_type_head', type);
                            } else {
                                this.setState({
                                    message: response.message,
                                    success: false,
                                    isLoader: false,
                                    errors: response.errors
                                });
                            }
                        }
                    },
                    error => { }
                );
        }
    }

    render() {
        return (
            <Translation>
                {
                    t =>
                        <div className="hotel-add-item">
                            {this.state.message &&
                                <AlertMessage
                                    className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                    title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                    content={this.state.message}
                                    icon={this.state.success ? "check" : "info"}
                                />
                            }
                            <h4 className="component-heading">{t('RF_ADD_ATTENDEE_TYPE')}</h4>
                            <div className="row d-flex">
                                <div className="col-12">
                                    <Input
                                        className={this.state.name_validate}
                                        type='text'
                                        label={t('RF_ADD_TYPE')}
                                        name='name'
                                        value={this.state.name}
                                        onChange={this.handleChange('name', 'name_validate', 'text')}
                                        required={true}
                                    />
                                    {this.state.errors.name && <p className="error-message">{this.state.errors.name}</p>}
                                    {this.state.name_validate === 'error' &&
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

export default connect(mapStateToProps)(FormWidget);