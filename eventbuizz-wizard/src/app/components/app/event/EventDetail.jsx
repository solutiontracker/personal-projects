import * as React from 'react';
import { NavLink } from 'react-router-dom';
import { withRouter } from 'react-router-dom';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import Input from '@/app/forms/Input';
import TextArea from '@/app/forms/TextArea';
import { EventAction } from 'actions/event/event-action';
import { GeneralAction } from 'actions/general-action';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { GeneralService } from 'services/general-service';
import DropDown from '@/app/forms/DropDown';

class EventDetail extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    console.log(this.props.template);
    this.state = {
      event_id: (this.props.eventState.detail ? this.props.eventState.detail.event_id : ''),
      name: (this.props.eventState.detail ? this.props.eventState.detail.name : (this.props.template ? this.props.template.event.name : '')),
      description: (this.props.eventState.detail ? this.props.eventState.detail.description : (this.props.template && this.props.template.info ? this.props.template.info.description : '')),
      organizer_name: (this.props.eventState.detail ? this.props.eventState.detail.organizer_name : (this.props.template ? this.props.template.event.organizer_name : '')),
      sms_organizer_name: (this.props.eventState.detail ? this.props.eventState.detail.sms_organizer_name : (this.props.template && this.props.template.info ? this.props.template.info.sms_organizer_name : '')),
      third_party_redirect_url: (this.props.eventState.detail ? this.props.eventState.detail.third_party_redirect_url : (this.props.template ? this.props.template.eventsite_setting.third_party_redirect_url : '')),
      support_email: (this.props.eventState.detail ? this.props.eventState.detail.support_email : (this.props.template && this.props.template.info ? this.props.template.info.support_email : '')),
      organizer_site: (this.props.eventState.detail ? this.props.eventState.detail.organizer_site : (this.props.template ? this.props.template.eventsite_setting.organizer_site : 1)),
      ga_setup: (this.props.eventState.detail ? this.props.eventState.detail.ga_setup : 1),
      language_id: (this.props.eventState.detail ? this.props.eventState.detail.language_id : (this.props.template ? this.props.template.event.language_id : '1')),
      languages: [],
      use_waitinglist: (this.props.eventState.detail ? this.props.eventState.detail.use_waitinglist :(this.props.template.eventsite_setting.use_waitinglist ? this.props.template.eventsite_setting.use_waitinglist : 0)),
      third_party_redirect: (this.props.eventState.detail ? this.props.eventState.detail.third_party_redirect :(this.props.template.eventsite_setting.third_party_redirect ? this.props.template.eventsite_setting.third_party_redirect : 0)),
      
      // Validation
      validate_name: ((this.props.eventState.detail && this.props.eventState.detail.name) || (this.props.template && this.props.template.event.name) ? 'success' : ''),
      organizer_validate: ((this.props.eventState.detail && this.props.eventState.detail.organizer_name) || (this.props.template && this.props.template.event.organizer_name) ? 'success' : ''),
      support_email_validate: ((this.props.eventState.detail && this.props.eventState.detail.support_email) || (this.props.template && this.props.template.info.support_email) ? 'success' : ''),
      sms_organizer_name_validate: ((this.props.eventState.detail && this.props.eventState.detail.sms_organizer_name) || (this.props.template && this.props.template.info.sms_organizer_name) ? 'success' : ''),
      third_party_redirect_url_validate: this.props.eventState.detail && this.props.eventState.detail.third_party_redirect_url ? 'success' : 'success',
      
      //loading
      preLoader: false,
      editState: false,

      change: this.props.change,
    };
  }

  componentDidMount() {
    this._isMounted = true;
    if (this.props.eventState.editData) {
      this.setState({
        editState: true
      })
    }

    if (this.state.languages.length === 0) {
      this.metadata();
    }
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.change !== this.state.change) {
      this.props.dispatch(GeneralAction.update(this.state.change));
    }
  }

  componentWillUnmount() {
    this.props.eventState.detail = this.state;
    this.props.dispatch(EventAction.eventState(this.props.eventState));
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
                languages: response.data.records.languages,
                preLoader: false,
              });
            }
          }
        },
        error => { }
      );
  }

  continue = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    if (this.state.validate_name === 'error' || this.state.validate_name.length === 0) {
      this.setState({
        validate_name: 'error'
      })
    }
    if (this.state.organizer_validate === 'error' || this.state.organizer_validate.length === 0) {
      this.setState({
        organizer_validate: 'error'
      })
    }
    if (this.state.support_email_validate === 'error' || this.state.support_email_validate.length === 0) {
      this.setState({
        support_email_validate: 'error'
      })
    }
    if (this.state.sms_organizer_name_validate === 'error' || this.state.sms_organizer_name_validate.length === 0) {
      this.setState({
        sms_organizer_name_validate: 'error'
      })
    }
    if (this.state.third_party_redirect_url_validate === 'error') {
      this.setState({
        third_party_redirect_url_validate: 'error'
      })
    }
    if (this.state.validate_name === 'success' &&
      this.state.organizer_validate === 'success' &&
      this.state.support_email_validate === 'success' &&
      this.state.third_party_redirect_url_validate === 'success' &&
      this.state.sms_organizer_name_validate === 'success') {
      if (this.props.eventState.editData) {
        var button = document.getElementById("save");
        if (type === "save") button.setAttribute("data-type", type);
        button.click();
        if (type === "save-next") this.props.dispatch(GeneralAction.step((this.props.eventStep + 1)));
      } else {
        this.props.dispatch(GeneralAction.step((this.props.eventStep + 1)));
      }
    }
  }

  handleChange = (input, item, type) => e => {
    if (!/^https?:\/\//i.test(e.target.value) && type === 'url' && e.target.value !== '' && this.state[input] === '') {
      this.setState({
        [input]: 'https://' + e.target.value,
        [item]: 'success',
        change: true
      })
    } else
      if (e.target.value === '' && type === 'url') {
        this.setState({
          [input]: e.target.value,
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

        if (this.state.editState === false) {
          localStorage.setItem('persistent_event_state', '1');
        }

        setTimeout(() => {
          this.props.eventState.detail = this.state;
          this.props.dispatch(EventAction.eventState(this.props.eventState));
        }, 500);
      }
  }

  updateFlag = input => e => {
    this.setState({
      [input]: this.state[input] === 1 ? 0 : 1,
      change: true
    });

    setTimeout(() => {
      this.props.eventState.detail = this.state;
      this.props.dispatch(EventAction.eventState(this.props.eventState));
    }, 500);
  };

  back = e => {
    e.preventDefault();
    this.props.dispatch(GeneralAction.step((this.props.eventStep - 1)))
  }

  render() {
    const { name, description, organizer_name,
      use_waitinglist, ga_setup, sms_organizer_name,
      support_email, third_party_redirect_url, languages, language_id, third_party_redirect, } = this.state;

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

            <div className="wrapper-content">
              {this.props.isLoader && !this.props.eventState.editData && <Loader className='fixed' />}
              {this.state.preLoader && <Loader fixed="true" />}
              {!this.state.preLoader && (
                <React.Fragment>
                  {this.props.message &&
                    <AlertMessage
                      className={`alert  ${this.props.success ? 'alert-success' : 'alert-danger'}`}
                      title={`${this.props.success ? '' : t('EE_OCCURRED')}`}
                      content={this.props.message}
                      icon={this.props.success ? "check" : "info"}
                    />
                  }
                  <h1 className="section-title">{t('ED_ENTER_EVENT_DETAILS')}</h1>
                  <div className="row d-flex">
                    <div className="col-6">
                      <Input
                        className={this.state.validate_name}
                        type='text'
                        label={t('ED_NAME')}
                        name='name'
                        value={name}
                        onChange={this.handleChange('name', 'validate_name', 'text')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.name &&
                        <p className="error-message">{this.props.eventState.errors.name}</p>}
                      {this.state.validate_name === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                      <TextArea
                        label={t('ED_DESCRIPTION')}
                        value={description}
                        height={330}
                        onChange={this.handleChange('description')}
                        required={false}
                        isDisabled={Number(this.props.eventState.is_registration) === 0 ? true : false}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.description &&
                        <p className="error-message">{this.props.eventState.errors.description}</p>}
                      <DropDown
                        className="lock"
                        label={t('ED_LANGUAGE')}
                        listitems={languages}
                        selected={language_id}
                        isDisabled={true}
                        selectedlabel={this.getSelectedLabel(languages, language_id)}
                        required={true}
                      />
                    </div>
                  </div>
                  <div className="row d-flex">
                    <div className="col-6">
                      <Input
                        className={this.state.organizer_validate}
                        type='text'
                        label={t('ED_ORGANIZER_NAME')}
                        value={organizer_name}
                        onChange={this.handleChange('organizer_name', 'organizer_validate', 'text')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.organizer_name &&
                        <p className="error-message">{this.props.eventState.errors.organizer_name}</p>}
                      {this.state.organizer_validate === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                        <Input
                        className={this.state.support_email_validate}
                        type='mail'
                        label={t('ED_EMAIL_RESPONSIBLE')}
                        value={support_email}
                        onChange={this.handleChange('support_email', 'support_email_validate', 'email')}
                        required={true}
                        tooltip={t('ED_EMAIL_RESPONSIBLE_INFO')}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.support_email &&
                        <p className="error-message">{this.props.eventState.errors.support_email}</p>}
                      {this.state.support_email_validate === 'error' &&
                        <p className="error-message">{t('EE_VALID_EMAIL')}</p>}
                      <Input
                        className={this.state.sms_organizer_name_validate}
                        type='text'
                        label={t('ED_SMS_SENDER')}
                        value={sms_organizer_name}
                        onChange={this.handleChange('sms_organizer_name', 'sms_organizer_name_validate', 'text')}
                        required={true}
                      />
                      {this.props.eventState.errors && this.props.eventState.errors.sms_organizer_name &&
                        <p className="error-message">{this.props.eventState.errors.sms_organizer_name}</p>}
                      {this.state.sms_organizer_name_validate === 'error' &&
                        <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
                       <div className="checkbox-row">
                        <h5>{t('ED_THIRD_PARTY_REDIRECT')}</h5>
                        <label className="custom-checkbox-toggle"><input
                          onChange={this.updateFlag('third_party_redirect')}
                          type="checkbox" defaultChecked={third_party_redirect} /><span></span></label>
                      </div>
                      {(third_party_redirect === 1) && <Input
                        type='text'
                        className={this.state.third_party_redirect_url_validate}
                        label={t('ED_REDIRECT_WEBSITE')}
                        value={third_party_redirect_url}
                        onChange={this.handleChange('third_party_redirect_url', 'third_party_redirect_url_validate', 'url')}
                        required={false}
                        tooltip={t('ED_REDIRECT_WEBSITE_INFO')}
                      />}
                      {this.props.eventState.errors && this.props.eventState.errors.third_party_redirect_url &&
                        <p className="error-message">{this.props.eventState.errors.third_party_redirect_url}</p>}
                      {this.state.third_party_redirect_url_validate === 'error' &&
                        <p className="error-message">{t('EE_VALID_URL')}</p>}
                      <div className="checkbox-row">
                        <h5>{t('ED_WAITING_LIST')}</h5>
                        <p>{t('ED_WAITING_LIST_DESC')}</p>
                        <label className="custom-checkbox-toggle"><input
                          onChange={this.updateFlag('use_waitinglist')}
                          type="checkbox" defaultChecked={use_waitinglist} /><span></span></label>
                      </div>
                      {/* <div className="checkbox-row">
                        <h5>{t('ED_DISPLAY_CALENDER')}</h5>
                        <p>{t('ED_DISPLAY_CALENDER_DESC')}</p>
                        <label className="custom-checkbox-toggle"><input
                          onChange={this.updateFlag('organizer_site')} defaultChecked={organizer_site}
                          type="checkbox" name="" /><span></span></label>
                      </div> */}
                      
                      {window.location.href.includes('/event/create') && (
                        <div className="checkbox-row">
                          <h5>{t('ED_GOOGLE_ANALYTICS')}</h5>
                          <p>{t('ED_GOOGLE_ANALYTICS_DESC')}</p>
                          <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('ga_setup')}
                            defaultChecked={ga_setup} type="checkbox"
                            name="" /><span></span></label>
                        </div>
                      )}
                    </div>
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <button id="btn-prev-step" className="btn btn-prev-step" onClick={this.back}><span className="material-icons">
                      keyboard_backspace</span></button>
                    {this.props.eventState.editData ? (
                      <React.Fragment>
                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                          <i className='material-icons'>remove_red_eye</i>
                          {t('G_PREVIEW')}
                        </NavLink>
                        <button data-type="save" disabled={this.props.isLoader ? true : false} className="btn btn-save" onClick={this.continue}>{this.props.isLoader === "save" ?
                          <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}</button>
                        <button data-type="save-next" disabled={this.props.isLoader ? true : false} className="btn btn-save-next" onClick={this.continue}>{this.props.isLoader === "save-next" ?
                          <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}</button>
                      </React.Fragment>
                    ) : (
                        <button disabled={this.props.isLoader ? true : false} id="btn-next-step" className="btn btn-next-step" onClick={this.continue}>{this.props.isLoader ?
                          <span className="spinner-border spinner-border-sm"></span> : t('G_NEXT_STEP')}
                        </button>
                      )}
                    <span className="hide" onClick={this.props.save} id="save"></span>
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
  const { eventState, eventStep, template } = state;
  return {
    eventState, eventStep, template
  };
}

export default connect(mapStateToProps)(withRouter(EventDetail));