import * as React from 'react';
import { NavLink } from 'react-router-dom';
import { withRouter } from 'react-router-dom';
import TemplateSelection from '@/app/event/components/TemplateSelection';
import Img from 'react-image';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { EventService } from 'services/event/event-service';
import i18n from "i18next";
import { connect } from 'react-redux';
import { EventAction } from 'actions/event/event-action';
import { GeneralAction } from 'actions/general-action';

class Template extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      from_event_id: (this.props.eventState.editData ? this.props.event.id : (localStorage.getItem('from_event_id') ? localStorage.getItem('from_event_id') : '')),
      is_app: (this.props.eventState.editData ? Number(this.props.event.is_app) : Number(localStorage.getItem('is_app')) ? Number(localStorage.getItem('is_app')) : 0),
      is_registration: (this.props.eventState.editData ? Number(this.props.event.is_registration) : localStorage.getItem('is_registration') ? localStorage.getItem('is_registration') : 1),
      editIndex: false,
      activeState: false,
      change: this.props.change,

      //template selection
      paymentTypes: [],
      filterLanguages: [],
      selectedTemplate: [],

      //loading & message
      preLoader: (this.props.match.params.id !== undefined ? true : false),
      message: "",
    };
  }

  componentDidMount() {
    this._isMounted = true;
    let id = (this.props.id ? this.props.id : this.props.match.params.id);
    if (id !== undefined) {
      this.fetchEvent(id);
    }
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.change !== this.state.change) {
      this.props.dispatch(GeneralAction.update(this.state.change));
    }
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
                this.props.eventInfo.detail.event_id = response.data.detail.event.id;
                this.props.eventInfo.detail.name = response.data.detail.event.name;
                this.props.eventInfo.detail.description = response.data.detail.info.description;
                this.props.eventInfo.detail.organizer_name = response.data.detail.event.organizer_name;
                this.props.eventInfo.detail.sms_organizer_name = response.data.detail.info.sms_organizer_name;
                this.props.eventInfo.detail.third_party_redirect_url = response.data.detail.eventsite_setting.third_party_redirect_url;
                this.props.eventInfo.detail.third_party_redirect = response.data.detail.eventsite_setting.third_party_redirect;
                this.props.eventInfo.detail.support_email = response.data.detail.info.support_email;
                this.props.eventInfo.detail.use_waitinglist = response.data.detail.eventsite_setting.use_waitinglist;
                // this.props.eventInfo.detail.waitinglist_offerLetter = response.data.detail.event_waiting_list_settings.offerletter;
                // this.props.eventInfo.detail.waitinglist_validity_duration = response.data.detail.event_waiting_list_settings.validity_duration;
                this.props.eventInfo.detail.organizer_site = response.data.detail.event.organizer_site;
                this.props.eventInfo.detail.ga_setup = response.data.detail.event.ga_setup;
                this.props.eventInfo.detail.language_id = response.data.detail.event.language_id;
                this.props.eventInfo.detail.editState = true;

                this.props.eventInfo.duration.country_id = response.data.detail.event.country_id;
                this.props.eventInfo.duration.timezone_id = response.data.detail.event.timezone_id;
                this.props.eventInfo.duration.location_name = response.data.detail.info.location_name;
                this.props.eventInfo.duration.location_address = response.data.detail.info.location_address;
                this.props.eventInfo.duration.dateformat = response.data.detail.info.dateformat;
                this.props.eventInfo.duration.readonly = response.data.detail.event.readonly;
                this.props.eventInfo.duration.start_date = response.data.detail.event.start_date;
                this.props.eventInfo.duration.start_time = response.data.detail.event.start_time;
                this.props.eventInfo.duration.end_date = response.data.detail.event.end_date;
                this.props.eventInfo.duration.end_time = response.data.detail.event.end_time;
                this.props.eventInfo.duration.cancellation_date = response.data.detail.eventsite_setting.cancellation_date;
                this.props.eventInfo.duration.cancellation_end_time = response.data.detail.eventsite_setting.cancellation_end_time;
                this.props.eventInfo.duration.registration_end_date = response.data.detail.eventsite_setting.registration_end_date;
                this.props.eventInfo.duration.registration_end_time = response.data.detail.eventsite_setting.registration_end_time;
                this.props.eventInfo.duration.ticket_left = response.data.detail.eventsite_setting.ticket_left;
                this.props.eventInfo.duration.countries = [];
                this.props.eventInfo.duration.timezones = [];
                this.props.eventInfo.duration.is_map = response.data.detail.event.is_map;

                this.props.eventInfo.editData = true;
                this.props.eventInfo.errors = {};

                this.props.eventInfo.is_app = response.data.detail.event.is_app;
                this.props.eventInfo.is_registration = response.data.detail.event.is_registration;
                this.props.dispatch(EventAction.eventState(this.props.eventInfo));

                this.setState({
                  from_event_id: response.data.detail.event.id,
                  is_app: response.data.detail.event.is_app,
                  is_registration: response.data.detail.event.is_registration,
                  preLoader: false
                });

                this.props.dispatch(EventAction.eventInfo(response.data.detail.event));
                this.props.dispatch(GeneralAction.redirect(!this.props.redirect));
              }
            } else {
              this.props.history.push('/');
            }
          }
        },
        error => { }
      );
  }

  fetchTemplate(id) {
    EventService.fetchEvent(id)
      .then(
        response => {
          if (response.success) {
            if (response.data.detail) {
              this.props.dispatch(EventAction.template(response.data.detail));
              this.setState({
                is_app: Number(response.data.detail.event.is_app),
                is_registration: Number(response.data.detail.event.is_registration),
              }, () => {
                localStorage.setItem('is_app', Number(response.data.detail.event.is_app));
                localStorage.setItem('is_registration', Number(response.data.detail.event.is_registration));
              });
            }
            this.setState({
              preLoader: false
            });
          }
        },
        error => { }
      );
  }

  handleApp  = e => {
    e.preventDefault();
    this.setState({
      is_app: (this.state.is_app === 1 && this.state.is_registration === 1) ? 0 : 1,
    });
    localStorage.setItem('is_app', (this.state.is_app === 1 && this.state.is_registration === 1) ? 0 : 1);
  };

  handleRegistration = e => {
    e.preventDefault();
    this.setState({
      is_registration: (this.state.is_registration === 1 && this.state.is_app === 1) ? 0 : 1,
    });
    localStorage.setItem('is_registration', (this.state.is_registration === 1 && this.state.is_app === 1) ? 0 : 1);
  };

  saveData = (e) => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    this.save(type);
  };

  save(type) {
    if(this.state.is_app === 1 || this.state.is_registration === 1) {
      if (this.props.eventState.editData) {
        this.setState({ isLoader: type });
        service.put(`${process.env.REACT_APP_URL}/event-settings/modules`, this.state)
          .then(
            response => {
              if (response.success) {
                if (this._isMounted) {
                  this.setState({
                    message: response.message,
                    success: true,
                    isLoader: false,
                    errors: {}
                  });
                }
  
                this.props.event.is_app = this.state.is_app;
                this.props.event.is_registration = this.state.is_registration;
                this.props.dispatch(EventAction.eventInfo(this.props.event));
  
                this.props.eventState.is_app = this.state.is_app;
                this.props.eventState.is_registration = this.state.is_registration;
                this.props.dispatch(EventAction.eventState(this.props.eventState));
  
                if (type === "save-next") {
                  this.props.dispatch(GeneralAction.step((this.props.eventStep + 1)));
                }
  
                this.props.dispatch(GeneralAction.redirect(!this.props.redirect));
  
              } else {
                if (this._isMounted) {
                  this.setState({
                    'message': response.message,
                    'success': false,
                    'isLoader': false,
                    'errors': response.errors
                  });
                }
              }
            },
            error => { }
          );
      } else {
        if (this.state.from_event_id) {
          this.props.eventState.is_app = this.state.is_app;
          this.props.eventState.is_registration = this.state.is_registration;
          this.props.dispatch(EventAction.eventState(this.props.eventState));
          this.props.dispatch(GeneralAction.step((this.props.eventStep + 1)));
        } else {
          this.setState({
            message: i18n.t('EE_SELECT_TEMPLATE')
          });
        }
      }
    } else {
      this.setState({
        message: i18n.t('EE_SELECT_PLATEFORM')
      });
    }
  }

  updateTemplate = (from_event_id, language_id, paymentTypes, filterLanguages, selectedTemplate) => {
    this.setState({
      from_event_id: from_event_id,
      paymentTypes: paymentTypes,
      filterLanguages: filterLanguages,
      selectedTemplate: selectedTemplate,
      preLoader: (from_event_id ? true : false)
    }, () => {
      if (from_event_id) {
        localStorage.setItem('from_event_id', from_event_id);
        localStorage.setItem('language_id', language_id);
        this.fetchTemplate(from_event_id);
      }
    });
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
            <div className="wrapper-content third-step main-landing-page">
              {this.state.preLoader && <Loader fixed="true" />}
              {!this.state.preLoader && (
                <React.Fragment>
                  {this.state.message &&
                    <AlertMessage
                      className={`alert  ${this.state.success ? 'alert-success' : 'alert-danger'}`}
                      title={`${this.state.success ? '' : t('EE_OCCURRED')}`}
                      content={this.state.message}
                      icon={this.state.success ? "check" : "info"}
                    />
                  }
                  <h1 className="section-title">{t('ED_EVENT_SETUP')}</h1>
                  <div className="row d-flex">
                    <div className="col-6 left-box-alt">
                      {!this.props.eventState.editData && (
                        <React.Fragment>
                          <h4 className="tooltipHeading">
                            {t('ED_SELECT_EVENT_TEMPLATE')}
                            <em className="app-tooltip"><i className="material-icons">info</i><div className="app-tooltipwrapper">{t('ED_CONTACT_SUPER_USER_FOR_TEMPLATES_INFORMATION_TEXT')}</div></em>
                          </h4>
                          <p>{t('ED_EVENT_SETUP_DETAIL')} </p>
                          <div data-count="1" className="counter-wrapp">
                            <TemplateSelection updateTemplate={this.updateTemplate} from_event_id={this.state.from_event_id} paymentTypes={this.state.paymentTypes} filterLanguages={this.state.filterLanguages} selectedTemplate={this.state.selectedTemplate} />
                            <div style={{ height: '30px' }}></div>
                          </div>
                        </React.Fragment>
                      )}
                      <React.Fragment>
                        <h4 className="tooltipHeading">{t('ED_SELECT_EVENT_PLATEFORM')}
                        </h4>
                        <p>{t('ED_TEMPLATE_DETAIL')}</p>
                      </React.Fragment>

                      <div data-count="2" className="devices-select counter-wrapp">
                        <div className="row">
                          <div className="col-6">
                            <div onClick={this.handleRegistration} className={Number(this.state.is_registration) === 1 ? "activestate devicebox" : "devicebox"}>
                              {Number(this.state.is_registration) === 1 &&
                                <span className="ischecked"><Img src={require("img/icon-close.svg")} /></span>}
                              <span className="icon">
                                {Number(this.state.is_registration) === 1 ? (
                                  <Img src={require('img/ico-screen-big-alt.svg')} width="63px" height="41px" />
                                ) : (
                                    <Img src={require('img/ico-screen-big.svg')} width="63px" height="41px" />
                                  )}
                              </span>
                              <div style={{ maxWidth: '100%' }} dangerouslySetInnerHTML={{ __html: t('ED_REGISTRATION_WEBSITE') }} />
                            </div>
                          </div>
                          <div className="col-6">
                            <div onClick={this.handleApp} className={Number(this.state.is_app) === 1 ? "activestate devicebox" : "devicebox"}>
                              {Number(this.state.is_app) === 1 &&
                                <span className="ischecked"><Img src={require("img/icon-close.svg")} /></span>}
                              <span className="icon">
                                {Number(this.state.is_app) === 1 ? (
                                  <Img src={require('img/ico-mobile-big-alt.svg')} width="35px" height="43px" />
                                ) : (
                                    <Img src={require('img/ico-mobile-big.svg')} width="35px" height="43px" />
                                  )}
                              </span>
                              <div style={{ maxWidth: '100%' }} dangerouslySetInnerHTML={{ __html: t('ED_EVENT_APP') }} />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="bottom-component-panel clearfix">
                    {this.props.eventState.editData ? (
                      <React.Fragment>
                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                          <i className='material-icons'>remove_red_eye</i>
                          {t('G_PREVIEW')}
                        </NavLink>
                        <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.saveData}>{this.state.isLoader === "save" ?
                          <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                        </button>
                        <button data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.saveData}>{this.state.isLoader === "save-next" ?
                          <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                        </button>
                      </React.Fragment>
                    ) : (
                        <button onClick={this.saveData} data-new="0" className="btn btn-next-step">{t('ED_NEXT_STEP')}</button>
                      )}
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
  const { template, eventStep, eventState, event, redirect } = state;
  return {
    template, eventStep, eventState, event, redirect
  };
}

export default connect(mapStateToProps)(withRouter(Template));