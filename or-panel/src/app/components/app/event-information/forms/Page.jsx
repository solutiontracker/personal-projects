import React, { Component } from 'react';
import { confirmAlert } from "react-confirm-alert";
import { ReactSVG } from "react-svg";
import Input from '@/app/forms/Input';
import FileUpload from '@/app/forms/FileUpload';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import { InformationService } from 'services/information/information-service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import CKEditor from 'ckeditor4-react';
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

function ValidateSingleInput(oInput, _validFileExtensions) {
    if (oInput.type === "file") {
        var sFileName = oInput.value;
        if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() === sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }

            if (!blnValid) {
                confirmAlert({
                    customUI: ({ onClose }) => {
                        return (
                            <Translation>
                                {
                                    t =>
                                        <div className='app-main-popup'>
                                            <div className="app-header">
                                                <h4>{t('EE_WARNING')}</h4>
                                            </div>
                                            <div className="app-body">
                                                <p>{t('EE_UPLOAD_MESSAGE')} <br />
                                                    {_validFileExtensions.length === 1 ? t('EE_WARNING_MESSAGE') : t('EE_WARNING_MESSAGES')} <strong>{_validFileExtensions.join(", ")}</strong></p>
                                            </div>
                                            <div className="app-footer">
                                                <button className="btn btn-cancel" onClick={onClose}>{t('G_OK')}</button>
                                            </div>
                                        </div>
                                }
                            </Translation>
                        );
                    }
                });
                oInput.value = "";
                return false;
            }
        }
    }
    return true;
}
class Page extends Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            description: '',
            image: '',
            pdf: '',
            type: 'page',
            page_type: '1',
            menu_id: '0',
            parent_id: '0',
            validate_name: '',
            validate_description: 'success',
            cms: this.props.cms,
            loaded: false,
            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,

            change: false
        }
    }

    handleChange = (input, item, type) => e => {
        if (e.target.value === undefined) {
            this.setState({
                [input]: '',
                change: true
            })
        } else {
            if (e.target.type === 'file') {
                const validate = ValidateSingleInput(e.target, item);
                if (!validate) return;
                this.setState({
                    [input]: e.target.files[0],
                    change: true
                })
            } else {
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
            }
        };
    }

    componentDidMount() {
        if (this.props.editData) {
            this.setState({
                name: this.props.editData.detail.name,
                description: this.props.editData.detail.description,
                menu_id: this.props.editData.menu_id,
                image: this.props.editData.image,
                pdf: this.props.editData.pdf,
                validate_name: 'success',
                validate_description: 'success',
                loaded: true
            })
        } else {
            this.setState({
                parent_id: (this.props.parent_id !== undefined ? this.props.parent_id : 0),
                menu_id: (this.props.parent_id !== undefined ? this.props.parent_id : 0),
                loaded: true
            })
        }
    }

    save = e => {
        if (this.state.validate_name === 'success' && this.state.validate_description === 'success') {
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
            if (this.state.validate_name !== 'success') {
                this.setState({
                    validate_name: 'error',
                })
            }
            if (this.state.validate_description !== 'success') {
                this.setState({
                    validate_description: 'error',
                })
            }
        }
    }

    handleEditorChange = (e) => {
        this.setState({
            description: e.editor.getData(),
            change: true
        });
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
                            <h3><ReactSVG wrapper='span' className="icons" src={require('img/ico-page-gray.svg')} /> {datamode}</h3>
                            <div className="row">
                                <div className="col-8">
                                    <Input
                                        className={this.state.validate_name}
                                        type='text'
                                        label={t('PI_PAGE_TITLE')}
                                        name='name'
                                        value={this.state.name}
                                        onChange={this.handleChange('name', 'validate_name', 'text')}
                                        required={true}
                                    />
                                    {this.state.errors.name &&
                                        <p className="error-message">{this.state.errors.name}</p>}
                                    {this.state.validate_name === 'error' &&
                                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                                    <h5>{t('PI_PAGE_DESCRIPTION')}</h5>
                                    {this.state.loaded &&
                                        <CKEditor
                                            data={this.state.description}
                                            config={{
                                                enterMode: CKEditor.ENTER_BR,
                                                fullPage: true,
                                                allowedContent: true,
                                                extraAllowedContent: 'style[id]',
                                                htmlEncodeOutput: false,
                                                entities: false,
                                                height: 400,
                                            }}
                                            onChange={this.handleEditorChange}
                                            onBeforeLoad={(CKEDITOR) => (CKEDITOR.disableAutoInline = true)}
                                        />
                                    }
                                    {this.state.errors.description &&
                                        <p className="error-message">{this.state.errors.description}</p>}
                                    {this.state.validate_description === 'error' &&
                                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                                </div>
                                <div className="col-4">
                                    <FileUpload
                                        title={t('PI_INCLUDE_IMAGE')}
                                        className="practical_info_file"
                                        dimenstion={t('PI_INCLUDE_IMAGE_SIZE')}
                                        imgExtension={['.jpg', '.jpeg', '.png', '.gif']}
                                        maxFileSize={5242880}
                                        multiple={false}
                                        video={false}
                                        value={this.state.image}
                                        cropper={false}
                                        onChange={this.handleChange('image', ['.jpg', '.png', '.gif'])}
                                    />
                                    {this.state.errors.image &&
                                        <p className="error-message">{this.state.errors.image}</p>}
                                    <FileUpload
                                        title={t('PI_INCLUDE_PDF')}
                                        className="practical_info_file"
                                        maxFileSize={15728640}
                                        multiple={false}
                                        video={false}
                                        value={this.state.pdf}
                                        cropper={false}
                                        acceptFiles='application/pdf'
                                        onChange={this.handleChange('pdf', ['.pdf'])}
                                    />
                                    {this.state.errors.pdf && <p className="error-message">{this.state.errors.pdf}</p>}
                                </div>
                            </div>
                            <div className="bottom-panel-button">
                                <button disabled={this.state.isLoader ? true : false} className="btn" onClick={this.save.bind(this)}>{this.state.isLoader ?
                                    <span className="spinner-border spinner-border-sm"></span> : (this.props.editData ? t('G_SAVE') : t('G_SAVE'))}</button>
                                <button className="btn btn-cancel" onClick={() => onCancel('page')}>{t('G_CANCEL')}</button>
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

export default connect(mapStateToProps)(withRouter(Page));