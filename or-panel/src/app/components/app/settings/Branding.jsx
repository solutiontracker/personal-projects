import * as React from 'react';
import Img from 'react-image';
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import { withRouter } from 'react-router-dom';
import FileUpload from '@/app/forms/FileUpload';
import ColorPicker from '@/app/forms/ColorPicker';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { EventService } from 'services/event/event-service';
import { GeneralAction } from 'actions/general-action';
import { Translation } from "react-i18next";
import { confirmAlert } from "react-confirm-alert";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { ReactSVG } from 'react-svg';
import 'react-confirm-alert/src/react-confirm-alert.css';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

function ValidateSingleInput(oInput) {
    var _validFileExtensions = ['.jpg', '.jpeg', '.png', '.gif'];
    if (oInput.type === "file") {
        let fileName = oInput.files;
        for (let i = 0; i < fileName.length; i++) {
            var sFileName = fileName[i].name;
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
                                                    <p>{t('EE_UPLOAD_MESSAGE')}! <br />
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

    }
    return true;
}

class Branding extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            //errors & loading
            message: false,
            success: true,
            errors: {},
            isLoader: false,
            // Social Media
            facebook: null,
            twitter: null,
            gplus: null,
            linkedin: null,
            pinterest: null,
            next: (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? "/event_site/billing-module/payment-methods" : (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 0 && Number(this.props.event.eventsite_payment_setting.is_item) === 1 ? "/event_site/billing-module/items" : "/event_site/billing-module/manage-orders")),
            change: false
        };
    }

    handleCropping = (name, value) => {
        this.setState({
            [name]: value === 'remove' ? '' : value,
            change: true
        });
    }

    componentDidMount() {
        this._isMounted = true;
        this.fetchEvent(this.props.event.id);
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    fetchEvent(id) {
        this.setState({ preLoader: true });
        EventService.fetchEvent(id)
            .then(
                response => {
                    if (response.success) {
                        if (response.data.detail) {
                            if (this._isMounted) {
                                this.setState({
                                    header_logo: response.data.detail.info.header_logo,
                                    app_icon: response.data.detail.info.app_icon,
                                    fav_icon: response.data.detail.info.fav_icon,
                                    social_media_logo: response.data.detail.info.social_media_logo,
                                    eventsite_banners: [],
                                    eventsite_banners_id: response.data.detail.eventsite_banners,
                                    primary_color: response.data.detail.info.primary_color,
                                    secondary_color: response.data.detail.info.secondary_color,
                                    facebook: response.data.detail.social_media ? response.data.detail.social_media.facebook : '',
                                    twitter: response.data.detail.social_media ? response.data.detail.social_media.twitter : '',
                                    linkedin: response.data.detail.social_media ? response.data.detail.social_media.linkedin : '',
                                    gplus: response.data.detail.social_media ? response.data.detail.social_media.gplus : '',
                                    pinterest: response.data.detail.social_media? response.data.detail.social_media.pinterest : '',
                                    preLoader: false
                                })
                            }
                        } else {
                            this.props.history.push('/');
                        }
                    }
                },
                error => { }
            );
    }

    handleChange = input => e => {
        if (e.hex) {
            this.setState({
                [input]: e.hex,
                change: true
            });
        } else {
            if (e.target.type === 'checkbox') {
                this.setState({
                    [input]: e.target.checked,
                    change: true
                });
            } else if (e.target.type === 'file') {
                if (e.target.getAttribute('multiple') !== null) {
                    const validate = ValidateSingleInput(e.target);
                    if (!validate) return;
                    let fileName = e.target.files;
                    this.setState(prevstate => ({
                        [input]: [...prevstate[input], ...fileName],
                        change: true
                    }));
                } else {
                    this.setState({
                        [input]: e.target.files[0],
                        change: true
                    });
                }
            } else if (e.target.getAttribute('data-value') === 'remove-file') {
                confirmAlert({
                    customUI: ({ onClose }) => {
                        return (
                            <Translation>
                                {
                                    t =>
                                        <div className='app-main-popup'>
                                            <div className="app-header">
                                                <h4>{t('G_DELETE')}</h4>
                                            </div>
                                            <div className="app-body">
                                                <p>{t('EE_ON_DELETE_ALERT_MSG')}</p>
                                            </div>
                                            <div className="app-footer">
                                                <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                                <button className="btn btn-success"
                                                    onClick={() => {
                                                        onClose();
                                                        if (this.state[input].length > 1) {
                                                            var array = [...this.state[input]];
                                                            var index = e.target.getAttribute('data-index');
                                                            if (index !== -1) {
                                                                array.splice(index, 1);
                                                                this.setState({
                                                                    [input]: array,
                                                                    change: true
                                                                });
                                                            }
                                                        } else {
                                                            this.setState({
                                                                [input]: [],
                                                                change: true
                                                            });
                                                        }
                                                    }}
                                                >
                                                    {t('G_DELETE')}
                                                </button>
                                            </div>
                                        </div>
                                }
                            </Translation>
                        );
                    }
                });
            } else {
                this.setState({
                    [input]: e.target.value,
                    change: true
                });
            }
        }
    }

    onRemove = input => e => {
        e.preventDefault();
        let id = e.target.getAttribute('data-index');
        let eventsite_banners = this.state.eventsite_banners_id.filter(function (el) {
            return Number(el.id) !== Number(id)
        });
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t('G_DELETE')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{t('EE_ON_DELETE_ALERT_MSG')}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                            onClick={() => {
                                                onClose();
                                                service.destroy(`${process.env.REACT_APP_URL}/eventsite-banner/destroy/${id}`)
                                                    .then(
                                                        response => {
                                                            if (response.success) {
                                                                this.setState({
                                                                    eventsite_banners_id: eventsite_banners,
                                                                    change: true
                                                                });
                                                            }
                                                        },
                                                        error => { }
                                                    );
                                            }}
                                        >
                                            {t('G_DELETE')}
                                        </button>
                                    </div>
                                </div>
                        }
                    </Translation>
                );
            }
        });
    }

    back = e => {
        e.preventDefault();
        this.props.dispatch(GeneralAction.step(3));
        this.props.history.push(`/event/edit/${this.props.event.id}`);
    }

    saveData = (e) => {
        e.preventDefault();
        const type = e.target.getAttribute('data-type');
        this.setState({ isLoader: type, preLoader: true });
        service.post(`${process.env.REACT_APP_URL}/event-settings/branding`, this.state)
            .then(
                response => {
                    if (response.success) {
                        this.setState({
                            message: response.message,
                            success: true,
                            isLoader: false,
                            errors: {},
                            change: false
                        });
                        if (type === "save-next") {
                            this.props.history.push(this.state.next);
                        } else {
                            this.fetchEvent(this.props.event.id);
                        }
                    } else {
                        this.setState({
                            message: response.message,
                            success: false,
                            isLoader: false,
                            preLoader: false,
                            errors: response.errors
                        });
                    }
                },
                error => { }
            );
    };

    handleSocialPopup = (state) => {
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{state === 'gplus' ? 'Google+' : state} URL</h4>
                                    </div>
                                    <div className="app-body">
                                        <input type="text" id="popup-url" className="input-popup" placeholder="URL" defaultValue={this.state[state] && this.state[state]} />
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                            onClick={() => {
                                                const item = document.getElementById("popup-url");
                                                this.setState({
                                                    [state]: item.value,
                                                    change: true
                                                })
                                                onClose();

                                            }}
                                        >
                                            {t('G_OK')}
                                        </button>
                                    </div>
                                </div>
                        }
                    </Translation>
                );
            }
        });
    }

    render() {

        return (
            <Translation>
                {
                    t =>
                        <div className="wrapper-content third-step">
                            <ConfirmationModal update={this.state.change} />
                            {this.state.preLoader &&
                                <Loader />
                            }
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <h1 className="section-title">{t('ED_BRANDING')}</h1>
                                    {this.state.message &&
                                        <AlertMessage
                                            className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                                            title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                                            content={this.state.message}
                                            icon={this.state.success ? "check" : "info"}
                                        />
                                    }
                                    <h4 className="component-heading">{t('ED_UPLOAD_IMAGES')}</h4>
                                    <div className="row d-flex">
                                        <div className="col-12">
                                            <FileUpload
                                                title={t('ED_TOP_HEADER_LOGO')}
                                                tooltip={t('ED_TOP_HEADER_LOGO_TOOLTIP')}
                                                dimenstion='500 x 170'
                                                imgExtension={['.jpg', '.jpeg', '.png']}
                                                maxFileSize={3145728}
                                                multiple={false}
                                                video={false}
                                                value={this.state.header_logo}
                                                stateName="header_logo"
                                                cropper={true}
                                                onChange={this.handleCropping.bind(this)}
                                            />
                                            {this.state.errors && this.state.errors.header_logo &&
                                                <p className="error-message">{this.state.errors.header_logo}</p>}
                                            <FileUpload
                                                title={t('ED_TOP_SITE_BANNER')}
                                                tooltip={t('ED_TOP_SITE_BANNER_TOOLTIP')}
                                                dimenstion='1500 x 500'
                                                imgExtension={['.jpg', '.jpeg', '.png', '.gif']}
                                                maxFileSize={5242880}
                                                multiple={true}
                                                video={false}
                                                value={this.state.eventsite_banners}
                                                cropper={false}
                                                lock={Number(this.props.event.is_registration) === 1 ? false : true}
                                                display={this.state.eventsite_banners_id}
                                                onChange={this.handleChange('eventsite_banners')}
                                                onRemove={this.onRemove('eventsite_banners_id')}
                                            />
                                            {this.state.errors && this.state.errors.eventsite_banners &&
                                                <p className="error-message">{this.state.errors.eventsite_banners}</p>}
                                            <FileUpload
                                                title={t('ED_FAV_ICON')}
                                                tooltip={t('ED_FAV_ICON_TOOLTIP')}
                                                dimenstion='60 x 60'
                                                imgExtension={['.ico']}
                                                maxFileSize={3145728}
                                                multiple={false}
                                                video={false}
                                                value={this.state.fav_icon}
                                                stateName="fav_icon"
                                                cropper={true}
                                                lock={Number(this.props.event.is_registration) === 1 ? false : true}
                                                acceptFiles='image/x-icon'
                                                onChange={this.handleCropping.bind(this)}
                                            />
                                            {this.state.errors && this.state.errors.fav_icon &&
                                                <p className="error-message">{this.state.errors.fav_icon}</p>}
                                            <FileUpload
                                                title={t('ED_SOCIAL_MEDIA_SHARING_IMAGE')}
                                                tooltip={t('ED_SOCIAL_MEDIA_SHARING_IMAGE_TOOLTIP')}
                                                dimenstion='1200 x 630'
                                                imgExtension={['.jpg', '.jpeg', '.png']}
                                                maxFileSize={3145728}
                                                multiple={false}
                                                video={false}
                                                value={this.state.social_media_logo}
                                                stateName="social_media_logo"
                                                cropper={true}
                                                lock={Number(this.props.event.is_registration) === 1 ? false : true}
                                                onChange={this.handleCropping.bind(this)}
                                            />
                                            {this.state.errors && this.state.errors.social_media_logo &&
                                                <p className="error-message">{this.state.errors.social_media_logo}</p>}
                                        </div>
                                        <div className="col-12">
                                            <div className="social-share-wrapp tooltipHeading">
                                                <h5>
                                                    {t('ED_SELECT_SOCIAL_MEDIA_HEADING')}
                                                    <em className="app-tooltip"><i className="material-icons">info</i>
                                                        <div className="app-tooltipwrapper">{t('ED_SELECT_SOCIAL_MEDIA_HEADING_TOOLTIP')}</div>
                                                    </em>
                                                </h5>
                                                <div className={Number(this.props.event.is_registration) === 0 ? "social-media-icons lockfile" : 'social-media-icons'}>
                                                    <span onClick={() => this.handleSocialPopup('facebook')} className={`social facebook ${this.state.facebook && "active"}`}>
                                                        <i className="icons"><ReactSVG alt="" src={require("img/facebook.svg")} /></i>
                                                        {t('G_FACEBOOK')}
                                                        <i className="material-icons">{this.state.facebook ? 'close' : 'add'}</i>
                                                    </span>
                                                    <span onClick={() => this.handleSocialPopup('twitter')} className={`social twitter ${this.state.twitter && "active"}`}>
                                                        <i className="icons"><ReactSVG alt="" src={require("img/twitter.svg")} /></i>
                                                        {t('G_TWITTER')}
                                                        <i className="material-icons">{this.state.twitter ? 'close' : 'add'}</i>
                                                    </span>
                                                    <span onClick={() => this.handleSocialPopup('gplus')} className={`social gplus ${this.state.gplus && "active"}`}>
                                                        <i className="icons"><ReactSVG alt="" src={require("img/gplus.svg")} /></i>
                                                        {t('G_GOOGLEPLUS')}
                                                        <i className="material-icons">{this.state.gplus ? 'close' : 'add'}</i>
                                                    </span>
                                                    <span onClick={() => this.handleSocialPopup('linkedin')} className={`social linkedin ${this.state.linkedin && "active"}`}>
                                                        <i className="icons"><ReactSVG alt="" src={require("img/linkedin.svg")} /></i>
                                                        {t('G_LINKEDIN')}
                                                        <i className="material-icons">{this.state.linkedin ? 'close' : 'add'}</i>
                                                    </span>
                                                    <span onClick={() => this.handleSocialPopup('pinterest')} className={`social pinterest ${this.state.pinterest && "active"}`}>
                                                        <i className="icons"><ReactSVG alt="" src={require("img/pinterest.svg")} /></i>
                                                        {t('G_PINTEREST')}
                                                        <i className="material-icons">{this.state.pinterest ? 'close' : 'add'}</i>
                                                    </span>
                                                    {Number(this.props.event.is_registration) === 0 &&
                                                        <label className="wrapp-file-upload lockfile">
                                                            <i className="icons">
                                                                <Img src={require("img/ico-lock.svg")} alt="" />
                                                            </i>
                                                        </label>
                                                    }
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-12">
                                            <FileUpload
                                                title={t('ED_APP_ICON')}
                                                tooltip={t('ED_APP_ICON_TOOLTIP')}
                                                dimenstion='129 x 129'
                                                imgExtension={['.jpg', '.jpeg', '.png']}
                                                maxFileSize={3145728}
                                                multiple={false}
                                                video={false}
                                                value={this.state.app_icon}
                                                stateName="app_icon"
                                                cropper={true}
                                                lock={Number(this.props.event.is_app) === 1 ? false : true}
                                                onChange={this.handleCropping.bind(this)}
                                            />
                                            {this.state.errors && this.state.errors.app_icon &&
                                                <p className="error-message">{this.state.errors.app_icon}</p>}
                                        </div>
                                    </div>
                                    <section className="widget-section">
                                        <h4 className="component-heading">{t('ED_COLORS')}</h4>
                                        <div className="row d-flex">
                                            <div className="col-12">
                                                <ColorPicker
                                                    label={t('ED_PRIMARY_COLOR')}
                                                    value={this.state.primary_color}
                                                    required={false}
                                                    onChange={this.handleChange('primary_color')}
                                                />
                                                {this.state.errors && this.state.errors.primary_color &&
                                                    <p className="error-message">{this.state.errors.primary_color}</p>}
                                            </div>
                                            <div className="col-12">
                                                <ColorPicker
                                                    label={t('ED_SECONDARY_COLOR')}
                                                    value={this.state.secondary_color}
                                                    onChange={this.handleChange('secondary_color')}
                                                    required={false}
                                                />
                                                {this.state.errors && this.state.errors.secondary_color &&
                                                    <p className="error-message">{this.state.errors.secondary_color}</p>}
                                            </div>
                                        </div>
                                    </section>
                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                            {t('G_PREVIEW')}
                                        </NavLink>
                                        <button id="btn-prev-step" className="btn btn-prev-step" onClick={this.back}><span className="material-icons">
                                            keyboard_backspace</span></button>
                                        <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.saveData}>{this.state.isLoader === "save" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                                        </button>
                                        <button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.saveData}>{this.state.isLoader === "save-next" ?
                                            <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                                        </button>
                                    </div>
                                </React.Fragment>
                            )}
                        </div>
                }
            </Translation>
        );
    }
}

function mapStateToProps(state) {
    const { event, eventStep, template } = state;
    return {
        event, eventStep, template
    };
}

export default connect(mapStateToProps)(withRouter(Branding));