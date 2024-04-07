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
            menu_id: '0',
            validate: '',
            cms: this.props.cms,
            showInApp: 0,
            showInWebsite: 0,

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
                validate: 'success',
                menu_id: (this.props.editData.section_id !== undefined ? this.props.editData.section_id : 0),
                showInApp:this.props.editData.show_in_app,
                showInWebsite: this.props.editData.show_in_reg_site
            });
        } else {
            this.setState({
                parent_id: (this.props.parent_id !== undefined ? this.props.parent_id : 0),
                menu_id: (this.props.menu_id !== undefined ? this.props.menu_id : 0)
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
                            
                            console.log(response)

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
                        error => { console.log(error) }
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
                                    {
                                        (this.state.cms === "information-pages" && this.state.menu_id == 0) ? 
                                        
                                            <div className='d-flex justify-content-start'>
                                                <label className='m-0 mr-2'>Show in App</label>
                                                <input type="checkbox" name="show-in-app" checked={this.state.showInApp} onClick={() => this.setState({showInApp: this.state.showInApp === 0 ? 1 : 0})}/>
                                                <label className='m-0 ml-2 mr-2'>Show in Website</label>
                                                <input type="checkbox" name="show-in-website" checked={this.state.showInWebsite} onClick={() => this.setState({showInWebsite: this.state.showInWebsite === 0 ? 1 : 0})}/>
                                            </div>
                                     
                                         : ""
                                    }

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