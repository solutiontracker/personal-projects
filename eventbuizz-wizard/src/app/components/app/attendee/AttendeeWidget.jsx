import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import { ReactSVG } from 'react-svg';
import Img from 'react-image';
import FormWidget from '@/app/attendee/forms/FormWidget';
import ImportCSV from '@/app/forms/ImportCSV';
import { AttendeeService } from 'services/attendee/attendee-service';
import Pagination from "react-js-pagination";
import Loader from '@/app/forms/Loader';
import { confirmAlert } from 'react-confirm-alert'; // Import
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import { Translation } from "react-i18next";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { connect } from 'react-redux';
import { service } from 'services/service';
import DropDown from '@/app/forms/DropDown';

const in_array = require("in_array");

const labelArray = [
  {
    name: '-1',
    value: 'Do not map this field'
  },
  {
    name: 'initial',
    value: 'Initial'
  },
  {
    name: 'first_name',
    value: 'First Name'
  },
  {
    name: 'last_name',
    value: 'Last Name'
  },
  {
    name: 'title',
    value: 'Title'
  },
  {
    name: 'company_name',
    value: 'Company Name'
  },
  /* {
    name: 'about',
    value: 'About Me'
  },
  {
    name: 'industry',
    value: 'Industry'
  }, */
  {
    name: 'email',
    value: 'Email'
  },
  /* {
    name: 'website',
    value: 'Website'
  },
  {
    name: 'facebook',
    value: 'Facebook'
  },
  {
    name: 'twitter',
    value: 'Twitter'
  },
  {
    name: 'linkedin',
    value: 'LinkedIn'
  },
  {
    name: 'country',
    value: 'Country ISO'
  },
  {
    name: 'organization',
    value: 'Organization'
  },
  {
    name: 'jobs',
    value: 'Job Tasks'
  },
  {
    name: 'interests',
    value: 'Interests'
  },
  {
    name: 'age',
    value: 'Age'
  },
  {
    name: 'gender',
    value: 'Gender'
  },*/
  {
    name: 'country_code',
    value: 'Country code'
  }, 
  {
    name: 'phone',
    value: 'Phone'
  },
  {
    name: 'allow_vote',
    value: 'Voting Permissions'
  },
  /* {
    name: 'group_id',
    value: 'Group Id'
  },
  {
    name: 'organizer_id',
    value: 'Organizer Id'
  },*/
  {
    name: 'department',
    value: 'Department'
  },
  /*{
    name: 'custom_field_id',
    value: 'Custom Field'
  },
  {
    name: 'allow_gallery',
    value: 'Image Gallery'
  }, */
  {
    name: 'ask_to_apeak',
    value: 'Ask to speak'
  },
  {
    name: 'attende_type',
    value: 'Attendee Type'
  },
  {
    name: 'network_group',
    value: 'Network Group'
  },
  {
    name: 'delegate_number',
    value: 'Delegate Number'
  },
  {
    name: 'gdpr',
    value: 'GDPR'
  },
  {
    name: 'table_number',
    value: 'Table Number'
  },
  /* {
    name: 'FIRST_NAME_PASSPORT',
    value: 'First Name (Passport)'
  },
  {
    name: 'LAST_NAME_PASSPORT',
    value: 'Last Name (Passport)'
  },
  {
    name: 'BIRTHDAY_YEAR',
    value: 'Date of Birth'
  },
  {
    name: 'EMPLOYMENT_DATE',
    value: 'Employment Date'
  },
  {
    name: 'SPOKEN_LANGUAGE',
    value: 'Languages'
  }, */
  {
    name: 'ss_number',
    value: 'Social security number'
  },
  /* {
    name: 'attendee_type_id',
    value: 'Attendee type id'
  },
  {
    name: 'attendee_type',
    value: 'Attendee type'
  },
  {
    name: 'type_resource',
    value: 'Type Resource'
  },
  {
    name: 'allow_my_document',
    value: 'Allow Document'
  } */
];

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class AttendeeWidget extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      query: '',
      sort_by: 'first_name',
      order_by: 'ASC',
      attendees: [],
      attendee_types: [],
      attendee_type:0,
      displayElement: true,
      editElement: false,
      editElementIndex: undefined,
      toggleList: false,
      importCSVcontainer: false,

      //pagination
      limit: 10,
      total: '',
      from: 0,
      to: 0,
      activePage: 1,

      //errors & loading
      preLoader: true,

      typing: false,
      typingTimeout: 0,

      message: false,
      success: true,

      checkedItems: new Map()
    }

    this.onSorting = this.onSorting.bind(this);
  }

  componentDidMount() {
    this._isMounted = true;
    this.listing();

    //set next previous
    if (this.props.event.modules !== undefined && this.props.event.modules.length > 0) {
      let modules = this.props.event.modules.filter(function (module, i) {
        return ((!in_array(module.alias, ["polls"]) && Number(module.status) === 1) || (in_array(module.alias, ["subregistration"])));
      });

      let index = modules.findIndex(function (module, i) {
        return module.alias === "attendees";
      });

      this.setState({
        next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
        prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
      });

    }
    document.body.addEventListener('click', this.removePopup.bind(this));
  }

  removePopup = e => {
    if (e.target.className !== 'btn active') {
      const items = document.querySelectorAll(".parctical-button-panel .btn");
      for (let i = 0; i < items.length; i++) {
        const element = items[i];
        element.classList.remove("active");
      }
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  componentDidUpdate(prevProps, prevState) {
    const { order_by, sort_by } = this.state;
    if (order_by !== prevState.order_by || sort_by !== prevState.sort_by || prevState.limit !== this.state.limit) {
      this.listing(1);
    }
  }

  handlePageChange = (activePage) => {
    this.listing(activePage);
  }

  listing = (activePage = 1, loader = false, type = "save", message = "", success = true) => {
    this.setState({ preLoader: (!loader ? true : false) });
    AttendeeService.listing(activePage, this.state)
      .then(
        response => {
          if (response.success) {
            if (this._isMounted) {
              this.setState({
                success: success,
                attendees: response.data.data,
                attendee_types: response.attendee_types,
                activePage: response.data.current_page,
                total: response.data.total,
                from: response.data.from,
                to: response.data.to,
                editElement: false,
                displayElement: (type === "save-new" ? true : false),
                preLoader: false,
                message: message,
                checkedItems: new Map()
              });
            }
          }
        },
        error => { }
      );
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
        self.listing(1)
      }, 1000)
    });
  }
  onAttendeTypeChange= (item, id,type) => {
   
    if (item && item.length > 0 && id && id!='select') {
      let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.attendee_type : 'Select attendee type');
     
    }else{
      console.log(id)
      return 'Select attendee type';
    }
  }
  handleEditElement = (index) => {
    this.setState({
      editElement: true,
      editElementIndex: index,
      displayElement: false,
    });
  }

  handleDeleteElement = id => {
    if (id === "selected" && this.state.checkedItems.size > 0) {
      let ids = [];
      this.state.checkedItems.forEach((value, key, map) => {
        if (value === true) {
          ids.push(key);
        }
      });
      this.deleteRecords(id, ids);
    } else if (id !== "selected") {
      this.deleteRecords(id);
    }
  }

  deleteRecords(id, ids = []) {
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
                        service
                          .destroy(
                            `${process.env.REACT_APP_URL}/attendee/destroy/${id}`,
                            { ids: ids }
                          )
                          .then(
                            response => {
                              this.listing(1, false, "save", response.message, response.success ? true: false);
                            },
                            error => {
                            }
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

  handleCancel = () => {
    this.setState({
      editElement: false,
      editElementIndex: undefined,
      displayElement: false
    });
  }

  handleAddElement = () => {
    this.setState({
      displayElement: true,
    });
  }

  importCSVFile = () => {
    this.setState({
      importCSVcontainer: true,
    });
  }

  handleClose = () => {
    this.setState({
      importCSVcontainer: false,
    });
    this.listing(1, false);
  }

  onSorting(event) {
    this.setState({
      order_by: event.target.attributes.getNamedItem('data-order').value,
      sort_by: event.target.attributes.getNamedItem('data-sort').value,
    });
  }

  handleSelectAll = e => {
    const check = e.target.checked;
    const checkitems = document.querySelectorAll(".check-box-list input");
    for (let i = 0; i < checkitems.length; i++) {
      const element = checkitems[i];
      this.setState(prevState => ({
        checkedItems: prevState.checkedItems.set(element.name, check)
      }));
    }
  };

  handleCheckbox = e => {
    const checkitems = document.querySelectorAll(".check-box-list input");
    const selectall = document.getElementById("selectall");
    for (let i = 0; i < checkitems.length; i++) {
      const element = checkitems[i].checked;
      if (element === false) {
        selectall.checked = false;
        break;
      } else {
        selectall.checked = true;
      }
    }
    const item = e.target.name;
    const isChecked = e.target.checked;
    this.setState(prevState => ({
      checkedItems: prevState.checkedItems.set(item, isChecked)
    }));
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

  handleLimit = (limit) => e => {
    this.setState(prevState => ({
      limit: limit,
    }));
  };
  handleAttendeType = (input) => e => {
    const self = this;
    if (self.state.typingTimeout) {
      clearTimeout(self.state.typingTimeout);
    }
    self.setState({
      attendee_type: e.value,
      typingTimeout: setTimeout(function () {
        self.listing(1)
      }, 1000)
    });
  }
  render() {

    const selected_rows_length = new Map(
      [...this.state.checkedItems]
        .filter(([k, v]) => v === true)
    ).size;

    let module = this.props.event.modules.filter(function (module, i) {
      return in_array(module.alias, ["attendees"]);
    });

    const AttendeesRecords = ({ data }) => {
      return (
        <React.Fragment>
          <Translation>
            {
              t =>
                <header className="header-records row d-flex">
                  <div className="col-1 d-flex">
                    <div className="header-invitations">
                      <label>
                        <input
                          id="selectall"
                          checked={(selected_rows_length === this.state.attendees.length ? true : false)}
                          onChange={this.handleSelectAll.bind(this)}
                          type="checkbox"
                          name="selectall"
                        />
                        <span style={{ height: '21px', paddingLeft: '21px', marginLeft: 0 }}></span>
                      </label>
                      <div style={{ marginLeft: 0 }} className="parctical-button-panel">
                        <div className="dropdown">
                          <button
                            onClick={this.handleDropdown.bind(this)}
                            className="btn"
                          >
                            <i className="material-icons">keyboard_arrow_down</i>
                          </button>
                          <div className="dropdown-menu leftAlign">
                            {selected_rows_length > 0 && (
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
                    </div>
                  </div>
                  <div className="col-2 col-flex-10">
                    <strong>{t('ATTENDEE_COMPANY')}</strong>
                  </div>
                  <div className="col-2 col-flex-10">
                    <strong>{t('ATTENDEE_FIRST_NAME')}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                  <div className="col-2 col-flex-10">
                    <strong>{t('ATTENDEE_LAST_NAME')}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="last_name" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "last_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "last_name" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                  <div className="col-2 col-flex-10">
                    <strong>{t('ATTENDEE_JOB_TITLE')}</strong>
                  </div>
                  <div className="col-2 col-flex-12">
                    <strong>{t('ATTENDEE_EMAIL')}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                  <div className="col-2 col-flex-10">
                    <strong>{t('ATTENDEE_DEPARTMENT')}</strong>
                  </div>
                  <div className="col-2 col-flex-10">
                    <strong>{t('ATTENDEE_DELEGATE_NUMBER')}</strong>
                  </div>
                  <div className="col-2 col-flex-12">
                  </div>
                  <div style={{flex: '0 0 7%',maxWidth: '7%'}}  className="col-1 text-right">
                    
                  </div>
                </header>
            }
          </Translation>
          {data.map((data, key) => {
            return (
              <Translation key={key}>
                {
                  t =>
                    <div className={`${this.state.editElement && this.state.editElement && this.state.editElementIndex === key ? "no-hover row d-flex align-items-center" : "row d-flex align-items-center"} ${this.state.checkedItems.get(data.id.toString()) ? 'check' : ''}`} key={key}>
                      <div className="col-1 check-box-list">
                        <label className={`checkbox-label`}>
                          <input
                            type="checkbox"
                            name={data.id.toString()}
                            checked={(this.state.checkedItems.get(
                              data.id.toString()
                            ))}
                            onChange={this.handleCheckbox}
                          />
                          <em></em>
                        </label>
                      </div>
                      <div className="col-2 col-flex-10">
                        <p>{data.attendee_detail.company_name ? data.attendee_detail.company_name : '-'}</p>
                      </div>
                      <div className="col-2 col-flex-10">
                        {data.first_name && <p>{data.first_name}</p>}
                      </div>
                      <div className="col-2 col-flex-10">
                        {data.last_name && <p>{data.last_name}</p>}
                      </div>
                      <div className="col-2 col-flex-10">
                        {data.attendee_detail.title && <p>{data.attendee_detail.title}</p>}
                      </div>
                      <div className="col-2 col-flex-12">
                        {data.email && <p>{data.email}</p>}

                      </div>
                      <div className="col-2 col-flex-10">
                        {data.attendee_detail.department && <p>{data.attendee_detail.department}</p>}
                      </div>
                      <div className="col-2 col-flex-10">
                        {data.attendee_detail.delegate_number &&
                          <p>{data.attendee_detail.delegate_number ? data.attendee_detail.delegate_number : '-'}</p>}
                      </div>
                      <div className="col-2 col-flex-12">
                        <ul className="panel-actions text-left">
                          <li><span><i className="icons"><ReactSVG wrapper="span" src={data.event.allow_vote ? require("img/ico-check-box.svg") : require("img/ico-check-box-gray.svg")} /></i></span></li>
                          <li><span><i className="icons"><ReactSVG wrapper="span" src={data.event.ask_to_apeak ? require("img/ico-privacy.svg") : require("img/ico-privacy-gray.svg")} /></i></span></li>
                          <li><span><i className="icons"><ReactSVG wrapper="span" src={data.attendee_detail.phone ? require("img/ico-telephone.svg") : require("img/ico-telephone-gray.svg")} /></i></span></li>
                        </ul>
                      </div>
                      <div style={{flex: '0 0 7%',maxWidth: '7%'}} className="col-1 text-right">
                        <ul className="panel-actions">
                          <li><span onClick={() => this.handleEditElement(key)}><i className="icons"><ReactSVG wrapper="span" src={require("img/ico-edit-gray.svg")} /></i></span></li>
                          <li><span onClick={() => this.handleDeleteElement(data.id)}><i className="icons"><ReactSVG wrapper="span" src={require("img/ico-delete-gray.svg")} /></i></span></li>
                        </ul>
                      </div>
                      {this.state.editElement && !this.state.displayElement && this.state.editElementIndex === key ? <FormWidget listing={this.listing} editdata={data} editdataindex={key} datacancel={this.handleCancel}  attendee_types={this.state.attendee_types} /> : ''}
                    </div>
                }
              </Translation>
            )
          })}
        </React.Fragment>
      )
    }

    return (
      <Translation>
        {
          t =>
            <div>
              {this.state.importCSVcontainer !== false ? (
                <ImportCSV
                  apiUrl={`${process.env.REACT_APP_URL}/general/import/attendees`}
                  downloadFile='/samples/attendee_template.csv'
                  onClick={this.handleClose}
                  element={labelArray}
                  validate={['first_name', 'email']}
                  compName={t('ATTENDEE_NAME')}
                />
              ) : (
                  <div className="wrapper-content third-step">
                    {this.state.preLoader &&
                      <Loader />
                    }
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
                        <header style={{ margin: 0 }} className="new-header clearfix">
                          <div className="row">
                            <div className="col-12">
                              <h1 className="section-title float-left">{(module.length > 0 && module[0]['value'] !== undefined ? module[0]['value'] : t('ATTENDEE_NAME'))}</h1>
                              <div className="new-right-header new-panel-buttons float-right">
                                <button onClick={this.handleAddElement} className="btn_addNew">
                                  <Img width="20px" src={require('img/ico-plus-lg.svg')} />
                                </button>
                                <button onClick={this.importCSVFile} className="btn_csvImport">
                                  <Img width="30px" src={require('img/ico-csvimport-lg.svg')} />
                                </button>
                              </div>
                            </div>
                            <div className="col-6">
                              <p>{t('ATTENDEE_FORM_SUB_HEADING')} </p>
                            </div>
                          </div>
                        </header>
                        <div className="attendee-management-section attendee-form-modifications">
                          {this.state.displayElement ? (
                            <div style={{ marginTop: '0px', marginBottom: '15px' }} >
                              <FormWidget listing={this.listing} datacancel={this.handleCancel} attendee_types={this.state.attendee_types} />
                            </div>
                          ) : ''}
                          <div style={{ marginTop: '0px', marginBottom: 0 }} className="new-header">
                            <div style={{ marginTop: '0px', marginBottom: 0 }} className="row d-flex align-items-center">
                              <div className="col-6">
                                <input style={{marginBottom: 10, height: 50}} value={this.state.query} name="query" type="text"
                                  placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                />
                              </div>
                              <div className="col-4">
                              {this.state.attendee_types.length > 0 && (
                                  <DropDown
                                    className=''
                                    label={false}
                                    listitems={this.state.attendee_types}
                                    selected={(this.state.attendee_type ? this.state.attendee_type : 'select')}
                                    selectedlabel={this.onAttendeTypeChange(this.state.attendee_types, (this.state.attendee_type ? this.state.attendee_type :'select'),'attendee_type')}
                                    onChange={this.handleAttendeType('attendee_type')}
                                    required={false}
                                    type='attendee_type'
                                  />
                                )}
                              </div>
                              {this.state.attendees.length > 0 && <div className="col-2">
                                <div className="panel-right-table d-flex justify-content-end">
                                  <div className="parctical-button-panel">
                                    <div className="dropdown">
                                      <button
                                        onClick={this.handleDropdown.bind(this)}
                                        className="btn"
                                        style={{ minWidth: '54px' }}
                                      >
                                        {this.state.limit}
                                        <i className="material-icons">keyboard_arrow_down</i>
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
                          </div>
                          {this.state.attendees.length > 0 ? (
                            <div className="hotel-management-records attendee-records-template">
                              <AttendeesRecords data={this.state.attendees} />
                            </div>
                          ) : ''}
                          <div style={{ marginTop: '10px' }} className="row">
                            <div className="col-6">
                              {this.state.attendees.length > 0 && (
                                <span className="total-counter">
                                  {`${this.state.from} - ${this.state.to} ${t('G_OF')} ${this.state.total} (${t('G_SELECTED')} ${selected_rows_length})`}
                                </span>
                              )}
                            </div>
                            <div className="col-6">
                              {this.state.total > this.state.limit && (
                                <nav className="page-navigation" aria-label="navigation">
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
                          {this.state.next !== undefined && (
                            <NavLink className="btn btn-next-step" to={this.state.next}>{t('G_NEXT')}</NavLink>
                          )}
                        </div>
                      </React.Fragment>
                    )}
                  </div>
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

export default connect(mapStateToProps)(AttendeeWidget);