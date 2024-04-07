import * as React from "react";
import { connect } from "react-redux";
import { AuthAction } from "actions/auth/auth-action";
import Input from "@/app/forms/Input";
import AlertMessage from "@/app/forms/alerts/AlertMessage";
import { Translation } from "react-i18next";
import { service } from "services/service";
import DropDown from "@/app/forms/DropDown";
import TextArea from "@/app/forms/TextArea";
import DateTime from "@/app/forms/DateTime";
import { confirmAlert } from "react-confirm-alert"; // Import
import Timepicker from "@/app/forms/Timepicker";
import { withRouter } from "react-router-dom";
import ConfirmationModal from "@/app/forms/ConfirmationModal";

class FormWidget extends React.Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      title:
        this.props.editdata !== undefined ? this.props.editdata.info.title : "",
      description:
        this.props.editdata !== undefined
          ? this.props.editdata.info.description
          : "",
      pre_schedule:
        this.props.editdata !== undefined
          ? this.props.editdata.pre_schedule
          : 0,
      alert_email:
        this.props.editdata !== undefined ? this.props.editdata.alert_email : 0,
      alert_sms:
        this.props.editdata !== undefined ? this.props.editdata.alert_sms : 0,
      sendto:
        this.props.editdata !== undefined ? this.props.editdata.sendto : "all",
      alert_date:
        this.props.editdata !== undefined ? this.props.editdata.alert_date : "",
      alert_time:
        this.props.editdata !== undefined ? this.props.editdata.alert_time : "",
      group_id:
        this.props.editdata !== undefined ? this.props.editdata.group_id : [],
      attendee_type_id:
        this.props.editdata !== undefined ? this.props.editdata.attendee_type_id : [],
      individual_id:
        this.props.editdata !== undefined
          ? this.props.editdata.individual_id
          : [],
      groups: this.props.groups,
      attendees: this.props.attendees,
      display: true,
      attendeeTypes: this.props.attendeeTypes,
      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      stateToggle: false,
      // Valdiation
      title_validate: this.props.editdata !== undefined ? "success" : "",

      change: false
    };

    this.handleGroupChange = this.handleGroupChange.bind(this);
    this.handleIndividualChange = this.handleIndividualChange.bind(this);
    this.handleAttendeeTypeChange = this.handleAttendeeTypeChange.bind(this);
  }

  componentDidMount() {
    this._isMounted = true;
    this.fetchData();
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  fetchData() {
    if (this.props.editdata !== undefined) {
      this.setState({ preLoader: true });
      service
        .get(
          `${process.env.REACT_APP_URL}/alert/update/${this.props.editdata.id}`,
          this.state
        )
        .then(
          (response) => {
            if (this._isMounted) {
              if (response.success) {
                this.setState({
                  group_id: response.group_id,
                  individual_id: response.individual_id,
                  preLoader: false,
                });
              }
            }
          },
          (error) => { }
        );
    }
  }

  handleChange = (input, item, type) => (e) => {
    if (e.target.value === undefined) {
      this.setState({
        [input]: [],
        change: true
      });
    } else {
      if (item && type) {
        const { dispatch } = this.props;
        const validate = dispatch(
          AuthAction.formValdiation(type, e.target.value)
        );
        if (validate.status) {
          this.setState({
            [input]: e.target.value,
            [item]: "success",
            change: true
          });
        } else {
          this.setState({
            [input]: e.target.value,
            [item]: "error",
            change: true
          });
        }
      } else {
        this.setState({
          [input]: e.target.value,
          change: true
        });
      }
    }
  };

  handleGroupChange(option) {
    this.setState((state) => {
      return {
        group_id: option,
        change: true
      };
    });
  }
  
  handleAttendeeTypeChange(option) {
    this.setState((state) => {
      return {
        attendee_type_id: option,
        change: true
      };
    });
  }

  handleIndividualChange(option) {
    this.setState((state) => {
      return {
        individual_id: option,
        change: true
      };
    });
  }

  saveData = (e) => {
    e.preventDefault();
    if (
      this.state.title_validate === "error" ||
      this.state.title_validate.length === 0
    ) {
      this.setState({
        title_validate: "error",
      });
    }
    if (this.state.title_validate === "success") {
      confirmAlert({
        customUI: ({ onClose }) => {
          return (
            <Translation>
              {(t) => (
                <div className="app-main-popup">
                  <div className="app-header">
                    <h4>{t("NS_SEND_NEWS")}</h4>
                  </div>
                  <div className="app-body">
                    <p>{t("NS_SEND_NEWS_CONFIRMATION_ALERT")}</p>
                  </div>
                  <div className="app-footer">
                    <button className="btn btn-cancel" onClick={onClose}>
                      {t("G_CANCEL")}
                    </button>
                    <button
                      className="btn btn-success"
                      onClick={() => {
                        onClose();
                        this.setState({ isLoader: true });
                        if (this.props.editdata !== undefined) {
                          service
                            .put(
                              `${process.env.REACT_APP_URL}/alert/update/${this.props.editdata.id}`,
                              this.state
                            )
                            .then(
                              (response) => {
                                if (this._isMounted) {
                                  if (response.success) {
                                    this.setState({
                                      message: response.message,
                                      success: true,
                                      isLoader: false,
                                      errors: {},
                                      change: false
                                    });
                                    this.props.listing(1, true);
                                  } else {
                                    this.setState({
                                      message: response.message,
                                      success: false,
                                      isLoader: false,
                                      errors: response.errors,
                                    });
                                  }
                                }
                              },
                              (error) => { }
                            );
                        } else {
                          service
                            .put(`${process.env.REACT_APP_URL}/alert/store`, this.state)
                            .then(
                              (response) => {
                                if (this._isMounted) {
                                  if (response.success) {
                                    this.setState({
                                      message: response.message,
                                      success: true,
                                      isLoader: false,
                                      errors: {},
                                      change: false
                                    });
                                    this.props.listing(1, true);
                                  } else {
                                    this.setState({
                                      message: response.message,
                                      success: false,
                                      isLoader: false,
                                      errors: response.errors,
                                    });
                                  }
                                }
                              },
                              (error) => { }
                            );
                        }
                      }}
                    >
                      { this.state.pre_schedule ? t("G_SAVE") : t("G_SEND")}
                    </button>
                  </div>
                </div>
              )}
            </Translation>
          );
        },
      });
    }
  };

  handleToggle = (input, value) => (e) => {
    e.preventDefault();
    this.setState({
      [input]: value,
      change: true
    });
  };

  handleDateChange = (input, item) => (e) => {
    if (e !== undefined && e !== "cleardate") {
      var month = e.getMonth() + 1;
      var day = e.getDate();
      var year = e.getFullYear();
      var daydigit = day.toString().length === 2 ? day : "0" + day;
      var date = month + "/" + daydigit + "/" + year;
      this.setState({
        [input]: date,
        [item]: "success",
        change: true
      });
    } else {
      this.setState({
        [input]: "",
        [item]: "error",
        change: true
      });
    }
  };

  timeChange = (input, date) => {
    if (input === "alert_time") {
      this.setState({
        [input]: date,
        alert_time: date.split("-")[0],
        change: true
      });
    } else {
      this.setState({
        [input]: date,
        change: true
      });
    }
  };

  handleTimeChange = (input, value, validate) => {
    if (value !== "") {
      if (input === "alert_time") {
        this.setState({
          alert_time: value,
          alert_time_validate: "success",
          change: true
        });
      } else {
        this.setState({
          [input]: value,
          [validate]: "success",
          change: true
        });
      }
    } else {
      this.setState({
        [input]: "",
        [validate]: "error",
        change: true
      });
    }
  };

  updateFlag = (input) => (e) => {
    this.setState({
      [input]: this.state[input] === 1 ? 0 : 1,
      change: true
    });
  };

  render() {
    return (
      <Translation>
        {(t) => (
          <React.Fragment>
            <div className="hotel-add-item send-news-wrapper">
              <ConfirmationModal update={this.state.change} />
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
              <div className="row d-flex">
                <div className="col-4">
                  <Input
                    type="text"
                    label={`${t("NS_FORM_SUBJECT_LABEL")}`}
                    name="title"
                    value={this.state.title}
                    onChange={this.handleChange(
                      "title",
                      "title_validate",
                      "title"
                    )}
                    required={true}
                  />
                  {this.state.errors.title && (
                    <p className="error-message">{this.state.errors.title}</p>
                  )}
                  {this.state.title_validate === "error" && (
                    <p className="error-message">{t("EE_FIELD_IS_REQUIRED")}</p>
                  )}
                </div>
              </div>
              <div className="row d-flex">
                <div className="col-4 PriceNight mb-3">
                  <TextArea
                    label={t("NS_FORM_DESCRIPTION_LABEL")}
                    value={this.state.description}
                    height={330}
                    onChange={this.handleChange("description")}
                    required={false}
                  />
                  <p className="below-info">
                    {t("NS_FORM_DESCRIPTION_LABEL_BELOW_INFO")}
                  </p>
                  {this.state.errors.description && (
                    <p className="error-message">
                      {this.state.errors.description}
                    </p>
                  )}
                </div>
              </div>
              <div className="row d-flex">
                <div className="col-4 label-advance-setting">
                  <span
                    onClick={() => this.setState({ stateToggle: !this.state.stateToggle })}
                    className="advance-settings"
                  >
                    <i className="material-icons">{this.state.stateToggle ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>
                    {t('NS_ADVANCE_SETTINGS')}
                  </span>
                </div>
              </div>
              {this.state.stateToggle && (
                <div className="wrapper-advance-setting">
                  <div className="row d-flex">
                    <div className="col-4 label-fields-news">
                      <label>{t("NS_FORM_PRE_SCHEDULE_LABEL")}</label>
                      <div className="navigation">
                        <span
                          className={
                            Number(this.state.pre_schedule) === 0
                              ? "active"
                              : ""
                          }
                          onClick={this.handleToggle("pre_schedule", 0)}
                        >
                          {t("G_NO")}
                          <i className="material-icons">
                            {Number(this.state.pre_schedule) === 0
                              ? "radio_button_checked"
                              : "radio_button_unchecked"}
                          </i>
                        </span>
                        <span
                          className={
                            Number(this.state.pre_schedule) === 1
                              ? "active"
                              : ""
                          }
                          onClick={this.handleToggle("pre_schedule", 1)}
                        >
                          {t("G_YES")}
                          <i className="material-icons">
                            {Number(this.state.pre_schedule) === 1
                              ? "radio_button_checked"
                              : "radio_button_unchecked"}
                          </i>
                        </span>
                      </div>
                    </div>
                  </div>
                  {Number(this.state.pre_schedule) === 1 && (
                    <div
                      style={{ marginBottom: "15px" }}
                      className="row d-flex"
                    >
                      <div className="col-4">
                        <div className="row d-flex">
                          <div className="col-6">
                            <DateTime
                              className="date"
                              value={this.state.alert_date}
                              onChange={this.handleDateChange(
                                "alert_date",
                                "alert_date_validate"
                              )}
                              label={t("NS_FORM_DATE_LABEL")}
                              required={true}
                            />
                            {this.state.errors.alert_date && (
                              <p className="error-message">
                                {this.state.errors.alert_date}
                              </p>
                            )}
                          </div>
                          <div className="col-6">
                            <Timepicker
                              label={t("NS_FORM_TIME_LABEL")}
                              value={this.state.alert_time}
                              onChange={this.handleTimeChange.bind(this)}
                              stateName="alert_time"
                              validateName="alert_time_validate"
                              required={true}
                              seconds={true}
                            />
                            {this.state.errors.alert_time && (
                              <p className="error-message">
                                {this.state.errors.alert_time}
                              </p>
                            )}
                          </div>
                        </div>
                      </div>
                    </div>
                  )}
                  <div style={{ marginTop: "25px" }} className="row d-flex">
                    <div className="col-4 label-fields-news">
                      <label>{t("NS_SEND_TO_LABEL")}</label>
                      <div className="navigation">
                        <span
                          className={
                            this.state.sendto === "all" ? "active" : ""
                          }
                          onClick={this.handleToggle("sendto", "all")}
                        >
                          {t("NS_SEND_TO_ALL_OPTION")}
                          <i className="material-icons">
                            {this.state.sendto === "all"
                              ? "radio_button_checked"
                              : "radio_button_unchecked"}
                          </i>
                        </span>
                        {/* <span
                          className={
                            this.state.sendto === "groups" ? "active" : ""
                          }
                          onClick={this.handleToggle("sendto", "groups")}
                        >
                          {t("NS_SEND_TO_GROUPS_OPTION")}
                          <i className="material-icons">
                            {this.state.sendto === "groups"
                              ? "radio_button_checked"
                              : "radio_button_unchecked"}
                          </i>
                        </span> */}
                        <span
                          className={
                            this.state.sendto === "attendee_types" ? "active" : ""
                          }
                          onClick={this.handleToggle("sendto", "attendee_type")}
                        >
                          {t("NS_SEND_TO_ATTENDEE_TYPES_OPTION")}
                          <i className="material-icons">
                            {this.state.sendto === "attendee_type"
                              ? "radio_button_checked"
                              : "radio_button_unchecked"}
                          </i>
                        </span>
                        <span
                          className={
                            this.state.sendto === "individuals" ? "active" : ""
                          }
                          onClick={this.handleToggle("sendto", "individuals")}
                        >
                          {t("NS_SEND_TO_INDIVIDUALS_OPTION")}
                          <i className="material-icons">
                            {this.state.sendto === "individuals"
                              ? "radio_button_checked"
                              : "radio_button_unchecked"}
                          </i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div className="row d-flex drop-down-items">
                    {/* {this.state.sendto === "groups" && (
                      <div className="col-4 d-flex">
                        <DropDown
                          label={t("NS_SEND_TO_SEARCH_GROUPS_PLACEHOLDER")}
                          listitems={this.state.groups}
                          selected={this.state.group_id}
                          onChange={this.handleGroupChange}
                          required={true}
                          isMulti={true}
                        />
                        {this.state.errors && this.state.errors.group_id && (
                          <p className="error-message">
                            {this.state.errors.group_id}
                          </p>
                        )}
                      </div>
                    )} */}
                    {this.state.sendto === "attendee_type" && (
                      <div className="col-4 d-flex">
                        <DropDown
                          label={t("NS_SEND_TO_SEARCH_ATTENDEE_TYPES_PLACEHOLDER")}
                          listitems={this.state.attendeeTypes}
                          selected={this.state.attendee_type_id}
                          onChange={this.handleAttendeeTypeChange}
                          required={true}
                          isMulti={true}
                        />
                        {this.state.errors && this.state.errors.group_id && (
                          <p className="error-message">
                            {this.state.errors.group_id}
                          </p>
                        )}
                      </div>
                    )}
                    {this.state.sendto === "individuals" && (
                      <div className="col-4 d-flex">
                        <DropDown
                          label={t("NS_SEND_TO_SEARCH_INDIVIDUALS_PLACEHOLDER")}
                          listitems={this.state.attendees}
                          selected={this.state.individual_id}
                          onChange={this.handleIndividualChange}
                          required={true}
                          isMulti={true}
                        />
                        {this.state.errors &&
                          this.state.errors.individual_id && (
                            <p className="error-message">
                              {this.state.errors.individual_id}
                            </p>
                          )}
                      </div>
                    )}
                  </div>
                  <div className="row d-flex">
                    <div className="col-6 d-flex">
                      <div
                        style={{ marginBottom: "0" }}
                        className="checkbox-row"
                      >
                        <h4 className="tooltipHeading">
                          {t("NS_FORM_SEND_BY_EMAIL_LABEL")}
                        </h4>
                        <label className="custom-checkbox-toggle">
                          <input
                            onChange={this.updateFlag("alert_email")}
                            type="checkbox"
                            defaultChecked={this.state.alert_email}
                          />
                          <span></span>
                        </label>
                      </div>
                      <div
                        style={{ marginBottom: "0" }}
                        className="checkbox-row"
                      >
                        <h4 className="tooltipHeading">
                          {t("NS_FORM_SEND_BY_SMS_LABEL")}
                        </h4>
                        <label className="custom-checkbox-toggle">
                          <input
                            onChange={this.updateFlag("alert_sms")}
                            type="checkbox"
                            defaultChecked={this.state.alert_sms}
                          />
                          <span></span>
                        </label>
                        {this.state.alert_sms ? (
                          <p className="below-info">
                            {t("NS_SMS_ADDITIONAL_CHARGES_NOTE")}
                          </p>
                        ) : ''}
                      </div>
                    </div>
                  </div>
                </div>
              )}
            </div>
            <div className="bottom-component-panel clearfix">
              <button className="btn btn-save" onClick={this.props.datacancel}>
                {t("G_CANCEL")}
              </button>
              <button
                disabled={this.state.isLoader ? true : false}
                onClick={this.saveData}
                className="btn btn-save-next"
              >
                {this.state.isLoader ? (
                  <span className="spinner-border spinner-border-sm"></span>
                ) : (
                   this.state.pre_schedule ? t("G_SAVE") : t("G_SEND")
                  )}
              </button>
            </div>
          </React.Fragment>
        )}
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { alert } = state;
  return {
    alert,
  };
}

export default connect(mapStateToProps)(withRouter(FormWidget));
