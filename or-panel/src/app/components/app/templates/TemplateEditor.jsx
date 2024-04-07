import * as React from "react";
import { NavLink } from 'react-router-dom';
import Input from '@/app/forms/Input';
import Img from 'react-image';
import IframeTemplate from '@/app/forms/IframeTemplate';
import { service } from 'services/service';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { Translation } from "react-i18next";
import { connect } from 'react-redux';
import CKEditor from 'ckeditor4-react';

const in_array = require("in_array");

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class TemplateEditor extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      id: this.props.match.params.id,
      title: '',
      subject: '',
      template: '',
      type: 'new',
      dynamic_fields: [],

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      preLoader: false,

      emaileditor: false,
      editUrl: false,
      event_id: this.props.event.id,
      organizer_id: this.props.event.organizer_id,
      language_id: this.props.event.language_id,

      prevUrl: (Number(this.props.event.is_registration) === 1 ? "/event/registration/tos" : "/event/settings/branding"),

      prev: '',

      interfaceLanguageId: localStorage.getItem('interface_language_id') > 0 ? localStorage.getItem('interface_language_id') : 1,

      update: this.props.update
    };

    this.config = {
      htmlRemoveTags: ['script'],
    };

    this.handleEditorChange = this.handleEditorChange.bind(this);
    this.iframeLister = this.iframeLister.bind(this)
  }

  componentDidMount() {

    this._isMounted = true;
    if (this.state.id !== undefined) {
      this.getTemplate(this.state.id);
    }

    //set next previous
    if (this.props.event && this.props.event.module_permissions && Number(this.props.event.module_permissions.polls) === 0) {
      if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
        let modules = this.props.event.modules.filter(function (module, i) {
          return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
        });

        this.setState({
          prevUrl: (modules[modules.length - 1] !== undefined && module_routes[modules[modules.length - 1]['alias']] !== undefined ? module_routes[modules[modules.length - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/registration/tos" : "/event/settings/branding")),
        });
      }
    } else {
      this.setState({
        prevUrl: "/event/manage/surveys",
      });
    }

    window.addEventListener('message',this.iframeLister, false)
  }

  componentWillUnmount() {
    this._isMounted = false;
      window.removeEventListener('message',this.iframeLister, false)
  }

  iframeLister = (event) => {
    if(event.origin === `${process.env.REACT_APP_EVENTCENTER_URL}`) {
      this.handleFrameClose()
    }    
  }
  
  getEditUrl(id) {
    service.eventCenterAutoLogin(`${process.env.REACT_APP_EVENTCENTER_URL}/_admin/templates/redirectAfterLogin/${this.state.organizer_id}/${this.state.event_id}/${id}/${this.state.language_id}?interface_language_id=${this.state.interfaceLanguageId}`)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                editUrl: response.link,
              });
            }
          }
        },
        error => { }
      );
  }

  getTemplate(id) {
    let index, next, prev;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/template/edit/${id}`)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                
                this.setState({
                  subject: response.data.info.subject,
                  title: response.data.alias,
                  dynamic_fields: response.data.dynamic_fields,
                  template: response.data.style + response.data.info.template,
                  type: response.data.type,
                  preLoader: false
                });

                if (response.data.info.template && response.data.type === "new") {
                  var templateHtml = [response.data.style + response.data.info.template];
                  const templateIframe = document.querySelector('#template').contentWindow.document;
                  templateIframe.open();
                  templateIframe.write(templateHtml);
                  templateIframe.close();
                }

                if (this.props.event.templateIds && this.props.event.templateIds.length > 0) {
                  index = this.props.event.templateIds.indexOf(Number(this.state.id));
                  if (index >= 0) {
                    prev = this.props.event.templateIds[index - 1];
                    next = this.props.event.templateIds[index + 1];
                    this.setState({
                      prev: (prev !== undefined ? prev : ''),
                      next: (next !== undefined ? next : ''),
                    });
                  }
                }

              }
            }
          }
        },
        error => { }
      );
  }

  static getDerivedStateFromProps(props, state) {
    if (state.id !== props.match.params.id && props.match.params.id !== undefined) {
      return {
        id: props.match.params.id,
        message: ""
      };
    } else if (state.update !== props.update) {
      return {
        update: props.update,
        interfaceLanguageId: localStorage.getItem('interface_language_id') > 0 ? localStorage.getItem('interface_language_id') : 1,
      };
    }
    // Return null to indicate no change to state.
    return null;
  }

  componentDidUpdate(prevProps, prevState) {
    if (prevState.id !== this.state.id) {
      this.getTemplate(this.state.id);
    }
  }

  save = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    this.setState({ isLoader: type });
    service.put(`${process.env.REACT_APP_URL}/template/edit/${this.state.id}`, this.state)
      .then(
        response => {
          if (response.success) {
            this.setState({
              'message': response.message,
              'success': true,
              isLoader: false,
              errors: {}
            });
            if (type === "save-next") {
              if (this.state.next) {
                this.props.history.push(`/event/template/edit/${this.state.next}`);
              } else {
                this.props.history.push('/event/invitation/send-invitation');
              }
            }
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

  handleChange = input => e => {
    this.setState({
      [input]: e.target.value
    })
  };

  handleFrameClose = (e) => {
    this.setState({
      editUrl: false
    }, () => {
      this.getTemplate(this.state.id);
    })
  }

  handleEditorChange = (e) => {
    this.setState({
      template: e.editor.getData()
    });
  }
  render() {
    console.log(this.state.title);

    return (
      <Translation key="template">
        {
          t =>
            <div className="wrapper-content third-step">
              {this.state.editUrl && (
                <IframeTemplate
                  url={this.state.editUrl}
                  onClick={this.handleFrameClose.bind(this)}
                />
              )}
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
                  <header style={{marginBottom: 0}} className="new-header clearfix">
                    <div className="row d-flex">
                      <div className="col-6">
                        <h1 className="section-title">{t(`T_EMAIL_TEMPLATES`)}</h1>
                        <h5 style={{fontSize: '14px',marginBottom: '15px'}}>{t(`T_${this.state.title}`)}</h5>
                        <p>{t(`ATTENDEE_UPDATE_EMAIL_CONTENT_ACCORDING_TO_INTERNAL_GUIDELINE_${this.state.title}`)}</p>
                      </div>
                      <div className="col-6">
                        <div className="new-right-header new-panel-buttons float-right">
                          {this.state.type === "new" && (
                            <button className="btn_addNew" onClick={() => this.getEditUrl(this.state.id)}><Img className="imgbox" alt="" src={require('img/ico-edit.svg')} /></button>
                          )}
                          <NavLink to={`/event/template/logs/${this.state.id}`}>
                            <button className="btn_addNew"><Img className="imgbox" alt="" src={require('img/ico-log.svg')} /></button>
                          </NavLink>
                        </div>
                      </div>
                    </div>
                  </header>
                  <Input
                    type='text'
                    label={t('T_SUBJECT')}
                    name='subject'
                    value={this.state.subject}
                    onChange={this.handleChange('subject')}
                    required={true}
                  />
                  {this.state.errors.subject && <p className="error-message">{this.state.errors.subject}</p>}
                  <div style={{ height: '100%' }}>
                    {this.state.template && this.state.type === "new" && (
                      <iframe
                        title="template"
                        frameBorder="0"
                        width="100%"
                        height="350px"
                        id="template"
                      ></iframe>
                    )}
                    {this.state.template && this.state.type === "old" && (
                      <React.Fragment>
                        <CKEditor
                          data={this.state.template}
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
                        />
                        {this.state.errors.template && <p className="error-message">{this.state.errors.template}</p>}
                        <p className="h6 mt-3">{t('T_TEMPLATE_DYNAMIC_FIELD_HEADING')}</p>
                        <p>primary_background_color : {t('T_TEMPLATE_PRIMARY_BACKGROUND_COLOR')}</p>
                        <p>secondary_background_color : {t('T_TEMPLATE_SECONDORY_BACKGROUND_COLOR')}</p>
                        <p>primary_font_color : {t('T_TEMPLATE_PRIMARY_FONT_COLOR')}</p>
                        <p>secondary_font_color : {t('T_TEMPLATE_SECONDORY_FONT_COLOR')}</p>
                        <p className="ml-5 ml-lg-0">{t('T_TEMPLATE_NOTE')}</p>
                        <table style={{ marginTop: '50px' }} className="table">
                          <thead>
                            <tr>
                              <th style={{ border: 'none' }} scope="col">{t('T_DYNAMIC_FIELDS')}</th>
                              <th style={{ border: 'none' }} scope="col"></th>
                            </tr>
                          </thead>
                          <tbody>
                            {this.state.dynamic_fields.length > 0 && Object.values(this.state.dynamic_fields).map(row =>
                              (
                                <tr key={row.name}>
                                  <td style={{ width: '250px', background: '#f8f9fa', fontWeight: '600', color: '#777' }}>{row.name}</td>
                                  <td>{row.field}</td>
                                </tr>
                              )
                            )}
                          </tbody>
                        </table>
                      </React.Fragment>
                    )}
                  </div>
                  <div className="bottom-component-panel clearfix">
                    {Number(this.props.event.default_template_id) === Number(this.state.id) ? (
                      <React.Fragment>
                        {/* this.state.prevUrl && (
                          <NavLink className="btn btn-prev-step" to={this.state.prevUrl}><span className="material-icons">
                            keyboard_backspace</span></NavLink>
                        ) */}
                      </React.Fragment>
                    ) : (
                        <NavLink className="btn btn-prev-step" to={`/event/template/edit/${this.state.prev}`}><span className="material-icons">
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
  const { event, update } = state;
  return {
    event, update
  };
}

export default connect(mapStateToProps)(TemplateEditor);