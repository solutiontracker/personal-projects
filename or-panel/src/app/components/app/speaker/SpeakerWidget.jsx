import React, { Component } from "react";
import Img from 'react-image';
import { NavLink } from 'react-router-dom';
import { ReactSVG } from "react-svg";
import ImportCSV from "@/app/forms/ImportCSV";
import FormWidget from "@/app/speaker/forms/FormWidget";
import AttendeeFormWidget from '@/app/attendee/forms/FormWidget';
import { AttendeeService } from "services/attendee/attendee-service";
import Pagination from "react-js-pagination";
import Loader from "@/app/forms/Loader";
import { confirmAlert } from "react-confirm-alert"; // Import
import "react-confirm-alert/src/react-confirm-alert.css"; // Import css
import { Translation } from "react-i18next";
import AlertMessage from "@/app/forms/alerts/AlertMessage";
import { connect } from 'react-redux';
import { service } from 'services/service';

const in_array = require("in_array");

const labelArray = [
  {
    name: "-1",
    value: "Do not map this field"
  },
  {
    name: "initial",
    value: "Initial"
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
    name: "title",
    value: "Title"
  },
  {
    name: "company_name",
    value: "Company Name"
  },
  {
    name: "about",
    value: "About Me"
  },
  {
    name: "industry",
    value: "Industry"
  },
  {
    name: "email",
    value: "Email"
  },
  {
    name: "website",
    value: "Website"
  },
  {
    name: "facebook",
    value: "Facebook"
  },
  {
    name: "twitter",
    value: "Twitter"
  },
  {
    name: "linkedin",
    value: "LinkedIn"
  },
  {
    name: "country",
    value: "Country ISO"
  },
  {
    name: "organization",
    value: "Organization"
  },
  {
    name: "jobs",
    value: "Job Tasks"
  },
  {
    name: "interests",
    value: "Interests"
  },
  {
    name: "age",
    value: "Age"
  },
  {
    name: "gender",
    value: "Gender"
  },
  {
    name: "country_code",
    value: "Country code"
  },
  {
    name: "phone",
    value: "Phone"
  },
  {
    name: "allow_vote",
    value: "Voting Permissions"
  },
  {
    name: "group_id",
    value: "Group Id"
  },
  {
    name: "organizer_id",
    value: "Organizer Id"
  },
  {
    name: "department",
    value: "Department"
  },
  {
    name: "custom_field_id",
    value: "Drop Down"
  },
  {
    name: "allow_gallery",
    value: "Image Gallery"
  },
  {
    name: "ask_to_apeak",
    value: "Ask to Speak"
  },
  {
    name: "network_group",
    value: "Network Group"
  },
  {
    name: "delegate_number",
    value: "Delegate Number"
  },
  {
    name: "table_number",
    value: "Table Number"
  },
  {
    name: "FIRST_NAME_PASSPORT",
    value: "First Name (Passport)"
  },
  {
    name: "LAST_NAME_PASSPORT",
    value: "Last Name (Passport)"
  },
  {
    name: "BIRTHDAY_YEAR",
    value: "Date of Birth"
  },
  {
    name: "EMPLOYMENT_DATE",
    value: "Employment Date"
  },
  {
    name: "SPOKEN_LANGUAGE",
    value: "Languages"
  },
  {
    name: "ss_number",
    value: "Social security number"
  },
  {
    name: "attendee_type_id",
    value: "Attendee type id"
  },
  {
    name: "attendee_type",
    value: "Attendee type"
  },
  {
    name: "type_resource",
    value: "Type resource"
  },
  {
    name: "allow_my_document",
    value: "Allow document"
  }
];

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

class SpeakerWidget extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      query: "",
      sort_by: 'first_name',
      order_by: 'ASC',
      speakers: [],
      attendee_types: [],
      displayElement: false,
      editElement: false,
      editElementIndex: undefined,
      importCSVcontainer: false,

      //pagination
      limit: 10,
      total: null,
      from: 0,
      to: 0,
      activePage: 1,

      //for speakers
      speaker: 1,

      //errors & loading
      preLoader: true,

      typing: false,
      typingTimeout: 0,

      message: false,
      success: true,

      checkedItems: new Map(),
    };

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
        return module.alias === "speakers";
      });

      this.setState({
        next: (modules[index + 1] !== undefined && module_routes[modules[index + 1]['alias']] !== undefined  ? module_routes[modules[index + 1]['alias']] : "/event/manage/surveys"),
        prev: (modules[index - 1] !== undefined && module_routes[modules[index - 1]['alias']] !== undefined  ? module_routes[modules[index - 1]['alias']] : (Number(this.props.event.is_registration) === 1 ? "/event/module/eventsite-module-order" : (Number(this.props.event.is_app) === 1 ? "/event/module/event-module-order" : "/event_site/billing-module/manage-orders")))
      });

    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  componentDidUpdate(prevProps: any, prevState: any) {
    const { order_by, sort_by } = this.state;
    if (order_by !== prevState.order_by || sort_by !== prevState.sort_by || prevState.limit !== this.state.limit) {
      this.listing(1);
    }
  }

  handlePageChange = activePage => {
    this.listing(activePage);
  };

  listing = (activePage = 1, loader = false, type = "save") => {
    this.setState({ preLoader: !loader ? true : false });
    AttendeeService.listing(activePage, this.state).then(
      response => {
        if (response.success) {
          if (this._isMounted) {
            this.setState({
              speakers: response.data.data,
              attendee_types: response.attendee_types,
              activePage: response.data.current_page,
              total: response.data.total,
              from: response.data.from,
              to: response.data.to,
              editElement: false,
              displayElement: (type === "save-new" ? true : false),
              preLoader: false,
              checkedItems: new Map()
            });
          }
        }
      },
      error => { }
    );
  };

  onFieldChange(event) {
    const self = this;
    if (self.state.typingTimeout) {
      clearTimeout(self.state.typingTimeout);
    }
    self.setState({
      query: event.target.value,
      typing: false,
      typingTimeout: setTimeout(function () {
        self.listing(1);
      }, 1000)
    });
  }

  handleEditElement = index => {
    this.setState({
      editElement: true,
      editElementIndex: index,
      displayElement: false
    });
  };

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
                            { ids: ids, speaker: 1 }
                          )
                          .then(
                            response => {
                              if (response.success) {
                                this.listing(1, false);
                              } else {
                                this.setState({
                                  preLoader: false,
                                  'message': response.message,
                                  'success': false
                                });
                              }
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
    this.listing(1, false);
  };

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

  render() {

    const selected_rows_length = new Map(
      [...this.state.checkedItems]
        .filter(([k, v]) => v === true)
    ).size;

    let module = this.props.event.modules.filter(function (module, i) {
      return in_array(module.alias, ["speakers"]);
    });

    const Records = ({ data }) => {
      return (
        <React.Fragment>
          <Translation>
            {t => (
              <header className="header-records row d-flex speaker-grid">
                <div className="col-10 d-flex ">
                  <div className="col-1">
                    <div className="header-invitations">
                      <label>
                        <input
                          id="selectall"
                          checked={(selected_rows_length === this.state.speakers.length ? true : false)}
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
                            <i className="material-icons">
                              keyboard_arrow_down
                                                        </i>
                          </button>
                          <div className="dropdown-menu">
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
                  <div className="col-3">
                    <strong>{t('ATTENDEE_FIRST_NAME')}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                  <div className="col-3">
                    <strong>{t('ATTENDEE_LAST_NAME')}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="last_name" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "last_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "last_name" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                  <div className="col-3">
                    <strong>{t('ATTENDEE_EMAIL')}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                  <div className="col-2">
                    <strong>{t("SPEAKER_PROGRAM")}</strong>
                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="program_id" onClick={this.onSorting} className="material-icons">
                      {(this.state.order_by === "ASC" && this.state.sort_by === "program_id" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "program_id" ? "keyboard_arrow_up" : "unfold_more"))}
                    </i>
                  </div>
                </div>
                <div className="col-2"></div>
              </header>
            )}
          </Translation>
          {data.map((data, key) => {
            return (
              <Translation key={key}>
                {t => (
                  <div
                    className={`${
                      this.state.editElement &&
                        this.state.editElementIndex === key
                        ? "no-hover row d-flex speaker-grid"
                        : "row d-flex speaker-grid"
                      } ${this.state.checkedItems.get(data.id.toString()) ? 'check' : ''}`}
                    key={key}
                  >
                    <div className={`col-10 d-flex check-box-list`}>
                      <div className="col-1">
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
                      <div className="col-3">
                        <p>{data.first_name ? data.first_name : '-'}</p>
                      </div>
                      <div className="col-3">
                        <p>{data.last_name ? data.last_name : '-'}</p>
                      </div>
                      <div className="col-3">
                        {data.email && <p>{data.email}</p>}
                      </div>
                      <div className="col-3">
                        <p>
                          {data.program_detail && data.program_detail.map((row, key) => 
                            row.topic+(data.program_detail.length !== (key + 1) ? ', ' : '')
                          )}
                        </p>
                      </div>
                    </div>
                    <div className="col-2">
                      <ul className="panel-actions">
                        <li>
                          <span onClick={() => this.handleEditElement(key)}>
                            <i className="icons">
                              <ReactSVG
                                wrapper="span"
                                src={require("img/ico-edit-gray.svg")}
                              />
                            </i>
                          </span>
                        </li>
                        <li>
                          <span
                            onClick={() => this.handleDeleteElement(data.id)}
                          >
                            <i className="icons">
                              <ReactSVG
                                wrapper="span"
                                src={require("img/ico-delete-gray.svg")}
                              />
                            </i>
                          </span>
                        </li>
                      </ul>
                    </div>
                    {this.state.editElement && !this.state.displayElement && this.state.editElementIndex === key ? <AttendeeFormWidget listing={this.listing} editdata={data} editdataindex={key} datacancel={this.handleCancel} speaker={1} attendee_types={this.state.attendee_types} /> : ''}
                  </div>
                )}
              </Translation>
            );
          })}
        </React.Fragment>
      );
    };
    
    return (
      <Translation>
        {t => (
          <div>
            {this.state.importCSVcontainer !== false ? (
              <ImportCSV
                apiUrl={`${process.env.REACT_APP_URL}/general/import/attendees`}
                speaker="1"
                onClick={this.handleClose}
                element={labelArray}
                validate={["first_name", "email"]}
              />
            ) : (
                <div className="wrapper-content third-step">
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
                      <header style={{ marginTop: '0px', marginBottom: 0 }} className="new-header clearfix">
                        <div className="row">
                          <div className="col-12">
                            <h1 className="section-title float-left">
                              {(module[0]['value'] !== undefined ? module[0]['value'] : t('SPEAKER_NAME'))}
                            </h1>
                            <div className="new-right-header new-panel-buttons float-right">
                              <button
                                onClick={this.handleAddElement}
                                className="btn_addNew"
                              >
                                {/* <i className="material-icons">add</i>
                                {t("SPEAKER_ADD")} */}
                                <Img width="20px" src={require('img/ico-plus-lg.svg')} />
                              </button>
                              {/* <button onClick={this.importCSVFile} className="btn btn-import-csv">{t('G_IMPORT_CSV')}
                                                        </button> */}
                            </div>
                          </div>
                          <div className="col-6">
                            <p>{t('SPEAKER_SUB_HEADING')}</p>
                          </div>
                        </div>
                      </header>

                      <div className="attendee-management-section attendee-form-modifications">
                        {this.state.displayElement ? (
                          <div style={{ marginTop: '0px', marginBottom: '15px' }} >
                            <FormWidget
                              listing={this.listing}
                              datacancel={this.handleCancel}
                            />
                          </div>
                        ) : (
                            ""
                          )}
                        <div style={{ marginTop: '0px', marginBottom: 0 }} className="new-header">
                          <div style={{ marginTop: '0px', marginBottom: 0 }} className="row d-flex align-items-center">
                            <div className="col-6">
                              <input
                                value={this.state.query}
                                name="query"
                                type="text"
                                placeholder={t("G_SEARCH")}
                                onChange={this.onFieldChange.bind(this)}
                              />
                            </div>
                            {this.state.speakers.length > 0 && <div className="col-6">
                              <div className="panel-right-table d-flex justify-content-end">
                                <div className="parctical-button-panel">
                                  <div className="dropdown">
                                    <button
                                      onClick={this.handleDropdown.bind(this)}
                                      className="btn"
                                      style={{ minWidth: '54px' }}
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
                        </div>
                        {this.state.speakers.length > 0 ? (
                          <div className="hotel-management-records attendee-records-template speaker-section">
                            <Records data={this.state.speakers} />
                          </div>
                        ) : (
                            ""
                          )}
                        <div style={{ marginTop: '10px' }} className="row">
                          <div className="col-6">
                            {this.state.speakers.length > 0 && (
                              <span className="total-counter">
                                {`${this.state.from} - ${this.state.to} ${t('G_OF')} ${this.state.total} (${t('G_SELECTED')} ${selected_rows_length})`}
                              </span>
                            )}
                          </div>
                          <div className="col-6">
                            {this.state.total > this.state.limit && (
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
        )}
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { event, update } = state;
  return {
    event, update
  };
}

export default connect(mapStateToProps)(SpeakerWidget);