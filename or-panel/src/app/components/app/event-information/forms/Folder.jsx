import React, { Component } from 'react';
import { ReactSVG } from 'react-svg';
import Input from '@/app/forms/Input';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import { InformationService } from 'services/information/information-service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class Folder extends Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            type: 'folder',
            parent_id: '0',
            validate: '',
            cms: this.props.cms,

            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            change: false
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
            })
        } else {
            this.setState({
                [input]: e.target.value,
                [item]: 'error',
                change: true
            })
        }
    };

    componentDidMount() {
        if (this.props.editData) {
            this.setState({
                name: this.props.editData.detail.name,
                validate: 'success'
            });
        } else {
            this.setState({
                parent_id: (this.props.parent_id !== undefined ? this.props.parent_id : 0)
            })
        }
    }

    save = e => {
        if (this.state.validate === 'success') {
            this.setState({ isLoader: true });
            if (this.props.editData) {
                InformationService.update(this.props.editData.id, this.state)
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
                                this.props.listing(true);
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
                InformationService.create(this.state)
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
                                this.props.listing(false);
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
        } else {
            if (this.state.validate !== 'success') {
                this.setState({
                    validate: 'error'
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
                        <div className={`option-wrapper ${this.props.editData ? 'isGray' : ''}`}>
                            <ConfirmationModal update={this.state.change} />
                            {this.state.message &&
                            <AlertMessage
                                className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                content={this.state.message}
                                icon={this.state.success ? "check" : "info"}
                            />
                            }
                            <h3><ReactSVG wrapper='span' className="icons" src={require('img/ico-folder-gray.svg')} /> {datamode}</h3>
                            <div className="row">
                                <div className="col-12">
                                    <Input
                                        className={this.state.validate}
                                        type='text'
                                        label={t('PI_FOLDER_NAME')}
                                        name='name'
                                        value={this.state.name}
                                        onChange={this.handleChange('name', 'validate', 'text')}
                                        required={true}
                                    />
                                    {this.state.errors.name &&
                                    <p className="error-message">{this.state.errors.name}</p>}
                                    {this.state.validate === 'error' &&
                                    <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                                </div>
                            </div>
                            <div className="bottom-panel-button">
                                <button disabled={this.state.isLoader ? true : false} className="btn" onClick={this.save.bind(this)}>{this.state.isLoader ?
                                    <span className="spinner-border spinner-border-sm"></span> : (this.props.editData ? t('G_SAVE') : t('G_SAVE'))}</button>
                                <button className="btn btn-cancel" onClick={() => onCancel('folder')}>{t('G_CANCEL')}</button>
                            </div>
                        </div>
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

export default connect(mapStateToProps)(withRouter(Folder));