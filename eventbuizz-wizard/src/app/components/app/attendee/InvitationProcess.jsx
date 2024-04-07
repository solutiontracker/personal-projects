import React, { Component } from 'react';
import { connect } from 'react-redux';
import Input from '@/app/forms/Input';
import { service } from 'services/service';
import { Translation, withTranslation } from "react-i18next";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { EventAction } from 'actions/event/event-action';
import { confirmAlert } from 'react-confirm-alert'; // Import
import { AuthAction } from 'actions/auth/auth-action';
import DropDown from '@/app/forms/DropDown';
import CKEditor from 'ckeditor4-react';
import IframeTemplate from '@/app/forms/IframeTemplate';
import ImportCSV from "@/app/forms/ImportCSV";
import { ReactSVG } from "react-svg";
import InviteAttendee from "@/app/attendee/forms/InviteAttendee";
import Pagination from "react-js-pagination";
import { formatString, scrollToTop } from 'helpers';
import LargeScreen from '@/app/attendee/forms/LargeScreen';

const in_array = require("in_array");

const labelArray = [
  {
    name: "-1",
    value: "Do not map this field"
  },
  {
    name: "first_name",
    value: "First Name"
  },
  {
    name: "last_name",
    value: "Last Name"
  },
  {
    name: "email",
    value: "Email"
  },
  {
    name: 'country_code',
    value: 'Country code'
  },
  {
    name: "phone",
    value: "Phone number"
  },
  {
    name: "ss_number",
    value: "Social security number"
  },
  {
    name: "allow_vote",
    value: "Allow vote"
  },
  {
    name: "ask_to_speak",
    value: "Ask to speak"
  }
];

class InvitationProcess extends Component {
  _isMounted = false;

  constructor(props) {

    super(props);
    this.state = {
      query: "",
      attendees: [],
      sms: "",
      total: 0,
      from: 0,
      to: 0,
      page: 1,
      total_requests: 1,
      limit: 10,
      sort_by: 'first_name',
      order_by: 'ASC',
      activePage: 1,
      action: (this.props.invitation ? this.props.invitation.action : ''),
      step: (this.props.invitation ? this.props.invitation.step : ''),
      module: (this.props.invitation ? this.props.invitation.module : ''),
      invite_type: (this.props.invitation ? this.props.invitation.invite_type : ''),
      guest_type: (this.props.invitation ? this.props.invitation.guest_type : ''),
      alias: (this.props.invitation ? this.props.invitation.template_alias : ''),
      ids: (this.props.invitation ? this.props.invitation.ids : []),
      email: "",
      displayElement: false,
      editElement: false,
      editElementIndex: undefined,
      importCSVcontainer: false,
      typing: false,
      typingTimeout: 0,
      modules: [],

      //email template state
      template_id: '',
      subject: '',
      template: '',
      type: 'new',
      emaileditor: false,
      editUrl: false,
      event_id: this.props.event.id,
      organizer_id: this.props.event.organizer_id,
      language_id: this.props.event.language_id,

      //errors & loading
      preLoader: false,
      message: false,
      success: true,
      close: false,
      isLoader: false,
      errors: {},

      //validation
      email_validate: 'success',
      subject_validate: 'success',

      isLarge: false,

      checkedItems: new Map(),

      //large screen popus states
      _url: null,

      interfaceLanguageId: localStorage.getItem('interface_language_id') > 0 ? localStorage.getItem('interface_language_id') : 1,

      update: this.props.update
    };

    this.config = {
      htmlRemoveTags: ['script'],
    };

    this.handleEditorChange = this.handleEditorChange.bind(this);

    this.onSorting = this.onSorting.bind(this);
  }

  componentDidMount() {
    this._isMounted = true;

    if (this.props.invitation) {
      if (this.state.step === 1) {
        this.editTemplate();
      } else if (this.state.step === 2) {
        this.readCheckedItems();
        this.attendees();
      } else if (this.state.step === 3) {
        this.previewTemplate();
      } else if (this.state.step > 3) {
        this.setState({
          action: '',
          step: '',
          module: '',
          invite_type: '',
          guest_type: '',
          alias: '',
          close: false,
          ids: [],
          checkedItems: new Map()
        });
        this.props.dispatch(EventAction.invitation(null));
      }
    }

    this.module_stats();

    document.body.addEventListener('click', this.removePopup.bind(this));
  }
  removePopup = e => {
    const items = document.querySelectorAll(".parctical-button-panel .btn");
    for (let i = 0; i < items.length; i++) {
      const element = items[i];
      element.classList.remove("active");
    }
  }
  componentWillUnmount() {
    this._isMounted = false;
  }

  componentDidUpdate(prevProps, prevState) {
    if ((prevState.step !== this.state.step || (this.state.editUrl !== prevState.editUrl && this.state.editUrl === false)) && this.state.step === 1) {
      this.editTemplate();
    } else if ((prevState.step !== this.state.step || prevState.limit !== this.state.limit || (this.state.order_by !== prevState.order_by || this.state.sort_by !== prevState.sort_by)) && this.state.step === 2) {
      this.readCheckedItems();
      this.attendees();
    } else if (prevState.step !== this.state.step && this.state.step === 3) {
      this.previewTemplate();
    }
    else if ((prevState.step !== this.state.step && this.state.step === 4) || (prevState.page !== this.state.page)) {
      this.progressBar();
      this.send();
    } else if (this.state.step === 4 && prevState.close !== this.state.close) {
      this.progressBar();
    } else if (
      ((prevState.step !== this.state.step &&
        (this.state.step === 5 || this.state.step < 1)) || (prevState.step !== this.state.step && !this.state.step))
    ) {
      this.setState({
        action: '',
        step: '',
        module: '',
        invite_type: '',
        guest_type: '',
        alias: '',
        close: false,
        ids: [],
        checkedItems: new Map()
      });
      this.props.dispatch(EventAction.invitation(null));
      this.module_stats();
    } else if ((prevState.email !== this.state.email || prevState.isLoader !== this.state.isLoader) && this.state.step === 3) {
      this.sendTestEmail();
    }
  }

  static getDerivedStateFromProps(props, state) {
    if (props.invitation.step !== undefined && props.invitation.step !== state.step) {
      return {
        step: props.invitation.step
      };
    } else if (props.invitation.step === undefined) {
      return {
        step: ''
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

  module_stats = () => {
    this.setState({ preLoader: true });
    service
      .get(
        `${process.env.REACT_APP_URL}/attendee/invitations-stats`,
        this.state
      )
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                modules: response.data,
                preLoader: false,
                message: ""
              });
            }
          }
        },
        error => { }
      );
  }

  previewTemplate = () => {
    this.setState({ preLoader: true });
    service
      .put(
        `${process.env.REACT_APP_URL}/attendee/invitation-template`,
        this.state
      )
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                template: response.data.template,
                sms: response.data.sms,
                total: Number(response.data.total_attendees),
                total_requests: Number(
                  Math.ceil(response.data.total_attendees / 50)
                ),
                preLoader: false,
                message: ""
              });

              if (response.data.template) {
                var templateHtml = new Blob([response.data.template], {
                  type: "text/html"
                });

                const templateIframe = document.getElementById("template");
                templateIframe.src = URL.createObjectURL(templateHtml);
              }

              if (response.data.sms) {
                var smsHtml = new Blob([response.data.sms], {
                  type: "text/html"
                });

                const smsIframe = document.getElementById("sms");
                smsIframe.src = URL.createObjectURL(smsHtml);
              }

            }
          }
        },
        error => { }
      );
  };

  editTemplate = () => {
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/template/edit/${this.state.module}`)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {

              this.setState({
                template_id: response.data.id,
                subject: response.data.info.subject,
                alias: response.data.alias,
                template: response.data.style + response.data.info.template,
                type: response.data.type,
                preLoader: false,
                message: ""
              });

              if (response.data.info.template && response.data.type === "new") {
                var templateHtml = new Blob([response.data.style + response.data.info.template], {
                  type: "text/html"
                });

                const templateIframe = document.getElementById("template");
                templateIframe.src = URL.createObjectURL(templateHtml);
              }
            }
          }
        },
        error => { }
      );
  }

  send = () => {
    service
      .put(
        `${process.env.REACT_APP_URL}/attendee/send-invitation`,
        this.state
      )
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              if (this.state.page < this.state.total_requests) {
                this.setState({
                  page: this.state.page + 1,
                  ids: response.data.ids
                });
                this.props.invitation.ids = response.data.ids;
                this.props.dispatch(
                  EventAction.invitation(this.props.invitation)
                );
              } else {
                this.setState({
                  message: response.message,
                  success: true,
                  close: true
                });
              }
            }
          }
        },
        error => { }
      );
  };

  goStep = step => e => {
    if (step === 4) {
      const selected_attendees_length = new Map(
        [...this.state.checkedItems]
          .filter(([k, v]) => v === true)
      ).size;

      confirmAlert({
        customUI: ({ onClose }) => {
          return (
            <Translation>
              {t => (
                <div className="app-main-popup">
                  <div className="app-header">
                    <h4>{t("ATTENDEE_SEND_EMAILS")}</h4>
                  </div>
                  <div className="app-body">
                    <p>
                      {in_array(this.state.action, ["send_by_email_all"]) ? (
                        <React.Fragment>
                          <p>
                            {
                              (() => {
                                if (in_array(this.state.module, ["add_reg"]))
                                  return `${formatString(t('ATTENDEE_SEND_INVITATION_EMAIL_CONFIRMATION'), this.state.modules.add_reg)}`
                                else if (in_array(this.state.module, ["not_registered_invite", "not_registered_reminder"]))
                                  return `${formatString(t('ATTENDEE_SEND_INVITATION_EMAIL_CONFIRMATION'), this.state.modules.not_registered)}`
                                else if (this.state.module === "app_invitation_not_sent")
                                  return `${formatString(t('ATTENDEE_SEND_INVITATION_EMAIL_CONFIRMATION'), this.state.modules.app_invitations_not_sent)}`
                                else if (this.state.module === "app_invitation_sent")
                                  return `${formatString(t('ATTENDEE_SEND_INVITATION_EMAIL_CONFIRMATION'), this.state.modules.app_invitations)}`
                              })()
                            }
                          </p>
                        </React.Fragment>
                      ) : (
                          <p>{formatString(t('ATTENDEE_SEND_INVITATION_EMAIL_CONFIRMATION_SELECTED'), selected_attendees_length)}</p>
                        )}
                    </p>
                  </div>
                  <div className="app-footer">
                    <button className="btn btn-cancel" onClick={onClose}>
                      {t("G_CANCEL")}
                    </button>
                    <button
                      className="btn btn-success"
                      onClick={() => {
                        onClose();
                        this.setState({ step: step, message: '' });
                        if (this.props.invitation) {
                          this.props.invitation.step = step;
                          this.props.dispatch(EventAction.invitation((step > 0 ? this.props.invitation : null)));
                          if (step > 0) {
                            this.props.history.push(`/event/invitation/send-invitation/${step}`);
                          } else {
                            this.props.history.push(`/event/invitation/send-invitation`);
                          }
                        }
                      }}
                    >
                      {t("G_SEND")}
                    </button>
                  </div>
                </div>
              )}
            </Translation>
          );
        }
      });
    } else {
      this.setState({ step: step, message: '' });
      if (this.props.invitation) {
        this.props.invitation.step = step;
        this.props.dispatch(EventAction.invitation((step > 0 ? this.props.invitation : null)));
        if (step > 0) {
          this.props.history.push(`/event/invitation/send-invitation/${step}`);
        } else {
          this.props.history.push(`/event/invitation/send-invitation`);
        }
      }
    }
  };

  sendTestEmail = e => {
    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <Translation>
            {t => (
              <div className="app-main-popup">
                <div className="app-header">
                  <h4>{t('ATTENDEE_SEND_TEST_EMAIL')}</h4>
                </div>
                <div className="app-body">
                  <React.Fragment>
                    <div className="message-box">
                      <p><strong>{t('ATTENDEE_SEND_TEST_TO')}</strong></p>
                      <input
                        value={this.state.email}
                        className="form-control"
                        type="text"
                        placeholder={t('ATTENDEE_EMAIL')}
                        onChange={this.handleChange('email', 'email_validate', 'email')}
                      />
                      {this.state.email_validate === 'error' &&
                        <p className="error-message">{t('EE_VALID_EMAIL')}</p>}
                    </div>
                  </React.Fragment>
                </div>
                <div className="app-footer">
                  <button className="btn btn-cancel" onClick={onClose}>
                    {t("G_CANCEL")}
                  </button>
                  {this.state.email && this.state.email_validate === "success" && (
                    <button disabled={this.state.isLoader ? true : false} className="btn btn-success" onClick={() => {
                      this.setState({ isLoader: true });
                      service
                        .put(
                          `${process.env.REACT_APP_URL}/attendee/send-test-email`,
                          this.state
                        )
                        .then(
                          response => {
                            if (response.success) {
                              if (this._isMounted) {
                                if (response.success) {
                                  this.setState({
                                    message: response.message,
                                    success: response.success,
                                    isLoader: false,
                                    email: ""
                                  });
                                  onClose();
                                }
                              }
                            }
                          },
                          error => { }
                        );
                    }}>{this.state.isLoader ?
                      <span className="spinner-border spinner-border-sm"></span> : t('ATTENDEE_SEND')}</button>
                  )}
                </div>
              </div>
            )}
          </Translation>
        );
      }
    });
  }

  progressBar() {
    var sending_from = this.state.page * 50;
    if (sending_from > this.state.total) {
      sending_from = this.state.total;
    }
    var percentage = (sending_from / this.state.total) * 100;

    confirmAlert({
      closeOnClickOutside: this.state.close === true ? true : false,
      customUI: ({ onClose }) => {
        return (
          <Translation>
            {t => (
              <div className="app-main-popup">
                <div className="app-header">
                  <h4>{t('ATTENDEE_EMAIL_STATUS')}</h4>
                </div>
                <div className="app-body">
                  {this.state.close === true ? (
                    onClose()
                  ) : (
                      <React.Fragment>
                        <div className="message-box">
                          <p><strong>{t('ATTENDEE_SENDING_MESSAGES')}</strong></p>
                          <p>
                            {sending_from}/{this.state.total}
                          </p>
                        </div>
                        <div className="progress-bar-small">
                          <div className="bar-loader" style={{ width: percentage + '%' }}></div>
                        </div>
                      </React.Fragment>
                    )}
                </div>
                {this.state.close && (
                  <div className="app-footer">
                    <button className="btn btn-success" onClick={onClose}>OK</button>
                  </div>
                )}
              </div>
            )}
          </Translation>
        );
      },
      onClickOutside: () => {
        if (this.state.close === true) {
          this.props.dispatch(EventAction.invitation(null));
        }
      },
    });
  }

  handleChange = (input, item, type) => e => {
    if (input === "module") {
      if (in_array(e.value, ["not_registered_invite", "not_registered_reminder"]) && Number(this.state.modules.not_registered) === 0) {
        this.alert(this.props.t('ATTENDEE_RESEND_INVITE'), this.props.t('ATTENDEE_RESEND_INVITE_ALERT_NO_GUEST'));
      } else if (in_array(e.value, ["app_invitation_not_sent"]) && Number(this.state.modules.app_invitations_not_sent) === 0) {
        this.alert(this.props.t('ATTENDEE_APP_INVITE'), this.props.t('ATTENDEE_APP_INVITE_ALERT_NO_ATTENDEES'));
      } else if (in_array(e.value, ["app_invitation_sent"]) && Number(this.state.modules.app_invitations) === 0) {
        this.alert(this.props.t('ATTENDEE_APP_REMINDER'), this.props.t('ATTENDEE_APP_REMINDER_ALERT_NO_SENT'));
      } else {
        this.setState({
          [input]: e.value,
          step: 1
        }, () => {
          this.props.dispatch(EventAction.invitation({
            module: e.value,
            step: 1
          }));
        });
      }
    } else if (input === "guest_type") {
      let parts = e.value.split('|');
      this.setState({
        [input]: e.value,
        action: parts[1],
        invite_type: parts[0]
      }, () => {
        this.props.invitation.invite_type = parts[0];
        this.props.invitation.action = parts[1];
        this.props.invitation.guest_type = e.value;
        this.props.dispatch(EventAction.invitation(this.props.invitation));
      });
    } else {
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
    }
  }

  handleEditorChange = (e) => {
    this.setState({
      template: e.editor.getData()
    });
  }

  handleFrameClose = (e) => {
    this.setState({
      editUrl: false
    });
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

  save = e => {
    e.preventDefault();
    if (this.state.step === 1) {
      if (this.state.subject_validate === 'error' || this.state.subject_validate.length === 0) {
        this.setState({
          subject_validate: 'error'
        })
      }
      if (this.state.subject_validate === 'success') {
        const type = e.target.getAttribute('data-type');
        this.setState({ isLoader: type });
        service.put(`${process.env.REACT_APP_URL}/template/edit/${this.state.template_id}`, this.state)
          .then(
            response => {
              if (response.success) {
                const { step } = this.state;
                this.setState({
                  message: response.message,
                  success: true,
                  isLoader: false,
                  step: (type === "save-next" ? (step + 1) : step),
                  errors: {}
                }, () => {
                  if (type === "save-next") {
                    if (this.props.invitation) {
                      this.props.invitation.template_alias = this.state.alias;
                      this.props.invitation.step = step + 1;
                      this.props.dispatch(EventAction.invitation(this.props.invitation));
                      this.props.history.push(`/event/invitation/send-invitation/${step + 1}`);
                    }
                  }
                });
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
    } else if (this.state.step === 2) {
      if (this.state.guest_type) {
        const { step } = this.state;
        let actions = [
          "send_by_email",
          "send_by_sms",
          "send_by_sms_email",
          "invite_send_only",
          "resend_by_email"
        ];
        let ids = [];
        this.state.checkedItems.forEach((value, key, map) => {
          if (value === true) {
            ids.push(key);
          }
        });
        if ((ids.length > 0 && actions.indexOf(this.state.action) > -1) || actions.indexOf(this.state.action) === -1) {
          this.setState({ step: (step + 1), message: '', ids: ids }, () => {
            this.props.invitation.step = (step + 1);
            this.props.invitation.ids = ids;
            this.props.dispatch(EventAction.invitation(this.props.invitation));
            this.props.history.push(`/event/invitation/send-invitation/${step + 1}`);
          });
        } else {
          this.setState({
            success: false,
            message: this.props.t('ATTENDEE_GUESTS_REQUIRED')
          }, () => {
            scrollToTop();
          });
        }
      } else {
        this.setState({
          success: false,
          message: this.props.t('ATTENDEE_SELECT_GUEST_TYPE_REQUIRED')
        }, () => {
          scrollToTop();
        });
      }
    }
  }

  attendees = (activePage = 1, loader = false, type = "save") => {
    var _url;

    if (this.state.module === "add_reg")
      _url = `${process.env.REACT_APP_URL}/attendee/invitations/`;
    else if (in_array(this.state.module, ["not_registered_invite", "not_registered_reminder"]))
      _url = `${process.env.REACT_APP_URL}/attendee/not-registered/`;
    else if (this.state.module === "app_invitation_not_sent")
      _url = `${process.env.REACT_APP_URL}/attendee/app-invitations-not-sent/`;
    else if (this.state.module === "app_invitation_sent")
      _url = `${process.env.REACT_APP_URL}/attendee/app-invitations/`;

    this.setState({ preLoader: !loader ? true : false, _url: _url });
    service
      .post(_url + activePage, this.state)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              var modules = { ...this.state.modules }
              modules.add_reg = (this.state.module === "add_reg" ? response.data.total : this.state.modules.add_reg);
              this.setState({
                attendees: response.data.data,
                activePage: response.data.current_page,
                total: response.data.total,
                from: response.data.from,
                to: response.data.to,
                editElement: false,
                preLoader: false,
                checkedItems: new Map(),
                modules: modules,
                displayElement: (type === "save-new" ? true : false),
                message: ""
              });
            }
          }
        },
        error => { }
      );
  };

  handleDeleteElement = id => {
    if (id === "selected" && this.state.checkedItems.size > 0) {
      let ids = [];
      this.state.checkedItems.forEach((value, key, map) => {
        if (value === true) {
          ids.push(key);
        }
      });
      this.deleteAttendees(id, ids);
    } else if (id !== "selected") {
      this.deleteAttendees(id);
    }
  };

  exportAttendees = () => {
    service.download((this.state.module === "add_reg" ? `${process.env.REACT_APP_URL}/attendee/invitations/export/registration-invitations` : `${process.env.REACT_APP_URL}/attendee/invitations/export/not-registered`))
      .then(response => {
        response.blob().then(blob => {
          if (window.navigator && window.navigator.msSaveOrOpenBlob) {
            var csvData = new Blob([blob], { type: "text/csv" });
            window.navigator.msSaveOrOpenBlob(csvData, "export.csv");
          } else {
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement("a");
            a.href = url;
            a.download = "export.csv";
            a.click();
          }
        });
      });
  }

  handlePageChange = activePage => {
    this.attendees(activePage);
  };

  deleteAttendees(id, ids = []) {
    const selected_attendees_length = new Map(
      [...this.state.checkedItems]
        .filter(([k, v]) => v === true)
    ).size;

    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <Translation>
            {t => (
              <div className="app-main-popup">
                <div className="app-header">
                  <h4>{(in_array(id, ['selected', 'all']) ? t("ATTENDEE_DELETE_ALL_GUESTS") : t("G_DELETE"))}</h4>
                </div>
                <div className="app-body">
                  <p>
                    {in_array(id, ['selected', 'all']) ? (
                      <React.Fragment>
                        {id === 'all' ? (
                          <React.Fragment>
                            {
                              (() => {
                                if (in_array(this.state.module, ["add_reg"]))
                                  return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.state.modules.add_reg)}`
                                else if (in_array(this.state.module, ["not_registered_invite", "not_registered_reminder"]))
                                  return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.state.modules.not_registered)}`
                                else if (this.state.module === "app_invitation_not_sent")
                                  return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.state.modules.app_invitations_not_sent)}`
                                else if (this.state.module === "app_invitation_sent")
                                  return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.state.modules.app_invitations)}`
                              })()
                            }
                          </React.Fragment>
                        ) : (
                            formatString(t('ATTENDEE_INVITATION_DELETE_SELECTED_CONFIRMATION'), selected_attendees_length)
                          )}
                      </React.Fragment>
                    ) : (
                        t('EE_ON_DELETE_ALERT_MSG')
                      )}
                  </p>
                </div>
                <div className="app-footer">
                  <button className="btn btn-cancel" onClick={onClose}>
                    {t("G_CANCEL")}
                  </button>
                  <button
                    className="btn btn-success"
                    onClick={() => {
                      onClose();
                      service
                        .destroy(
                          `${process.env.REACT_APP_URL}/attendee/destroy-invitation/${id}`,
                          { ids: ids, module: (this.state.module === "add_reg" ? 'add_reg' : 'not_registered') }
                        )
                        .then(
                          response => {
                            if (response.success) {
                              this.attendees(1, false);
                            } else {
                              this.setState({
                                message: response.message,
                                success: false
                              });
                            }
                          },
                          error => { }
                        );
                    }}
                  >
                    {t("G_DELETE")}
                  </button>
                </div>
              </div>
            )}
          </Translation>
        );
      }
    });
  }
  _actions = {
    add_reg: {
      selected: "registration_invite|send_by_email",
      all: "all_invites|send_by_email_all",
    },
    not_registered_invite: {
      selected: "registration_invite|resend_by_email",
      all: "reg_all_reinvites|send_by_email_all",
    },
    not_registered_reminder: {
      selected: "registration_invite_reminder|send_by_email",
      all: "all_reinvites|send_by_email_all",
    },
    app_invitation_not_sent: {
      selected: "app_invite|send_by_email",
      all: "not_send_all_invites|send_by_email_all",
    },
    app_invitation_sent: {
      selected: "app_invite|send_by_email",
      all: "resend_all_invites|send_by_email_all",
    },
  }
  onFieldChange(event) {
    const self = this;
    if (self.state.typingTimeout) {
      clearTimeout(self.state.typingTimeout);
    }
    self.setState({
      query: event.target.value,
      typing: false,
      typingTimeout: setTimeout(function () {
        self.attendees(1);
      }, 1000)
    });
  }

  handleDocument = e => {
    const element = document.querySelectorAll(".dropdown .btn.active");
    for (let i = 0; i < element.length; i++) {
      const item = element[i];
      item.classList.remove('active');
    }

  }

  handleEditElement = index => {
    this.setState({
      editElement: true,
      editElementIndex: index,
      displayElement: false
    });
  };

  handleDropdown = e => {
    e.stopPropagation();
    const items = document.querySelectorAll(".parctical-button-panel .btn");
    for (let i = 0; i < items.length; i++) {
      const element = items[i];
      if (element.classList === e.target.classList) {
        e.target.classList.toggle("active");
      } else {
        element.classList.remove("active");
      }
    }
  };

  handleCancel = () => {
    this.setState({
      editElement: false,
      editElementIndex: undefined,
      displayElement: false
    });
  };

  handleAddElement = () => {
    this.setState({
      displayElement: true
    });
  };

  importCSVFile = () => {
    this.setState({
      importCSVcontainer: true
    });
  };

  handleClose = () => {
    this.setState({
      importCSVcontainer: false
    });
    this.attendees(1, false);
  };

  handleSelectAll = e => {

    const check = e.target.checked;
  
    if(!check) {
      let parts = this._actions[this.state.module]['selected'].split('|');
      this.setState({action: parts[1], guest_type:this._actions[this.state.module]['selected'], invite_type: parts[0]});
    } else {
      let parts = this._actions[this.state.module]['all'].split('|');
      this.setState({action: parts[1], guest_type:this._actions[this.state.module]['all'], invite_type: parts[0]});
    }

    const checkitems = document.querySelectorAll(".invitation-records input");

    for (let i = 0; i < checkitems.length; i++) {
      const element = checkitems[i];
      this.setState(prevState => ({
        checkedItems: prevState.checkedItems.set(element.name, check)
      }));
    }

  };

  handleCheckbox = e => {
    const checkitems = document.querySelectorAll(".invitation-records input");
    const selectall = document.getElementById("selectall");
    for (let i = 0; i < checkitems.length; i++) {
      const element = checkitems[i].checked;
      if (element === false) {
        let parts = this._actions[this.state.module]['selected'].split('|');
        selectall.checked = false;
        this.setState({action: parts[1],guest_type:this._actions[this.state.module]['selected'], invite_type: parts[0]});
        break;
      } else {
        let parts = this._actions[this.state.module]['all'].split('|');
        selectall.checked = true;
        this.setState({action: parts[1],guest_type:this._actions[this.state.module]['all'], invite_type: parts[0]});
      }
    }
    const item = e.target.name;
    const isChecked = e.target.checked;
    this.setState(prevState => ({
      checkedItems: prevState.checkedItems.set(item, isChecked)
    }));
  };

  readCheckedItems = () => {
    if (this.props.invitation && this.props.invitation.ids !== undefined && this.props.invitation.ids.length > 0) {
      for (let i = 0; i < this.props.invitation.ids.length; i++) {
        const id = this.props.invitation.ids[i];
        this.setState(prevState => ({
          checkedItems: prevState.checkedItems.set(id, true)
        }));
      }
    }
  }
   

  alert = (heading, sub_heading) => {
    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <Translation>
            {t => (
              <div className="app-main-popup">
                <div className="app-header">
                  <h4>{heading}</h4>
                </div>
                <div className="app-body">
                  <p>{sub_heading}</p>
                </div>
                <div className="app-footer">
                  <button className="btn btn-success" onClick={onClose}>
                    {t("G_OK")}
                  </button>
                </div>
              </div>
            )}
          </Translation>
        );
      }
    });
  }

  handleLimit = (limit) => e => {
    this.setState(prevState => ({
      limit: limit,
    }));
  };

  popup = e => {
    this.setState({
      isLarge: false
    });
  };

  onSorting(event) {
    this.setState({
      order_by: event.target.attributes.getNamedItem('data-order').value,
      sort_by: event.target.attributes.getNamedItem('data-sort').value,
    });
  }

  render() {

    const modules = [
      {
        name: this.props.t('ATTENDEE_REGISTRATION_INVITE'),
        id: 'add_reg'
      }, {
        name: this.props.t('ATTENDEE_RESEND_INVITE'),
        id: 'not_registered_invite'
      }, {
        name: this.props.t('ATTENDEE_REGISTRATION_REMINDER'),
        id: 'not_registered_reminder'
      }, {
        name: this.props.t('ATTENDEE_APP_INVITE'),
        id: 'app_invitation_not_sent'
      }, {
        name: this.props.t('ATTENDEE_APP_REMINDER'),
        id: 'app_invitation_sent'
      }
    ];

    
    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.name : '');
      }
    }

    const Attendees = ({ data }) => {
      return data.map((data, key) => {
        return (
          <Translation key={key}>
            {t => (
              <React.Fragment>
                <div
                  className={`row d-flex align-items-center invitation-records ${this.state.editElement && 'no-hover'} ${this.state.checkedItems.get(data.id.toString()) ? 'check' : ''}`}
                  key={key}
                >
                  <div className="col-2">
                    <h5>
                      <label className={`checkbox-label`}>
                        <input
                          type="checkbox"
                          disabled={in_array(this.state.action, ["send_by_email_all"]) ? true : false}
                          name={data.id.toString()}
                          checked={in_array(this.state.action, ["send_by_email_all"]) ? true : (this.state.checkedItems.get(
                            data.id.toString()
                          ))}
                          onChange={this.handleCheckbox}
                        />
                        <em></em>
                      </label>
                      {data.first_name}
                    </h5>
                  </div>
                  <div className="col-2">
                    <p>{data.last_name ? data.last_name : '-'}</p>
                  </div>
                  <div className="col-2 col-phone">
                    <p>{data.phone ? data.phone : '-'}</p>
                  </div>
                  <div className="col-2">
                    <p>{data.email ? data.email : '-'}</p>
                  </div>                 
                  <div className="col-2">
                    <p>{data.ss_number ? t("G_YES") : t("G_NO")}</p>
                  </div>
                  <div className="col-2 col-last">
                    {in_array(this.state.module, ["not_registered_invite", "not_registered_reminder", "add_reg"])
                      && (
                        <ul className="panel-actions">
                          {/*  <li>
                          <span onClick={() => this.handleEditElement(key)}>
                            <i className="icons">
                              <ReactSVG wrapper="span"  src={require("img/ico-email-send-gray.svg")} />
                            </i>
                          </span>
                        </li> */}
                          {in_array(this.state.module, ["add_reg"]) && (
                            <li>
                              <span onClick={() => this.handleEditElement(key)}>
                                <i className="icons">
                                  <ReactSVG wrapper="span" src={require("img/ico-edit-gray.svg")} />
                                </i>
                              </span>
                            </li>
                          )}
                          <li>
                            <span onClick={() => this.handleDeleteElement(data.id)}>
                              <i className="icons">
                                <ReactSVG wrapper="span" src={require("img/ico-delete-gray.svg")} />
                              </i>
                            </span>
                          </li>
                        </ul>
                      )}
                  </div>
                  {this.state.editElement &&
                    this.state.editElementIndex === key ? (
                      <InviteAttendee
                        listing={this.attendees}
                        editdata={data}
                        editdataindex={key}
                        datacancel={this.handleCancel}
                        editElement={this.state.editElement}
                      />
                    ) : (
                      ""
                    )}
                </div>
              </React.Fragment>
            )}
          </Translation>
        );
      });
    };

    const selected_attendees_length = new Map(
      [...this.state.checkedItems]
        .filter(([k, v]) => v === true)
    ).size;

    var topHeading, bottomHeading, description;

    if (this.state.module === "add_reg") {
      topHeading = this.props.t("ATTENDEE_UPLOAD_UPDATE_LIST");
      bottomHeading = this.props.t("ATTENDEE_REGISTRATION_INVITE");
      description = this.props.t("ATTENDEE_REGISTRATION_INVITE_DESCRIPTION");
    }
    else if (in_array(this.state.module, ["not_registered_invite"])) {
      topHeading = this.props.t("ATTENDEE_UPLOAD_UPDATE_LIST");
      bottomHeading = this.props.t("ATTENDEE_RESEND_INVITE");
      description = this.props.t("ATTENDEE_RESEND_INVITE_DESCRIPTION");
    }
    else if (in_array(this.state.module, ["not_registered_reminder"])) {
      topHeading = this.props.t("ATTENDEE_UPLOAD_UPDATE_LIST");
      bottomHeading = this.props.t("ATTENDEE_REGISTRATION_REMINDER");
      description = this.props.t("ATTENDEE_REGISTRATION_REMINDER_DESCRIPTION");
    }
    else if (this.state.module === "app_invitation_not_sent") {
      topHeading = this.props.t("ATTENDEE_APP_INVITE");
      bottomHeading = "";
      description = this.props.t("ATTENDEE_APP_INVITE_DESCRIPTION");
    }
    else if (this.state.module === "app_invitation_sent") {
      topHeading = this.props.t("ATTENDEE_APP_REMINDER");
      bottomHeading = "";
      description = this.props.t("ATTENDEE_APP_REMINDER_DESCRIPTION");
    }

    return (
      <Translation>
        {t => (
          <div className="wrapper-content third-step">
            {this.state.isLarge && (
              <LargeScreen module={this.state.module} modules={this.state.modules} _url={this.state._url} topHeading={topHeading} bottomHeading={bottomHeading} popup={this.popup} destroyUrl={`${process.env.REACT_APP_URL}/attendee/destroy-invitation`} checkedItems={this.state.checkedItems}>
                <InviteAttendee />
              </LargeScreen>
            )}
            {this.state.preLoader && <Loader />}
            {!this.state.preLoader && (
              <React.Fragment>
                {this.state.message && (
                  <AlertMessage
                    className={`alert  ${
                      this.state.success ? "alert-success" : "alert-danger"
                      }`}
                    title={`${this.state.success ? "" : t("EE_OCCURRED")}`}
                    content={this.state.message}
                    icon={this.state.success ? "check" : "info"}
                  />
                )}
                {(!this.state.module) ? (
                  <React.Fragment>
                    <div style={{ height: '100%' }}>
                      <div className="row d-flex">
                        <div className="col-6 left-box-alt">
                          <h1 className="section-title">{t('ATTENDEE_ATTENDEE_TYPE')}</h1>
                          <h4 className="component-heading">{t('ATTENDEE_SELECT_INVITE_TYPE')}</h4>
                          <DropDown
                            label={t('ATTENDEE_SELECT_INVITE_TYPE_LABEL')}
                            listitems={modules}
                            required={true}
                            selected={this.state.module}
                            isSearchable='false'
                            selectedlabel={this.getSelectedLabel(modules, this.state.module)}
                            onChange={this.handleChange('module')}
                          />
                        </div>
                      </div>
                    </div>
                  </React.Fragment>
                ) : (
                    <React.Fragment>
                      {this.state.step === 1 && (
                        <React.Fragment>
                          <div style={{ height: '100%' }}>
                            <div className="wrapper-content third-step">
                              {this.state.editUrl && (
                                <IframeTemplate
                                  url={this.state.editUrl}
                                  onClick={this.handleFrameClose.bind(this)}
                                />
                              )}
                              <header style={{ marginBottom: '0' }} className="new-header clearfix">
                                <div className="row">
                                  <div className="col-6">
                                    <h1 className="section-title">{t('ATTENDEE_EDIT_EMAIL')}</h1>
                                    {this.state.alias && (
                                      <h4 className="component-heading">{bottomHeading}</h4>
                                    )}
                                    <p>{t(`ATTENDEE_UPDATE_EMAIL_CONTENT_ACCORDING_TO_INTERNAL_GUIDELINE_${this.state.alias}`)}</p>
                                  </div>
                                  <div className="col-6">
                                    <div style={{ paddingRight: '0' }} className="new-right-header new-panel-buttons justify-content-end">
                                      {this.state.type === "new" && (
                                        <button onClick={() => this.getEditUrl(this.state.template_id)} className="btn_addNew">
                                          <ReactSVG wrapper="span" className="icons" src={require('img/ico-edit.svg')} />
                                        </button>
                                      )}
                                    </div>
                                  </div>
                                </div>
                              </header>
                              <Input
                                type='text'
                                label={t('T_SUBJECT')}
                                name='subject'
                                value={this.state.subject}
                                onChange={this.handleChange('subject', 'subject_validate', 'subject')}
                                required={true}
                              />
                              {this.state.errors.subject && <p className="error-message">{this.state.errors.subject}</p>}
                              {this.state.subject_validate === 'error' &&
                                <p className="error-message">{t('EE_FIELD_IS_REQUIRED')}</p>}
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
                                </React.Fragment>
                              )}
                            </div>
                          </div>
                          <div className="bottom-component-panel clearfix">
                            <button
                              onClick={this.goStep(this.state.step - 1)}
                              className="btn btn-prev-step"
                            >
                              <span className="material-icons">keyboard_backspace</span>
                            </button>
                            {/* <button data-type="save" disabled={this.state.isLoader ? true : false} className="btn btn btn-save" onClick={this.save}>{this.state.isLoader === "save" ?
                              <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE')}
                            </button> */}
                            <button style={{ marginLeft: '15px' }} data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.save}>{this.state.isLoader === "save-next" ?
                              <span className="spinner-border spinner-border-sm"></span> : t('G_NEXT')}
                            </button>
                          </div>
                        </React.Fragment>
                      )}
                      {this.state.step === 2 && (
                        <React.Fragment>
                          <div style={{ height: '100%' }}>
                            {this.state.importCSVcontainer !== false ? (
                              <ImportCSV
                                apiUrl={`${process.env.REACT_APP_URL}/general/import/attendee-invites`}
                                downloadFile="/samples/attendee-invite-sample.csv"
                                speaker="1"
                                onClick={this.handleClose}
                                element={labelArray}
                                validate={["first_name", "email"]}
                              />
                            ) : (
                                <div style={{ flexDirection: 'column', height: '100%' }} className="d-flex">
                                  {this.state.preLoader && <Loader />}
                                  {!this.state.preLoader && (
                                    <React.Fragment>
                                      <header className="new-header clearfix">
                                        <div style={{ height: '100%' }} className="row d-flex">
                                          <div className="col-6">
                                            <h1
                                              style={{ whiteSpace: "nowrap" }}
                                              className="section-title"
                                            >
                                              {topHeading}
                                            </h1>
                                            <h4 style={{ marginBottom: "10px" }} className="component-heading">{bottomHeading}</h4>
                                            <p>{description}</p>
                                            {this.state.attendees.length > 0 && (
                                              <React.Fragment>
                                                <h4 className="component-heading">{t('ATTENDEE_SELECT_GUEST_TYPE')}</h4>
                                                <DropDown
                                                  label={t('ATTENDEE_SELECT_GUESTS')}
                                                  listitems={[
                                                    {
                                                      name: this.props.t('ATTENDEE_SEND_EMAIL_TO_SELECTED_GUEST'),
                                                      id: this._actions[this.state.module]['selected']
                                                    }, {
                                                      name: this.props.t('ATTENDEE_SEND_EMAIL_TO_ALL_GUEST'),
                                                      id: this._actions[this.state.module]['all']
                                                    }
                                                  ]}
                                                  required={true}
                                                  isSearchable={false}
                                                  selected={this.state.guest_type}
                                                  selectedlabel={this.getSelectedLabel([
                                                    {
                                                      name: this.props.t('ATTENDEE_SEND_EMAIL_TO_SELECTED_GUEST'),
                                                      id: this._actions[this.state.module]['selected']
                                                    }, {
                                                      name: this.props.t('ATTENDEE_SEND_EMAIL_TO_ALL_GUEST'),
                                                      id: this._actions[this.state.module]['all']
                                                    }
                                                  ], this.state.guest_type)}
                                                  onChange={this.handleChange('guest_type')}
                                                />
                                                <p className="info-alert-parag">
                                                  {in_array(this.state.action, ["send_by_email_all"]) ? (
                                                    <React.Fragment>
                                                      {in_array(this.state.module, ["add_reg", "not_registered_invite", "not_registered_reminder", "app_invitation_not_sent", "app_invitation_sent"]) && (
                                                        <React.Fragment>
                                                          <ReactSVG
                                                            wrapper="span"
                                                            src={require('img/ico-alert.svg')}
                                                          />
                                                          {
                                                            (() => {
                                                              if (in_array(this.state.module, ["add_reg"]))
                                                                return `${formatString(t('ATTENDEE_SELECTED_GUESTS_ALERT'), this.state.modules.add_reg)}`
                                                              else if (in_array(this.state.module, ["not_registered_invite", "not_registered_reminder"]))
                                                                return `${formatString(t('ATTENDEE_SELECTED_GUESTS_ALERT'), this.state.modules.not_registered)}`
                                                              else if (this.state.module === "app_invitation_not_sent")
                                                                return `${formatString(t('ATTENDEE_SELECTED_GUESTS_ALERT'), this.state.modules.app_invitations_not_sent)}`
                                                              else if (this.state.module === "app_invitation_sent")
                                                                return `${formatString(t('ATTENDEE_SELECTED_GUESTS_ALERT'), this.state.modules.app_invitations)}`
                                                            })()
                                                          }
                                                        </React.Fragment>
                                                      )}
                                                    </React.Fragment>
                                                  ) : (
                                                      <React.Fragment>
                                                        <ReactSVG
                                                          wrapper="span"
                                                          src={require('img/ico-alert.svg')}
                                                        />
                                                        {formatString(t('ATTENDEE_SELECTED_GUESTS_SELECTED_ALERT'), selected_attendees_length)}
                                                      </React.Fragment>
                                                    )}
                                                </p>
                                              </React.Fragment>
                                            )}
                                          </div>
                                          <div className="col-6">
                                            {in_array(this.state.module, ["not_registered_invite", "not_registered_reminder", "add_reg"]) && (
                                              <div className="d-flex justify-content-end new-right-header new-panel-buttons right-header-generic">
                                                {this.state.module === "add_reg" && (
                                                  <button
                                                    onClick={this.handleAddElement}
                                                    className="btn_addNew"
                                                  >
                                                    <ReactSVG wrapper="span" className="icons" src={require('img/ico-plus-lg.svg')} />
                                                  </button>
                                                )}
                                                <button
                                                  onClick={this.importCSVFile}
                                                  className="btn_csvImport"
                                                >
                                                  <ReactSVG wrapper="span" className="icons" src={require('img/ico-csvimport-lg.svg')} />
                                                </button>
                                                {this.state.attendees.length > 0 ? (
                                                  <React.Fragment>
                                                    {this.state.module === "add_reg" ? (
                                                      <div className="parctical-button-panel export-panel-top">
                                                        <div className="dropdown">
                                                          <button
                                                            style={{ marginLeft: '12px' }}
                                                            onClick={this.handleDropdown.bind(this)}
                                                            className="btn"
                                                          >
                                                            <i className="icons">
                                                              <ReactSVG
                                                                wrapper="span"
                                                                src={require("img/dots.svg")}
                                                              />
                                                            </i>
                                                          </button>
                                                          <div className="dropdown-menu">
                                                            <button
                                                              className="dropdown-item"
                                                              onClick={() => this.exportAttendees()}
                                                            >
                                                              {t("G_EXPORT")}
                                                            </button>
                                                          </div>
                                                        </div>
                                                      </div>
                                                    ) : (
                                                        <div className="new-right-header">
                                                          <button
                                                            onClick={
                                                              () =>
                                                                this.exportAttendees()
                                                            } 
                                                            className="btn_csvImport"
                                                          >
                                                            <ReactSVG wrapper="span" className="icons" src={require('img/export-csv.svg')} />
                                                          </button>
                                                        </div>
                                                      )}
                                                  </React.Fragment>
                                                ) : ''}
                                              </div>
                                            )}
                                          </div>
                                        </div>
                                      </header>
                                      <div style={{minHeight: '1px', height: 'auto'}} className="attendee-management-section">
                                        {this.state.displayElement && (
                                          <InviteAttendee
                                            listing={this.attendees}
                                            datacancel={this.handleCancel}
                                          />
                                        )}
                                      </div>
                                      <div style={{ height: 'auto' }}>
                                        <div style={{ marginTop: '20px', marginBottom: 0 }} className="row d-flex align-items-center">
                                          <div className="col-6">
                                            <div style={{ marginTop: '0', marginBottom: 0 }} className="new-header">
                                              <input
                                                style={{ width: '100%', maxWidth: '390px' }}
                                                value={this.state.query}
                                                name="query"
                                                type="text"
                                                placeholder={t("G_SEARCH")}
                                                onChange={this.onFieldChange.bind(this)}
                                              />
                                            </div>
                                          </div>
                                          {this.state.attendees.length > 0 && <div className="col-6">
                                            <div className="panel-right-table d-flex justify-content-end">
                                              {!this.state.isLarge && <button onClick={() => this.setState({ isLarge: true })} className="btn btn-fullscreen">
                                                <ReactSVG wrapper="span" className="icons" src={require('img/fullscreen.svg')} />
                                              </button>}
                                              <div className="parctical-button-panel">
                                                <div className="dropdown">
                                                  <button
                                                    onClick={this.handleDropdown.bind(this)}
                                                    className="btn"
                                                  >
                                                    {this.state.limit}
                                                    <i className="material-icons">
                                                      keyboard_arrow_down
                                                                  </i>
                                                  </button>
                                                  <div className="dropdown-menu">
                                                    {this.state.limit !== 10 && (
                                                      <button className="dropdown-item" onClick={this.handleLimit(10)}>
                                                        10
                                                      </button>
                                                    )}
                                                    {this.state.limit !== 20 && (
                                                      <button className="dropdown-item" onClick={this.handleLimit(20)}>
                                                        20
                                                      </button>
                                                    )}
                                                    {this.state.limit !== 50 && (
                                                      <button className="dropdown-item" onClick={this.handleLimit(50)}>
                                                        50
                                                      </button>
                                                    )}
                                                    {this.state.limit !== 500 && (
                                                      <button className="dropdown-item" onClick={this.handleLimit(500)}>
                                                        500
                                                      </button>
                                                    )}
                                                    {this.state.limit !== 1000 && (
                                                      <button className="dropdown-item" onClick={this.handleLimit(1000)}>
                                                        1000
                                                      </button>
                                                    )}
                                                  </div>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                          }
                                        </div>
                                        <div
                                          style={{ paddingTop: "10px" }}
                                          className="attendee-management-section"
                                        >
                                          {this.state.attendees.length > 0 && (
                                            <React.Fragment>
                                              <div
                                                style={{ minHeight: "1px", paddingTop: 0 }}
                                                className={`hotel-management-records attendee-records-template invitation-list ${in_array(this.state.action, ["send_by_email_all"]) && 'checkedallgray'}`}>
                                                <header className="header-records row d-flex">
                                                  <div className="col-2 d-flex">
                                                    <div className="header-invitations">
                                                      <label>
                                                        <input
                                                          id="selectall"
                                                         /*  disabled={in_array(this.state.action, ["send_by_email_all"]) ? true : false} */
                                                          checked={in_array(this.state.action, ["send_by_email_all"]) || (selected_attendees_length === this.state.attendees.length ? true :false)}
                                                          onChange={this.handleSelectAll.bind(this)}
                                                          type="checkbox"
                                                          name="selectall"
                                                        />
                                                        <span style={{ height: '21px', paddingLeft: '21px', marginLeft: 0 }}></span>
                                                      </label>
                                                      {in_array(this.state.module, ["not_registered_invite", "not_registered_reminder", "add_reg"]) && (
                                                        <div style={{ marginLeft: 0 }} className="parctical-button-panel">
                                                          <div className="dropdown">
                                                            <button
                                                              onClick={this.handleDropdown.bind(this)}
                                                              className="btn"
                                                            >
                                                              <i className="material-icons">
                                                                keyboard_arrow_down
                                                        </i>
                                                            </button>
                                                            <div className="dropdown-menu leftAlign">
                                                              {selected_attendees_length > 0 && (
                                                                <button
                                                                  className="dropdown-item"
                                                                  onClick={() =>
                                                                    this.handleDeleteElement("selected")
                                                                  }
                                                                >
                                                                  {t("G_DELETE_SELECTED")}
                                                                </button>
                                                              )}
                                                              <button
                                                                className="dropdown-item"
                                                                onClick={() =>
                                                                  this.handleDeleteElement("all")
                                                                }
                                                              >
                                                                {t("G_DELETE_ALL")}
                                                              </button>
                                                            </div>
                                                          </div>
                                                        </div>
                                                      )}
                                                    </div>
                                                    <strong>{t('ATTENDEE_FIRST_NAME')}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                                                      {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                  </div>
                                                  <div className="col-2">
                                                    <strong>{t('ATTENDEE_LAST_NAME')}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="last_name" onClick={this.onSorting} className="material-icons">
                                                      {(this.state.order_by === "ASC" && this.state.sort_by === "last_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "last_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                  </div>
                                                  <div className="col-2 col-phone">
                                                    <strong>{t('ATTENDEE_PHONE')} </strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="phone" onClick={this.onSorting} className="material-icons">
                                                      {(this.state.order_by === "ASC" && this.state.sort_by === "phone" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "phone" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                  </div>
                                                  <div className="col-2">
                                                    <strong>{t('ATTENDEE_EMAIL')} </strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                      {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                  </div>
                                                  <div className="col-2">
                                                    <strong>{t('ATTENDEE_CPR')} </strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                      {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                  </div>
                                                  <div className="col-2 text-right col-last">
                                                    <span className="total-counter">
                                                      {`${this.state.attendees.length} / ${this.state.total} ${t('ATTENDEE_GUESTS')}`}
                                                    </span>
                                                  </div>
                                                </header>
                                                <Attendees data={this.state.attendees} />
                                              </div>
                                            </React.Fragment>
                                          )}
                                          <div style={{ marginTop: '10px' }} className="row">
                                            <div className="col-6">
                                              {this.state.attendees.length > 0 && (
                                                <span className="total-counter">
                                                  {`${this.state.from} - ${this.state.to} ${t('G_OF')} ${this.state.total}`}
                                                  &nbsp;({t('G_SELECTED')}&nbsp;
                                                  {in_array(this.state.action, ["send_by_email_all"]) ? (
                                                    <React.Fragment>
                                                      {
                                                        (() => {
                                                          if (in_array(this.state.module, ["add_reg"]))
                                                            return this.state.modules.add_reg
                                                          else if (in_array(this.state.module, ["not_registered_invite", "not_registered_reminder"]))
                                                            return this.state.modules.not_registered
                                                          else if (this.state.module === "app_invitation_not_sent")
                                                            return this.state.modules.app_invitations_not_sent
                                                          else if (this.state.module === "app_invitation_sent")
                                                            return this.state.modules.app_invitations
                                                        })()
                                                      }
                                                    </React.Fragment>
                                                  ) : selected_attendees_length}
                                                  )
                                                </span>
                                              )}
                                            </div>
                                            <div className="col-6">
                                              {this.state.total > this.state.limit && (
                                                <React.Fragment>
                                                  <nav
                                                    className="page-navigation"
                                                    aria-label="navigation"
                                                  >
                                                    <Pagination
                                                      hideFirstLastPages={true}
                                                      prevPageText="keyboard_arrow_left"
                                                      linkClassPrev="material-icons"
                                                      nextPageText="keyboard_arrow_right"
                                                      linkClassNext="material-icons"
                                                      innerClass="pagination"
                                                      itemClass="page-item"
                                                      linkClass="page-link"
                                                      activePage={this.state.activePage}
                                                      itemsCountPerPage={this.state.limit}
                                                      totalItemsCount={this.state.total}
                                                      pageRangeDisplayed={5}
                                                      onChange={this.handlePageChange}
                                                    />
                                                  </nav>
                                                </React.Fragment>
                                              )}
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </React.Fragment>
                                  )}
                                </div>
                              )}
                          </div>
                          <div className="bottom-component-panel clearfix">
                            <React.Fragment>
                              <button
                                onClick={this.goStep(this.state.step - 1)}
                                className="btn btn-prev-step"
                              >
                                <span className="material-icons">keyboard_backspace</span>
                              </button>
                              <button style={{ marginLeft: '15px' }} data-type="save-next" disabled={this.state.isLoader ? true : false} className="btn btn-save-next" onClick={this.save}>{this.state.isLoader === "save-next" ?
                                <span className="spinner-border spinner-border-sm"></span> : t('G_SAVE_NEXT')}
                              </button>
                            </React.Fragment>
                          </div>
                        </React.Fragment>
                      )}
                      {this.state.step === 3 && (
                        <React.Fragment>
                          <div style={{ height: '100%' }}>
                            {this.state.template && (
                              <React.Fragment>
                                <h1
                                  style={{ whiteSpace: "nowrap" }}
                                  className="section-title"
                                >
                                  {t('ATTENDEE_PREVIEW_EMAIL_HEADING')}
                                </h1>
                                {this.state.alias && (
                                  <h4 style={{ marginBottom: "10px" }} className="component-heading">{bottomHeading}</h4>
                                )}
                                <h6 className="text-right" style={{ marginBottom: "10px" }}>
                                  <a style={{ cursor: 'pointer' }} onClick={this.sendTestEmail}>{t('ATTENDEE_SEND_TEST_EMAIL')}</a>
                                </h6>
                                <iframe
                                  title="template"
                                  frameBorder="0"
                                  width="100%"
                                  height="350px"
                                  id="template"
                                ></iframe>
                              </React.Fragment>
                            )}
                            {this.state.sms && (
                              <React.Fragment>
                                <h4 style={{ marginBottom: "10px", marginTop: "20px" }}>
                                  {t('ATTENDEE_INVITE_SMS_TEMPLATE')}
                                </h4>
                                <iframe
                                  title="sms"
                                  frameBorder="0"
                                  width="100%"
                                  height="80px"
                                  id="sms"
                                ></iframe>
                              </React.Fragment>
                            )}
                          </div>
                          <div className="bottom-component-panel clearfix">
                            <button
                              onClick={this.goStep(this.state.step - 1)}
                              className="btn btn-prev-step"
                            >
                              <span className="material-icons">keyboard_backspace</span>
                            </button>
                            {this.state.step && (
                              <button
                                onClick={this.goStep(this.state.step + 1)}
                                className="btn btn-next-step"
                              >
                                {this.state.step === 3
                                  ? t("G_SEND")
                                  : this.state.step === 4
                                    ? t('ATTENDEE_INVITATION_DONE')
                                    : t('G_NEXT')}
                              </button>
                            )}
                          </div>
                        </React.Fragment>
                      )}
                      {this.state.step === 4 && (
                        <React.Fragment>
                          <div style={{ height: '100%' }}> </div>
                          <div className="bottom-component-panel clearfix">
                            {this.state.step && (
                              <button
                                onClick={this.goStep(this.state.step + 1)}
                                className="btn btn-next-step"
                              >
                                {this.state.step === 3
                                  ? t("G_SEND")
                                  : this.state.step === 4
                                    ? t('ATTENDEE_INVITATION_DONE')
                                    : t('G_NEXT')}
                              </button>
                            )}
                          </div>
                        </React.Fragment>
                      )}
                    </React.Fragment>
                  )}
              </React.Fragment>
            )}
          </div>
        )
        }
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { invitation, event, update } = state;
  return {
    invitation, event, update
  };
}

export default connect(mapStateToProps)(withTranslation()(InvitationProcess));