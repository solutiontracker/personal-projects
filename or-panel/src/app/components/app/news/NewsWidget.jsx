import React, { Component } from "react";
import { ReactSVG } from "react-svg";
import Img from "react-image";
import FormWidget from "@/app/news/forms/FormWidget";
import Pagination from "react-js-pagination";
import Loader from "@/app/forms/Loader";
import { confirmAlert } from "react-confirm-alert"; // Import
import "react-confirm-alert/src/react-confirm-alert.css"; // Import css
import { Translation } from "react-i18next";
import AlertMessage from "@/app/forms/alerts/AlertMessage";
import { connect } from "react-redux";
import { service } from "services/service";
class AttendeeWidget extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      sort_by: "first_name",
      order_by: "ASC",
      records: [],
      displayElement: true,
      editElement: false,
      editElementIndex: undefined,
      toggleList: false,
      attendeeTypes:[],
      //pagination
      limit: 10,
      total: "",
      from: 0,
      to: 0,
      activePage: 1,

      //errors & loading
      preLoader: true,

      typing: false,
      typingTimeout: 0,

      message: false,
      success: true,
    };
  }

  componentDidMount() {
    this._isMounted = true;
    this.listing();
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  componentDidUpdate(prevProps, prevState) {
    const { order_by, sort_by } = this.state;
    if (
      order_by !== prevState.order_by ||
      sort_by !== prevState.sort_by ||
      prevState.limit !== this.state.limit ||
      (prevState.displayElement !== this.state.displayElement &&
        this.state.displayElement === false) ||
      (prevState.editElement !== this.state.editElement &&
        this.state.editElement === false)
    ) {
      this.listing(1);
    }
  }

  handlePageChange = (activePage) => {
    this.listing(activePage);
  };

  listing = (activePage = 1, loader = false) => {
    this.setState({ preLoader: !loader ? true : false });
    service.post(`${process.env.REACT_APP_URL}/alert/listing`, this.state).then(
      (response) => {
        if (response.success) {
          if (this._isMounted) {
            this.setState({
              records: response.data.records.data,
              groups: response.data.groups,
              attendeeTypes: response.data.attendeeTypes.map((type, i)=>{
                return { "name" : type.attendee_type, "id" : type.id }
              }),
              attendees: response.data.attendees,
              activePage: response.data.records.current_page,
              total: response.data.records.total,
              from: response.data.records.from,
              to: response.data.records.to,
              editElement: false,
              displayElement: false,
              preLoader: false,
            });
            console.log(this.state.attendeeTypes);
          }
        }
      },
      (error) => { }
    );
  };

  handleEditElement = (index) => {
    this.setState({
      editElement: true,
      editElementIndex: index,
      displayElement: false,
    });
  };

  handleDeleteElement = (id) => {
    this.deleteRecord(id);
  };

  deleteRecord(id) {
    confirmAlert({
      customUI: ({ onClose }) => {
        return (
          <Translation>
            {(t) => (
              <div className="app-main-popup">
                <div className="app-header">
                  <h4>{t("G_DELETE")}</h4>
                </div>
                <div className="app-body">
                  <p>{t("EE_ON_DELETE_ALERT_MSG")}</p>
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
                          `${process.env.REACT_APP_URL}/alert/destroy/${id}`
                        )
                        .then(
                          (response) => {
                            if (response.success) {
                              this.listing(1, false);
                            } else {
                              this.setState({
                                message: response.message,
                                success: false,
                              });
                            }
                          },
                          (error) => { }
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
      },
    });
  }

  handleCancel = () => {
    this.setState({
      editElement: false,
      editElementIndex: undefined,
      displayElement: false,
    });
  };

  handleAddElement = () => {
    this.setState({
      displayElement: true,
    });
  };

  render() {
    const Records = ({ data }) => {
      return (
        <React.Fragment>
          {!this.state.editElement && (
            <Translation>
              {(t) => (
                <header className="header-records row d-flex">
                  <div className="col-6">
                    <strong>{t("NS_MESSAGE")}</strong>
                  </div>
                  <div className="col-2 text-center">
                    <strong>{t("NS_SEND_STATUS")}</strong>
                  </div>
                  <div className="col-2">
                    <strong>{t("NS_SEND_DATE")}</strong>
                  </div>
                  <div className="col-2 text-center">
                    <strong>{t("NS_MESSAGE_TYPE")}</strong>
                  </div>
                  <div className="col-2"></div>
                </header>
              )}
            </Translation>
          )}
          {data.map((data, key) => {
            return (
              <Translation key={key}>
                {(t) => (
                  <React.Fragment>
                    {!this.state.editElement && (
                      <div
                        className={`${
                          this.state.editElement &&
                            this.state.editElement &&
                            this.state.editElementIndex === key
                            ? "no-hover row d-flex align-items-center"
                            : "row d-flex align-items-center"
                          }`}
                        key={key}
                      >
                        <div className="col-6">
                          <p><strong>{data.info.title && data.info.title}</strong></p>
                          <p>
                            {data.info.description && data.info.description}
                          </p>
                        </div>
                        <div className="col-2 text-center hover-icons-news">
                          {data.status === 2 &&
                            <i>
                              <ReactSVG
                                wrapper="span"
                                src={require("img/ico-circle-check.svg")}
                              />
                            </i>
                          }
                          {data.status === 1 && data.pre_schedule === 1 &&
                            <i className="icons">
                              <ReactSVG
                                wrapper="span"
                                src={require("img/ico-calendar.svg")}
                              />
                            </i>
                          }
                        </div>
                        <div className="col-2">
                          <p className="news-date">{data.display_alert_date && data.display_alert_date} {data.alert_time && data.alert_time}</p>
                        </div>
                        <div className="col-2 text-center hover-icons-news">
                          {data.alert_sms === 1 && <i className="icons">
                            <ReactSVG
                              wrapper="span"
                              src={require("img/ico-sms.svg")}
                            />
                          </i>}
                          {data.alert_email === 1 && <i className="icons">
                            <ReactSVG
                              wrapper="span"
                              src={require("img/ico-msg.svg")}
                            />
                          </i>}
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
                                onClick={() =>
                                  this.handleDeleteElement(data.id)
                                }
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
                      </div>
                    )}
                    {this.state.editElement &&
                      !this.state.displayElement &&
                      this.state.editElementIndex === key ? (
                        <FormWidget
                          groups={this.state.groups}
                          attendees={this.state.attendees}
                          attendeeTypes={this.state.attendeeTypes}
                          listing={this.listing}
                          editdata={data}
                          editdataindex={key}
                          datacancel={this.handleCancel}
                        />
                      ) : (
                        ""
                      )}
                  </React.Fragment>
                )}
              </Translation>
            );
          })}
        </React.Fragment>
      );
    };

    return (
      <Translation>
        {(t) => (
          <div>
            <div
              className="wrapper-content third-step"
              style={{ paddingRight: "0px" }}
            >
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
                      icon="check"
                    />
                  )}
                  <header style={{ margin: 0 }} className="new-header clearfix">
                    <div className="row">
                      <div className="col-12">
                        <h1 className="section-title float-left">
                          {t("NS_NEWS_UPDATES")}
                        </h1>
                        {!this.state.displayElement && !this.state.editElement && (
                          <div className="new-right-header new-panel-buttons float-right">
                            <button
                              onClick={this.handleAddElement}
                              className="btn_addNew"
                            >
                              <Img
                                width="20px"
                                src={require("img/ico-plus-lg.svg")}
                              />
                            </button>
                          </div>
                        )}
                      </div>
                      <div className="col-4">
                        <p>{t("NS_NEWS_PAGE_SUB_HEADING")} </p>
                      </div>
                    </div>
                  </header>
                  <div className="attendee-management-section">
                    {this.state.displayElement ? (
                      <div style={{ marginTop: "0px", marginBottom: "0px" }}>
                        <FormWidget
                          groups={this.state.groups}
                          attendees={this.state.attendees}
                          attendeeTypes={this.state.attendeeTypes}
                          listing={this.listing}
                          datacancel={this.handleCancel}
                        />
                      </div>
                    ) : (
                        <React.Fragment>
                          {this.state.records.length > 0 && (
                            <React.Fragment>
                              <div
                                style={{ marginBottom: "0" }}
                                className="hotel-management-records attendee-records-template news-widget-records"
                              >
                                <Records data={this.state.records} />
                              </div>
                              {!this.state.editElement && (
                                <div
                                  style={{ marginTop: "10px" }}
                                  className="row"
                                >
                                  <div className="col-6">
                                    {this.state.records.length > 0 && (
                                      <span className="total-counter">
                                        {`${this.state.from} - ${
                                          this.state.to
                                          } ${t("G_OF")} ${this.state.total}`}
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
                              )}
                            </React.Fragment>
                          )}
                        </React.Fragment>
                      )}
                  </div>
                </React.Fragment>
              )}
            </div>
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

export default connect(mapStateToProps)(AttendeeWidget);
