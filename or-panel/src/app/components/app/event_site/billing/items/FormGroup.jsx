import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import 'sass/billing.scss';
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class FormGroup extends Component {
    constructor(props) {
        super(props);
        this.state = {
            group_name: (this.props.editData && this.props.editData.detail.group_name ? this.props.editData.detail.group_name : ''),
            type: 'group',
            parent_id: '0',
            group_required: (this.props.editData && this.props.editData.group_required === "yes" ? 1 : 0),
            group_is_expanded: (this.props.editData && this.props.editData.group_is_expanded === "yes" ? 1 : (!this.props.editData ? 1 : 0)),
            group_type: (this.props.editData && this.props.editData.group_type ? this.props.editData.group_type : "single"),
            is_free: (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? 0 : 1),

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            change: false,

            //validations
            validate_group_name: (this.props.editData ? "success" : "")
        }
    }

    handleChange = (input, item, type) => e => {
        const { dispatch } = this.props;
        const validate = dispatch(AuthAction.formValdiation(type, e.target.value));
        if (validate.status) {
            this.setState({
                [input]: e.target.value,
                [item]: 'success',
                change: true
            });
        } else {
            this.setState({
                [input]: e.target.value,
                [item]: 'error',
                change: true
            })
        }
    };

    updateFlag = input => e => {
        if (input === "group_type") {
            this.setState({
                [input]: this.state[input] === "single" ? "multiple" : "single",
                change: true
            });
        } else {
            this.setState({
                [input]: this.state[input] === 1 ? 0 : 1,
                change: true
            });
        }
    };

    save = e => {
        if (this.state.validate_group_name === 'success') {
            this.setState({ isLoader: true });
            if (this.props.editData) {
                service.put(`${process.env.REACT_APP_URL}/eventsite/billing/items/edit/${this.props.editData.id}`, this.state)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    message: response.message,
                                    success: true,
                                    isLoader: false,
                                    change: false,
                                    errors: {}
                                });
                                this.props.listing(true);
                            } else {
                                this.setState({
                                    message: response.message,
                                    success: false,
                                    isLoader: false,
                                    errors: response.errors
                                });
                            }
                        },
                        error => { }
                    );
            } else {
                service.put(`${process.env.REACT_APP_URL}/eventsite/billing/items/create`, this.state)
                    .then(
                        response => {
                            if (response.success) {
                                this.setState({
                                    message: response.message,
                                    success: true,
                                    isLoader: false,
                                    change: false,
                                    errors: {}
                                });
                                this.props.listing(false);
                            } else {
                                this.setState({
                                    message: response.message,
                                    success: false,
                                    isLoader: false,
                                    errors: response.errors
                                });
                            }
                        },
                        error => { }
                    );
            }
        } else {
            if (this.state.validate_group_name !== 'success') {
                this.setState({
                    validate_group_name: 'error',
                })
            }
        }
    }

    render() {
        const { datamode, onCancel } = this.props;
        return (
            <Translation>
                {
                    t =>
                        <React.Fragment>
                            <div className="new-header">
                                <h1 className="section-title">Group</h1>
                            </div>
                            <div className={`option-wrapper billing-group-form ${this.props.editData ? 'isGray' : ''}`}>
                                <ConfirmationModal update={this.state.change} />
                                {this.state.message &&
                                    <AlertMessage
                                        className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                        title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                        content={this.state.message}
                                        icon={this.state.success ? "check" : "info"}
                                    />
                                }
                                <h3>{datamode}</h3>
                                <p>{t('BILLING_ITEMS_ADD_BILLING_ITEM_LABEL')}</p>
                                <div className="row">
                                    <div className="col-6">
                                        <Input
                                            className={this.state.validate_group_name}
                                            type='text'
                                            label={t("BILLING_ITEMS_GROUP_NAME_LABEL")}
                                            name='name'
                                            value={this.state.group_name}
                                            onChange={this.handleChange('group_name', 'validate_group_name', 'text')}
                                            required={true}
                                        />
                                        {this.state.errors.group_name &&
                                            <p className="error-message">{this.state.errors.group_name}</p>}
                                        {this.state.validate_group_name === 'error' &&
                                            <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                                        <h3>{t("BILLING_ITEMS_SETTING_FOR_GROUPS_ON_REG_FORM")}</h3>
                                        <div className="checkbox-row">
                                            <p>{t("BILLING_ITEMS_PARTICIPANT_SELECT_MULTI_ITEMS_IN_GROUP")}</p>
                                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('group_type')}
                                                defaultChecked={(this.state.group_type === "multiple" ? true : false)} type="checkbox" /><span></span></label>
                                        </div>
                                        <div className="checkbox-row">
                                            <p>{t("BILLING_ITEMS_GROUP_REQUIRED_LABEL")}</p>
                                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('group_required')}
                                                defaultChecked={this.state.group_required} type="checkbox" /><span></span></label>
                                        </div>
                                        <div className="checkbox-row">
                                            <p>{t("BILLING_ITEMS_GROUP_EXPAND_LABEL")}</p>
                                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('group_is_expanded')}
                                                defaultChecked={this.state.group_is_expanded} type="checkbox" /><span></span></label>
                                        </div>
                                    </div>
                                </div>
                                <div className="bottom-panel-button">
                                    <button disabled={this.state.isLoader ? true : false} className="btn" onClick={this.save.bind(this)}>{this.state.isLoader ?
                                        <span className="spinner-border spinner-border-sm"></span> : (this.props.editData ? t('G_SAVE') : t('G_SAVE'))}</button>
                                    <button className="btn btn-cancel" onClick={() => onCancel('folder')}>{t('G_CANCEL')}</button>
                                </div>
                            </div>
                        </React.Fragment>
                }
            </Translation>

        )
    }
}

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(withRouter(FormGroup));