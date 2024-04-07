import * as React from "react";
import { NavLink } from 'react-router-dom';
import FileUpload from '@/app/forms/FileUpload';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation, withTranslation } from "react-i18next";
import { connect } from 'react-redux';
import { confirmAlert } from "react-confirm-alert";
import ConfirmationModal from "@/app/forms/ConfirmationModal";
import SimpleReactValidator from 'simple-react-validator';

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

const Iframe = ({ data, height }) => {
  var parser = new DOMParser();
  var parsedIframe = parser.parseFromString(data, "text/html");
  let iFrame = parsedIframe.getElementsByTagName("iframe");
  if (iFrame.length !== 0 && iFrame !== undefined) {
    // Read URL:
    let src = iFrame[0].src;
    return (
      <div className="iframe">
        <iframe title='iframe' border="0" src={src} height={height} width="100%"></iframe>
      </div>
    )
  }
  else {
    return (
      <div className="iframe">
        <iframe title='iframe' border="0" src={data} height={height} width="100%"></iframe>
      </div>
    )
  }
};

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

class GoogleMap extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      google_map: 1,
      image: '',
      image_src: '',
      url: '',
      editData: false,
      update: false,

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      preLoader: false,
      toggleMap: true,

      change: false
    }

    this.validator = new SimpleReactValidator({
      validators: {
        validUrl: {  // name the rule
          message: this.props.t('MAP_IFRAME_VALUE_ERROR_MESSAGE'),
          rule: (val, params, validator) => {
            return validator.helpers.testRegex(val, /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/) && params.indexOf(val) === -1
          },
          messageReplace: (message, params) => message.replace(':values', this.helpers.toSentence(params)),  // optional
          required: true  // optional
        }
      },
      element: message => <p className="error-message">{message}</p>,
      messages: {
        required: this.props.t("EE_FIELD_IS_REQUIRED")
      },
    })

  }

  componentDidMount() {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/map/fetch`)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              if (response.data) {
                this.setState({
                  preLoader: false,
                  url: response.data.url,
                  image_src: response.data.image,
                  google_map: response.data.google_map,
                  editData: response.data
                });
              } else {
                this.setState({
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );

    //set next previous
    if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
      let modules = this.props.event.modules.filter(function (module, i) {
        return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
      });

      let index = modules.findIndex(function (module, i) {
        return module.alias === "maps";
      });

      this.setState({
        next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
        prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
      });
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  static getDerivedStateFromProps(props, state) {
    if (!state.image && state.change) {
      return {
        image_src: false
      };
    }
    // Return null to indicate no change to state.
    return null;
  }

  save = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    if (this.state.editData) {
      if (this.validator.allValid() || Number(this.state.google_map) === 0) {
        this.setState({ isLoader: type });
        service.post(`${process.env.REACT_APP_URL}/map/update/${this.state.editData.id}`, this.state)
          .then(
            response => {
              if (response.success) {
                this.setState({
                  'message': response.message,
                  'success': true,
                  isLoader: false,
                  errors: {},
                  update: false
                });
                if (type === "save-next") this.props.history.push(this.state.next);
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
        //This will automatically call the this.forceUpdate() for you when showMessages, hideMessages, showMessageFor, and hideMessageFor are called.
        this.validator.showMessages();
        this.forceUpdate();
      }
    } else {
      if (this.validator.allValid() || Number(this.state.google_map) === 0) {
        this.setState({ isLoader: type });
        service.post(`${process.env.REACT_APP_URL}/map/store`, this.state)
          .then(
            response => {
              if (response.success) {
                this.setState({
                  'message': response.message,
                  'success': true,
                  isLoader: false,
                  errors: {},
                  editData: response.data,
                  update: false
                });
                if (type === "save-next") this.props.history.push(this.state.next);
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
        //This will automatically call the this.forceUpdate() for you when showMessages, hideMessages, showMessageFor, and hideMessageFor are called.
        this.validator.showMessages();
        this.forceUpdate();
      }
    }
  }

  handleChange = input => e => {
    if (e.target.value === undefined) {
      this.setState({
        [input]: '',
        update: true
      })
    } else {
      if (e.target.type === 'file') {
        const validate = ValidateSingleInput(e.target);
        if (!validate) return;
        this.setState({
          [input]: e.target.files[0],
          image_src: URL.createObjectURL(e.target.files[0]),
          change: true,
          update: true
        })
      } else {
        if (input === "url") {
          var parser = new DOMParser();
          var parsedIframe = parser.parseFromString(e.target.value, "text/html");
          let iFrame = parsedIframe.getElementsByTagName("iframe");
          if (iFrame.length !== 0 && iFrame !== undefined) {
            // Read URL:
            let src = iFrame[0].src;
            this.setState({
              [input]: src,
              update: true
            })
          } else {
            this.setState({
              [input]: e.target.value,
              update: true
            })
          }
        } else {
          this.setState({
            [input]: e.target.value,
            update: true
          })
        }
      }
    };
  }

  handleToggle = (google_map) => e => {
    e.preventDefault();
    this.setState({
      google_map: google_map,
      update: true
    })
  }

  render() {

    let module = this.props.event.modules.filter(function (module, i) {
      return in_array(module.alias, ["maps"]);
    });

    return (
      <Translation>
        {
          t =>
            <div className="wrapper-content third-step main-landing-page landing-page-map">
              <ConfirmationModal update={this.state.update} />
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
                  <div className="row">
                    <div className="col-7">
                      <h1 className="section-title">{(module[0]['value'] !== undefined ? module[0]['value'] : t('MAP_NAME'))}</h1>
                      <p>{t('MAP_TOP_HEADLINE')} </p>
                    </div>
                  </div>
                  <div className="row d-flex">
                    <div className={Number(this.state.google_map) === 1 ? 'col-7' : 'col-12'}>
                      <div className="container-map">
                        <div className="navigation">
                          <span className={Number(this.state.google_map) === 1 ? 'active' : ''} onClick={this.handleToggle(1)}>
                            <i className="material-icons">
                              {Number(this.state.google_map) === 1 ? 'radio_button_checked' : 'radio_button_unchecked'}
                            </i>
                            {t('MAP_GOOGLE')}</span>
                          <span className={Number(this.state.google_map) === 0 ? 'active' : ''}
                            onClick={this.handleToggle(0)}>
                            <i className="material-icons">
                              {Number(this.state.google_map) === 0 ? 'radio_button_checked' : 'radio_button_unchecked'}
                            </i>
                            {t('MAP_IMAGE')}
                          </span>
                        </div>
                        {Number(this.state.google_map) === 1 && (
                          <React.Fragment>
                            {!this.state.url ? (
                              <React.Fragment>
                                <h5>{t('MAP_GOOGLE_MAP')}</h5>
                                <p>{t('MAP_GOOGLE_MAP_FOLLOW_STEPS')}</p>
                                <div className="mapboxarea-section">
                                  <span onClick={() => this.setState({ toggleMap: !this.state.toggleMap })} className="btn-showguid">{t('MAP_SHOW_GUIDE')} <i className="material-icons">{this.state.toggleMap ? 'keyboard_arrow_down' : 'keyboard_arrow_right'} </i></span>
                                  {this.state.toggleMap && (
                                    <ul>
                                      <li><strong>{t('MAP_STEP')} 1:</strong> {t('MAP_GO_TO')} <a rel="noopener noreferrer" target="_blank" href="https://maps.google.com">maps.google.com</a></li>
                                      <li><strong>{t('MAP_STEP')} 2:</strong> {t('MAP_ENTER_EMAIL')}</li>
                                      <li><strong>{t('MAP_STEP')} 3:</strong> {t('MAP_PRESS_SHARE_BUTTON')}</li>
                                      <li><strong>{t('MAP_STEP')} 4:</strong> {t('MAP_PRESS_INTEGRATE_MAP')}</li>
                                      <li><strong>{t('MAP_STEP')} 5:</strong> {t('MAP_COPY_HTML_TEXT')}</li>
                                    </ul>
                                  )}
                                </div>
                              </React.Fragment>
                            ) : ''}
                            <Input
                              type='text'
                              label={t('MAP_GOOGLE_URL')}
                              name='mapiframe'
                              value={this.state.url}
                              onChange={this.handleChange('url')}
                              required={true}
                            />
                            {this.state.errors.url && <p className="error-message">{this.state.errors.url}</p>}
                            {this.validator.message('url', this.state.url, 'required|validUrl')}
                          </React.Fragment>
                        )}
                        {Number(this.state.google_map) === 0 && (
                          <React.Fragment>
                            <FileUpload
                              title={t('MAP_IMAGE_UPLOAD_LABEL')}
                              tooltip={t('MAP_IMAGE_UPLOAD_LABEL_TOOLTIP')}
                              className="gmapinput"
                              maxFileSize={15728640}
                              imgExtension={['.jpg', '.jpeg', '.png', '.gif']}
                              multiple={false}
                              video={false}
                              value={this.state.image}
                              cropper={false}
                              onChange={this.handleChange('image')}
                            />
                            {this.state.errors.image && <p className="error-message">{this.state.errors.image}</p>}
                          </React.Fragment>
                        )}
                      </div>
                    </div>
                  </div>
                  <div className="row">
                    <div className="col-12">
                      <div className="result-container">
                        {(this.state.image || this.state.image_src) && Number(this.state.google_map) === 0 && (
                          <div className="googlemap-image">
                            <img src={(this.state.image_src ? this.state.image_src : this.state.image)} alt="" />
                          </div>
                        )}
                        {this.state.url && this.validator.fieldValid('url') && Number(this.state.google_map) === 1 && (
                          <React.Fragment>
                            <Iframe
                              data={this.state.url}
                              height="380px"
                            />
                          </React.Fragment>
                        )}
                      </div>
                    </div>
                  </div>

                  <div className="bottom-component-panel clearfix">
                    <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                      <i className='material-icons'>remove_red_eye</i>
                      {t('G_PREVIEW')}
                    </NavLink>
                    {this.state.prev !== undefined && (
                      <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                        keyboard_backspace</span></NavLink>
                    )}
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

function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(withTranslation()(GoogleMap));