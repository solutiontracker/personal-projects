import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import DropDown from '@/app/forms/DropDown';
import Img from 'react-image';
import { connect } from 'react-redux';
import { NavLink } from 'react-router-dom';
import { Translation } from "react-i18next";
import { AuthAction } from 'actions/auth/auth-action';
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { EventAction } from 'actions/event/event-action';
import { GeneralAction } from 'actions/general-action';
import 'sass/billing.scss';
import '@/app/event_site/billing/style/style.css';
import ConfirmationModal from "@/app/forms/ConfirmationModal";

const in_array = require("in_array");

class PaymentMethod extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      type: 'invoice-setting',
      eventsite_currency: "208",
      eventsite_invoice_prefix: "",
      eventsite_invoice_no: "",
      eventsite_vat: 0,
      eventsite_apply_multi_vat: 0,
      eventsite_always_apply_vat: 0,
      eventsite_billing_fik: 0,
      active_orders: 0,
      eventsite_vat_countries: [],
      currencies: [],
      countries: [],
      bcc_emails: [],
      sections_data: [],

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      preLoader: false,

      prev: "/event/settings/branding",
      next: "/event_site/billing-module/payment-providers",

      change: false
    }
  }

  componentDidMount() {
    this._isMounted = true;
    this.loadSettingsData();
  }

  loadSettingsData = () => {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/eventsite/billing/invoice-settings`)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  sections_data: response.data.sections_data,
                  bcc_emails: response.data.bcc_emails,
                  active_orders: response.data.active_orders,
                  currencies: response.data.currencies,
                  countries: response.data.countries,
                  eventsite_vat_countries: response.data.eventsite_vat_countries,
                  eventsite_currency: response.data.payment_setting.eventsite_currency,
                  eventsite_invoice_prefix: response.data.payment_setting.eventsite_invoice_prefix,
                  eventsite_invoice_no: response.data.payment_setting.eventsite_invoice_no,
                  eventsite_always_apply_vat: response.data.payment_setting.eventsite_always_apply_vat,
                  eventsite_vat: response.data.payment_setting.eventsite_vat,
                  eventsite_apply_multi_vat: response.data.payment_setting.eventsite_apply_multi_vat,
                  eventsite_billing_fik: response.data.payment_setting.eventsite_billing_fik,
                  preLoader: false
                });
              }
            }
          }
        },
        error => { }
      );
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  handleChange = (input, item, type) => e => {
    var value = (type === "dropdown" ? e.value : e.target.value);
    if (item && type) {
      const { dispatch } = this.props;
      const validate = dispatch(AuthAction.formValdiation(type, value));
      if (validate.status) {
        this.setState({
          [input]: value,
          [item]: 'success',
          change: true
        })
      } else {
        this.setState({
          [input]: value,
          [item]: 'error',
          change: true
        })
      }
    } else {
      this.setState({
        [input]: value,
        change: true
      })
    }
  }

  updateFlag = input => e => {
    this.setState({
      [input]: Number(this.state[input]) === 1 ? 0 : 1,
      change: true
    });
  };

  vatCountryChange = (eventsite_vat_countries) => {
    this.setState({
      eventsite_vat_countries: eventsite_vat_countries,
      change: true
    });
  }

  saveData = e => {
    e.preventDefault();
    const type = e.target.getAttribute('data-type');
    this.setState({ isLoader: type });
    service.put(`${process.env.REACT_APP_URL}/eventsite/billing/invoice-settings`, this.state)
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
            this.props.event.eventsite_secion_fields = response.eventsite_secion_fields;
            this.props.event.eventsite_payment_setting = response.eventsite_payment_setting;
            this.props.dispatch(EventAction.eventInfo(this.props.event));
            this.props.dispatch(GeneralAction.redirect(!this.props.redirect));
            if (type === "save-next") this.props.history.push(this.state.next);
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

  // handle input change
  handleInputChange = (e, index) => {
    const { value } = e.target;
    const bcc_emails = [...this.state.bcc_emails];
    bcc_emails[index] = value;
    this.setState({
      bcc_emails: bcc_emails,
      change: true
    });
  };

  // handle click event of the Remove button
  handleRemoveClick = index => {
    const bcc_emails = [...this.state.bcc_emails];
    bcc_emails.splice(index, 1);
    this.setState({
      bcc_emails: bcc_emails,
      change: true
    });
  };

  // handle click event of the Add button
  handleAddClick = () => e => {
    this.setState({
      bcc_emails: [...this.state.bcc_emails, ''],
      change: true
    })
  };

  // handle input change
  handleSectionField = (sectionKey, FieldKey, status) => e => {
    const sections_data = [...this.state.sections_data];
    sections_data[sectionKey]["fields"][FieldKey].status = (Number(status) === 1 ? 0 : 1);
    this.setState({
      sections_data: sections_data,
      change: true
    });
  };

  render() {
    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.id.toString() === id.toString());
        return (obj ? obj.name : '');
      }
    }

    return (
      <Translation>
        {(t) => (
          <React.Fragment>
            <div className="wrapper-content third-step main-billing-page ">
              <ConfirmationModal update={this.state.change} />
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
                  <div style={{ height: "100%" }}>
                    <div className="row header-section">
                      <div className="col-6">
                        <h1 className="section-title">{t('BILLING_PAYMENT_METHOD_MAIN_HEADING')}</h1>
                        <p>{t('BILLING_PAYMENT_METHOD_SUB_HEADING')}</p>
                      </div>
                    </div>
                    <div style={{ marginBottom: "20px" }} className="devices-select counter-wrapp">
                      <div className="row">
                        {
                          this.state.sections_data.map((section, sectionIndex) => {
                            return (
                              <React.Fragment key={section.id}>
                                {section.fields.map((field, fieldIndex) => {
                                  return (
                                    <React.Fragment key={field.id}>
                                      {in_array(field.field_alias, ["company_invoice_payment", "credit_card_payment", "company_public_payment"]) && ((field.field_alias === "company_public_payment" && this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.billing_type) === 1) || field.field_alias !== "company_public_payment") && (
                                        <div className="col-6">
                                          <div onClick={this.handleSectionField(sectionIndex, fieldIndex, field.status)} className={Number(field.status) === 1 ? "activestate devicebox" : "devicebox"}>
                                            {Number(field.status) === 1 && <span className="ischecked"><Img src={require("img/icon-close.svg")} /></span>}
                                            <span className="icon">
                                              {
                                                (() => {
                                                  if (field.field_alias === "company_invoice_payment")
                                                    return (
                                                      Number(field.status) === 1 ? (
                                                        <Img src={require('img/img-invoice-alt.svg')} width="69px" height="50px" />
                                                      ) : (
                                                          <Img src={require('img/img-invoice.svg')} width="69px" height="50px" />
                                                        )
                                                    )
                                                  else if (field.field_alias === "credit_card_payment")
                                                    return (
                                                      Number(field.status) === 1 ? (
                                                        <Img src={require('img/img-opayment-alt.svg')} width="69px" height="50px" />
                                                      ) : (
                                                          <Img src={require('img/img-opayment.svg')} width="69px" height="50px" />
                                                        )
                                                    )
                                                  else if (field.field_alias === "company_public_payment")
                                                    return (
                                                      Number(field.status) === 1 ? (
                                                        <Img src={require('img/img-invoice-alt.svg')} width="69px" height="50px" />
                                                      ) : (
                                                          <Img src={require('img/img-invoice.svg')} width="69px" height="50px" />
                                                        )
                                                    )
                                                })()
                                              }

                                            </span>
                                            <div>{(field.info && field.info.length > 0 ? field.info[0].value : "")}</div>
                                          </div>
                                        </div>
                                      )}
                                    </React.Fragment>
                                  )
                                })}
                              </React.Fragment>
                            )
                          })
                        }
                        {this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.billing_type) === 1 && (
                          <div className="col-6">
                            <div onClick={this.updateFlag('eventsite_billing_fik')} className={Number(this.state.eventsite_billing_fik) === 1 ? "activestate devicebox" : "devicebox"}>
                              {Number(this.state.eventsite_billing_fik) === 1 &&
                                <span className="ischecked"><Img src={require("img/icon-close.svg")} /></span>}
                              <span className="icon">
                                {Number(this.state.eventsite_billing_fik) === 1 ? (
                                  <Img src={require('img/img-fik-alt.svg')} width="54px" height="36px" />
                                ) : (
                                    <Img src={require('img/img-fik.svg')} width="54px" height="36px" />
                                  )}
                              </span>
                              <div>FIK</div>
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                    <div className="row">
                      <div className="col-6">
                        <DropDown
                          label={t("BILLING_PAYMENT_INVOICE_SETUP_CURRENCY")}
                          listitems={this.state.currencies}
                          required={true}
                          selected={this.state.eventsite_currency}
                          isSearchable='false'
                          selectedlabel={this.getSelectedLabel(this.state.currencies, this.state.eventsite_currency)}
                          onChange={this.handleChange('eventsite_currency', '', 'dropdown')}
                        />
                        <Input
                          type='text'
                          label={t("BILLING_PAYMENT_INVOICE_SETUP_INVOICE_PREFIX")}
                          name='name'
                          required={false}
                          value={this.state.eventsite_invoice_prefix}
                          onChange={this.handleChange('eventsite_invoice_prefix', '', 'text')}
                        />
                        <Input
                          type='text'
                          label={t("BILLING_PAYMENT_INVOICE_SETUP_INVOICE_NUMBER")}
                          preLoader="0000"
                          name='name'
                          required={false}
                          value={this.state.eventsite_invoice_no}
                          disabled={this.state.active_orders > 0 ? true : false}
                          onChange={this.handleChange('eventsite_invoice_no', '', 'text')}
                        />
                        <Input
                          type='text'
                          label={t("BILLING_PAYMENT_INVOICE_SETUP_VAT")}
                          name='name'
                          required={false}
                          disabled={this.state.active_orders > 0 ? true : false}
                          value={this.state.eventsite_vat}
                          onChange={this.handleChange('eventsite_vat', '', 'text')}
                        />
                        <div className="checkbox-row">
                          <p>{t("BILLING_PAYMENT_INVOICE_SETUP_APPLY_MULTI_VAT_LABEL")}</p>
                          <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('eventsite_apply_multi_vat')}
                            defaultChecked={this.state.eventsite_apply_multi_vat} type="checkbox" disabled={this.state.active_orders > 0 ? true : false} /><span></span></label>
                        </div>
                        <div style={{ marginBottom: "15px" }} className="checkbox-row">
                          <p>{t("BILLING_PAYMENT_INVOICE_SETUP_ALWAYS_APPLY_VAT_LABEL")}</p>
                          <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('eventsite_always_apply_vat')}
                            defaultChecked={this.state.eventsite_always_apply_vat} type="checkbox" disabled={this.state.active_orders > 0 ? true : false} /><span></span></label>
                        </div>
                        {Number(this.state.eventsite_always_apply_vat) === 0 && (
                          <DropDown
                            label={t("BILLING_PAYMENT_INVOICE_SETUP_SELECT_COUNTRIES")}
                            listitems={this.state.countries}
                            selected={this.state.eventsite_vat_countries}
                            isSearchable='false'
                            onChange={this.vatCountryChange}
                            isDisabled={this.state.active_orders > 0 ? true : false}
                            isMulti={true}
                          />
                        )}
                      </div>
                      <div style={{ marginTop: '10px' }} className="col-12">
                        <div className="hotel-add-item invoice-copy-form">
                          <h4 className="component-heading">
                            {t("BILLING_PAYMENT_INVOICE_ADD_BCC_EMAILS")}
                          </h4>
                          <div className="row d-flex">
                            <div className="col-6">
                              {
                                this.state.bcc_emails.map((email, i) => {
                                  return (
                                    <React.Fragment key={i}>
                                      <div style={{ position: "relative" }}>
                                        <Input
                                          type="text"
                                          label="info@eventbuizz.com"
                                          value={email}
                                          required={false}
                                          onChange={e => this.handleInputChange(e, i)}
                                        />
                                        <span className="btn-delete-item" onClick={() => this.handleRemoveClick(i)}>
                                          <Img src={require('img/ico-bin-alt.svg')} alt="" />
                                        </span>
                                      </div>
                                    </React.Fragment>
                                  )
                                })
                              }
                            </div>
                          </div>
                          <div className="bottom-panel-button">
                            <button
                              data-type="save-new"
                              className="btn save-new"
                              onClick={this.handleAddClick()}
                            >
                              {t("BILLING_PAYMENT_INVOICE_ADD_BCC_EMAIL")}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="bottom-component-panel clearfix">
                    <NavLink
                      target="_blank"
                      className="btn btn-preview float-left"
                      to={`/event/preview`}
                    >
                      <i className="material-icons">remove_red_eye</i>
                      {t("G_PREVIEW")}
                    </NavLink>
                    {this.state.prev !== undefined && (
                      <NavLink className="btn btn-prev-step" to={this.state.prev}><span className="material-icons">
                        keyboard_backspace</span></NavLink>
                    )}
                    <button
                      style={{ minWidth: "124px" }}
                      data-type="save"
                      disabled={this.state.isLoader ? true : false}
                      className="btn btn btn-save"
                      onClick={this.saveData}
                    >
                      {this.state.isLoader === "save" ? (
                        <span className="spinner-border spinner-border-sm"></span>
                      ) : (
                          t("G_SAVE")
                        )}
                    </button>
                    <button
                      data-type="save-next"
                      disabled={this.state.isLoader ? true : false}
                      className="btn btn-save-next"
                      onClick={this.saveData}
                    >
                      {this.state.isLoader === "save-next" ? (
                        <span className="spinner-border spinner-border-sm"></span>
                      ) : (
                          t("G_SAVE_NEXT")
                        )}
                    </button>
                  </div>
                </React.Fragment>
              )}
            </div>
          </React.Fragment>
        )}
      </Translation>
    );
  }
}

function mapStateToProps(state) {
  const { event, redirect } = state;
  return {
    event, redirect
  };
}

export default connect(mapStateToProps)(PaymentMethod);