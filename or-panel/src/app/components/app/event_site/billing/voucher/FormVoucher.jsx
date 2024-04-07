import React, { Component } from 'react';
import Img from 'react-image';
import VoucherItems from "@/app/event_site/billing/voucher/VoucherItems";
import Input from '@/app/forms/Input';
import DropDown from '@/app/forms/DropDown';
import DateTime from '@/app/forms/DateTime';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation } from "react-i18next";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import { connect } from 'react-redux';
import { AuthAction } from 'actions/auth/auth-action';
import moment from 'moment';
import ConfirmationModal from "@/app/forms/ConfirmationModal";
import 'sass/billing.scss';
import in_array from 'in_array'; 

class FormVoucher extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      type: this.props.type,
      itemsPopup: false,
      message: false,
      success: true,
      errors: {},
      isLoader: false,
      discount_type_id: (this.props.editData !== undefined ? this.props.editData.discount_type_id : ''),
      discountTypes: [{ id: '1', name: 'Amount' }, { id: '2', name: 'Percentage' }],
      types: [{ id: "order", name: 'Order' }, { id: "billing_items", name: 'Billing Items' }],
      items: [],
      limit: 10,

      //validations
      validate_type: 'success',
      validate_voucher_name: '',
      validate_discount_type: '',
      validate_price: '',
      validate_code: '',

      //loading
      preLoader: false,

      change: false

    }
  }

  componentDidMount() {
    if (this.props.editData) {
      this.loadVoucher();
    }
  }

  componentWillUnmount() {
    this._isMounted = false;
  }

  loadVoucher = () => {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/eventsite/billing/voucher/edit/${this.props.editData.id}`)
      .then(
        response => {
          if (response.success) {
            if (response.items) {
              if (this._isMounted) {
                this.setState({
                  items: response.items,
                  preLoader: false,
                  type: this.props.editData.type,
                  code: this.props.editData.code,
                  voucher_name: this.props.editData.value,
                  expiry_date: moment(new Date(this.props.editData.expiry_date)).utc().format('MM/DD/YYYY'),
                  discount_type: this.props.editData.discount_type,
                  price: this.props.editData.price,
                  usage: this.props.editData.usage,
                  validate_type: 'success',
                  validate_voucher_name: 'success',
                  validate_discount_type: 'success',
                  validate_price: 'success',
                  validate_code: 'success',
                });
              }
            }
          }
        },
        error => { }
      );
  }

  save = e => {
    this._isMounted = true;
    if (this.state.validate_type === 'success' && this.state.validate_voucher_name === 'success' && ((this.state.validate_discount_type === 'success' && this.state.validate_price === 'success' && in_array(this.state.type, ["order", "vat_free"])) || this.state.type === "billing_items") && this.state.validate_code === 'success') {
      this.setState({ isLoader: true });
      if (this.props.editData) {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/voucher/edit/${this.props.editData.id}`, this.state)
          .then(
            response => {
              if (this._isMounted) {
                if (response.success) {
                  this.setState({
                    message: response.message,
                    success: true,
                    isLoader: false,
                    errors: {},
                    change: false
                  });
                  this.props.listing(1);
                } else {
                  this.setState({
                    message: response.message,
                    success: false,
                    isLoader: false,
                    errors: response.errors
                  });
                }
              }
            },
            error => { }
          );
      } else {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/voucher/create`, this.state)
          .then(
            response => {
              if (this._isMounted) {
                if (response.success) {
                  this.setState({
                    message: response.message,
                    success: true,
                    isLoader: false,
                    errors: {},
                    change: false
                  });
                  this.props.listing(1);
                } else {
                  this.setState({
                    message: response.message,
                    success: false,
                    isLoader: false,
                    errors: response.errors
                  });
                }
              }
            },
            error => { }
          );
      }
    } else {
      if (this.state.validate_voucher_name !== 'success') {
        this.setState({
          validate_voucher_name: 'error',
        })
      }
      if (this.state.validate_type !== 'success') {
        this.setState({
          validate_type: 'error',
        })
      }
      if (this.state.validate_discount_type !== 'success' && in_array(this.state.type, ["order", "vat_free"])) {
        this.setState({
          validate_discount_type: 'error',
        })
      }
      if (this.state.validate_price !== 'success' && in_array(this.state.type, ["order", "vat_free"])) {
        this.setState({
          validate_price: 'error',
        })
      }
      if (this.state.validate_code !== 'success') {
        this.setState({
          validate_code: 'error',
        })
      }
    }
  }

  handleChange = (input, item, type) => e => {
    var value = (type === "dropdown" ? e.value : e.target.value);
    if (value === undefined) {
      this.setState({
        [input]: '',
        change: true
      })
    } else {
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
    };
  }

  handleDateChange = input => e => {
    if (e !== undefined) {
      var month = e.getMonth() + 1;
      var day = e.getDate();
      var year = e.getFullYear();
      var daydigit = (day.toString().length === 2) ? day : '0' + day;
      var date = month + '/' + daydigit + '/' + year;
      this.setState({
        [input]: date,
        change: true
      });
    } else {
      this.setState({
        [input]: '',
        change: true
      });
    }
  }

  closePopup = (e) => {
    e.preventDefault();
    this.setState({
      itemsPopup: false
    })
  }

  saveItems = (data) => {
    this.setState({
      itemsPopup: false,
      items: data,
      change: true
    });
  }

  removeItem = index => {
    const items = this.state.items;
    items.splice(index, 1);
    this.setState({
      items: items,
      change: true
    });
  };

  generateRandomCode = index => {
    this._isMounted = true;
    service.post(`${process.env.REACT_APP_URL}/eventsite/billing/voucher/generate-code`, this.state)
      .then(
        response => {
          if (this._isMounted) {
            if (response.success) {
              this.setState({
                code: response.code,
                change: true,
                validate_code: "success"
              });
            }
          }
        },
        error => { }
      );
  };

  handleDropdown = e => {
    e.preventDefault();
    if (e.target.classList.contains('active')) {
      e.target.classList.remove('active');
    } else {
      var query = document.querySelectorAll('.btn_addmore');
      for (var i = 0; i < query.length; ++i) {
        query[i].classList.remove('active');
      }
      e.target.classList.add('active');
    }
  }

  handleLimit = (limit) => e => {
    this.setState(prevState => ({
      limit: limit,
    }));
  };

  render() {
    const { datamode, onCancel } = this.props;
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
            {this.state.itemsPopup && <VoucherItems editData={this.props.editData} selectedItems={this.state.items} saveItems={this.saveItems} onClose={this.closePopup.bind(this)} />}
            <ConfirmationModal update={this.state.change} />
            {this.state.preLoader &&
              <Loader />
            }
            {!this.state.preLoader && (
              <React.Fragment>
                <div className="new-header">
                  <h1 className="section-title">{t("BILLING_VOUCHERS_MAIN_HEADING")}</h1>
                </div>
                <div className={`option-wrapper form-items-section ${this.props.editData ? "isGray" : ""}`}>
                  {this.state.message && (
                    <AlertMessage
                      className={`alert  ${this.state.success ? "alert-success" : "alert-danger"}`}
                      title={`${this.state.success ? "" : t("EE_OCCURRED")}`}
                      content={this.state.message}
                      icon={this.state.success ? "check" : "info"}
                    />
                  )}
                  <h3>{datamode}</h3>
                  <div className="row">
                    <div className="col-6">
                      {/* <DropDown
                        label={t("BILLING_VOUCHERS_VOUCHER_TYPE_LABEL")}
                        listitems={this.state.types}
                        required={true}
                        selected={this.state.type}
                        isSearchable='false'
                        selectedlabel={this.getSelectedLabel(this.state.types, this.state.type)}
                        onChange={this.handleChange('type', 'validate_type', 'dropdown')}
                      /> */}
                      {this.state.errors.type && (
                        <p className="error-message">{this.state.errors.type}</p>
                      )}
                      {this.state.validate_type === "error" && (
                        <p className="error-message">{t("EE_FIELD_IS_REQUIRED")}</p>
                      )}
                      <Input
                        type='text'
                        label={t("BILLING_VOUCHERS_VOUCHER_NAME_LABEL")}
                        name='name'
                        required={true}
                        value={this.state.voucher_name}
                        onChange={this.handleChange('voucher_name', 'validate_voucher_name', 'text')}
                      />
                      {this.state.errors.voucher_name && (
                        <p className="error-message">{this.state.errors.voucher_name}</p>
                      )}
                      {this.state.validate_voucher_name === "error" && (
                        <p className="error-message">{t("EE_FIELD_IS_REQUIRED")}</p>
                      )}
                      {in_array(this.state.type, ["order", "vat_free"]) && (
                        <React.Fragment>
                          <DropDown
                            label={t("BILLING_VOUCHERS_DISCOUNT_TYPE_LABEL")}
                            listitems={this.state.discountTypes}
                            required={true}
                            selected={this.state.discount_type}
                            isSearchable='false'
                            selectedlabel={this.getSelectedLabel(this.state.discountTypes, this.state.discount_type)}
                            onChange={this.handleChange('discount_type', 'validate_discount_type', 'dropdown')}
                          />
                          {this.state.errors.discount_type && (
                            <p className="error-message">{this.state.errors.discount_type}</p>
                          )}
                          {this.state.validate_discount_type === "error" && (
                            <p className="error-message">{t("EE_FIELD_IS_REQUIRED")}</p>
                          )}
                          <Input
                            type='text'
                            label={(in_array(this.state.type, ["order", "vat_free"]) ? t("BILLING_VOUCHERS_ORDER_PRICE_LABEL") : t("BILLING_VOUCHERS_ITEM_PRICE_LABEL"))}
                            name='name'
                            required={true}
                            value={this.state.price}
                            onChange={this.handleChange('price', 'validate_price', 'text')}
                          />
                          {this.state.errors.price && (
                            <p className="error-message">{this.state.errors.price}</p>
                          )}
                          {this.state.validate_price === "error" && (
                            <p className="error-message">{t("EE_FIELD_IS_REQUIRED")}</p>
                          )}
                        </React.Fragment>
                      )}
                      <div className="field-item-name">
                        <Input
                          type='text'
                          label={t("BILLING_VOUCHERS_VOUCHER_CODE_LABEL")}
                          name='name'
                          required={true}
                          value={this.state.code}
                          onChange={this.handleChange('code', 'validate_code', 'text')}
                        />
                        <div className="info-sec-field">
                          <p><a onClick={() => this.generateRandomCode()} style={{ cursor: 'pointer' }}>{t("BILLING_VOUCHERS_GENERATE_VOUCHER_CODE")}</a></p>
                        </div>
                        {this.state.errors.code && (
                          <p className="error-message">{this.state.errors.code}</p>
                        )}
                        {this.state.validate_code === "error" && (
                          <p className="error-message">{t("EE_FIELD_IS_REQUIRED")}</p>
                        )}
                      </div>
                      <div className="field-item-name">
                        <DateTime
                          className="date"
                          value={this.state.expiry_date}
                          onChange={this.handleDateChange('expiry_date')}
                          label={t("BILLING_VOUCHERS_EXPIRE_DATE_LABEL")}
                          required={false}
                        />
                        <div className="info-sec-field">
                          <p>{t("BILLING_VOUCHERS_LEAVE_BLANK_EXPIRY")}</p>
                        </div>
                      </div>
                      {in_array(this.state.type, ["order", "vat_free"]) && (
                        <div className="field-item-name">
                          <Input
                            type="number"
                            label={t("BILLING_VOUCHERS_TOTAL_AVAILABLE_VOUCHER")}
                            name="usage"
                            value={this.state.usage}
                            onChange={this.handleChange("usage", '', 'text')}
                            required={true}
                          />
                          <div className="info-sec-field">
                            <p>{t("BILLING_VOUCHERS_LEAVE_BLANK_UNLIMITED")}</p>
                          </div>
                        </div>
                      )}
                      {this.props.type === "billing_items" && (
                        <label onClick={() => this.setState({ itemsPopup: true })} className='label-input-dropdown label-input'>
                          <input type="text" placeholder=" " readOnly />
                          <span>Search items</span>
                          <button className="btn">
                            <img src={require('img/ico-more.svg')} alt="" />
                          </button>
                        </label>
                      )}
                    </div>
                    {this.props.type === "billing_items" && this.state.items && this.state.items.length > 0 &&
                      <div className="col-12">
                        {this.state.errors.voucher_items && (
                          <p className="error-message" dangerouslySetInnerHTML={{ __html: this.state.errors.voucher_items }}></p>
                        )}
                        <div className="wrapper-billing-section voucher-select-items voucher-elements-inner">
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
                          <div style={{borderTop: '0px'}} className="row d-flex header-billing">
                            <div className="grid-3">
                              <strong>{t("BILLING_VOUCHERS_ITEM_NAME").toUpperCase()}</strong>
                            </div>
                            <div className="grid-4">
                              <strong>{t("BILLING_VOUCHERS_NUMBER_OF_USAGE").toUpperCase()}</strong>
                            </div>
                            <div className="grid-5">
                              <strong>{t("BILLING_VOUCHERS_DISCOUNT_TYPE_LABEL").toUpperCase()}</strong>
                            </div>
                            <div className="grid-6">
                              <strong>{t("BILLING_VOUCHERS_AMOUNT_LABEL").toUpperCase()}</strong>
                            </div>
                            <div className="grid-6">
                            </div>
                          </div>
                          {this.state.items && this.state.items.map((item, key) => {
                            return (
                              <React.Fragment key={key}>
                                {(key + 1) <= this.state.limit && item.checked && (
                                  <div className="row">
                                    <div className="grid-3">
                                      <p>{(item.detail ? item.detail.item_name : '')}</p>
                                    </div>
                                    <div className="grid-4">
                                      <p>{item.useage}</p>
                                    </div>
                                    <div className="grid-5">
                                      <p>{Number(item.discount_type) === 2 ? t("BILLING_VOUCHERS_PERCENTAGE_LABEL") : t("BILLING_VOUCHERS_AMOUNT_LABEL")}</p>
                                    </div>
                                    <div className="grid-6">
                                      <p>{item.discount_price}</p>
                                    </div>
                                    <div className="grid-7">
                                      <div className="practical-edit-panel">
                                        <span className="btn-edit" onClick={() => this.setState({ itemsPopup: true })}>
                                          <Img src={require('img/ico-edit.svg')} />
                                        </span>
                                        <span className="btn-edit" onClick={() => this.removeItem(key)}>
                                          <Img src={require('img/ico-delete.svg')} />
                                        </span>
                                      </div>
                                    </div>
                                  </div>
                                )}
                              </React.Fragment>
                            )
                          })}
                        </div>
                      </div>}
                  </div>
                  <div className="bottom-panel-button">
                    <button
                      disabled={this.state.isLoader ? true : false}
                      className="btn"
                      onClick={this.save.bind(this)}
                    >
                      {this.state.isLoader ? (
                        <span className="spinner-border spinner-border-sm"></span>
                      ) : this.props.editData ? (
                        t("G_SAVE")
                      ) : (
                            t("G_SAVE")
                          )}
                    </button>
                    <button
                      className="btn btn-cancel"
                      onClick={() => onCancel("FormVoucher")}
                    >
                      {t("G_CANCEL")}
                    </button>
                  </div>
                </div>
              </React.Fragment>
            )}
          </React.Fragment>
        )}
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

export default connect(mapStateToProps)(FormVoucher);