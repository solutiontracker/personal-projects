import React, { Component } from 'react';
import Input from '@/app/forms/Input';
import DateRange from '@/app/forms/DateRange';
import moment from 'moment';
import LinkTo from "@/app/event_site/billing/items/LinkTo";
import { service } from 'services/service';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import { Translation, withTranslation } from "react-i18next";
import CKEditor from 'ckeditor4-react';
import DropDown from '@/app/forms/DropDown';
import { connect } from 'react-redux';
import ConfirmationModal from "@/app/forms/ConfirmationModal";
import 'sass/billing.scss';
import SimpleReactValidator from 'simple-react-validator';

const in_array = require("in_array");

class FormItem extends Component {
  constructor(props) {
    super(props);

    this.state = {
      price: "",
      vat: (this.props.event && this.props.event.eventsite_payment_setting && this.props.event.eventsite_payment_setting.eventsite_vat ? this.props.event.eventsite_payment_setting.eventsite_vat : 0),
      item_number: "",
      item_name: "",
      description: "",
      total_tickets: "",
      qty: "",
      type: this.props.type,
      groups: this.props.groups,
      link_to_id: (Number(this.props.billing_item_type) === 3 ? [] : ""),
      is_free: (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? 0 : 1),
      parent_id: '0',
      link_to: (!this.props.editData ? "none" : ""),
      link_to_names: (this.props.editData && this.props.editData.detail ? this.props.editData.detail.link_to_name : ''),
      advanceToggle: false,
      program_sidebar: false,
      date_prices: [{
        item_name: '',
        price: '',
        dates: undefined
      }],
      qty_base_discount: [{
        qty: '',
        discount_type: '',
        discount: '',
      }],
      discount_types: [
        { id: 'price', name: 'Price' },
        { id: 'percentage', name: 'Percentage' }
      ],

      //errors & loading
      message: false,
      success: true,
      errors: {},
      isLoader: false,

      change: false,
      editorLoaded: false
    }

    this.validator = new SimpleReactValidator({
      element: message => <p className="error-message">{message}</p>,
      messages: {
        required: this.props.t("EE_FIELD_IS_REQUIRED")
      },
    })
  }

  handleChange = (input, type) => e => {
    var value = (type === "dropdown" ? (e !== null ? e.value : null) : e.target.value);
    if (value === undefined || value === null) {
      this.setState({
        [input]: '',
        change: true
      })
    } else {
      this.setState({
        [input]: value,
        change: true
      })
    };
  }

  componentDidMount() {
    if (this.props.editData) {
      this.loadItem();
    } else {
      this.setState({
        parent_id: (this.props.parent_id !== undefined ? this.props.parent_id : 0),
        editorLoaded: true
      })
    }
    this.validator.showMessages();
    this.forceUpdate();
  }

  loadItem = () => {
    this._isMounted = true;
    this.setState({ preLoader: true });
    service.get(`${process.env.REACT_APP_URL}/eventsite/billing/items/edit/${this.props.editData.id}`)
      .then(
        response => {
          if (response.success) {
            if (response.data) {
              if (this._isMounted) {
                this.setState({
                  link_to_id: (Number(this.props.billing_item_type) === 3 ? response.data.link_to_id.split(",") : response.data.link_to_id),
                  link_to: response.data.link_to,
                  type: response.data.type,
                  date_prices: response.data.date_prices,
                  qty_base_discount: response.data.qty_base_discount,
                  group_id: this.props.editData.group_id,
                  item_number: this.props.editData.item_number,
                  price: this.props.editData.price,
                  vat: this.props.editData.vat,
                  is_default: Number(response.data.is_default),
                  is_required: Number(response.data.is_required),
                  qty: this.props.editData.qty,
                  total_tickets: this.props.editData.total_tickets,
                  item_name: this.props.editData.detail.item_name,
                  description: this.props.editData.detail.description,
                  advanceToggle: (response.data.date_prices.length > 0 || response.data.qty_base_discount.length > 0 ? true : false),
                  preLoader: false,
                  editorLoaded: true
                });
              }
            }
          }
        },
        error => { }
      );
  }

  save = e => {
    if (this.validator.allValid()) {
      this.setState({ isLoader: true });
      if (this.props.editData) {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/items/edit/${this.props.editData.id}`, this.state)
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
                this.props.listing(true);
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
      } else {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/items/create`, this.state)
          .then(
            response => {
              if (response.success) {
                this.setState({
                  'message': response.message,
                  'success': true,
                  isLoader: false,
                  errors: {},
                  change: false
                });
                this.props.listing(false);
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
    }
  }

  handleEditorChange = (e) => {
    this.setState({
      description: e.editor.getData(),
      change: true
    });
  }

  handleSession = e => {
    this.setState({
      program_sidebar: (this.props.editData && Number(this.props.editData.sold_tickets) > 0 ? false : !this.state.program_sidebar),
      change: true
    })
  }

  dateList = (from, to, index) => {
    if (from && to) {
      var startDate = moment(from);
      var endDate = moment(to);
      var now = startDate, dates = [];
      while (now.isSameOrBefore(endDate)) {
        dates.push(now.format('MM/DD/YYYY'));
        now.add(1, 'days');
      }
      var date_prices = [...this.state.date_prices];
      date_prices[index].value = dates;
      this.setState({
        date_prices
      })
    }
  }

  handleChangeprice = (name, key, element, type) => e => {
    var _element = [...this.state[element]];
    _element[key][name] = (type === "dropdown" ? e.value : e.target.value);
    this.setState({
      _element: _element,
      change: true
    })
  }

  addPriceDate = e => {
    e.preventDefault();
    var date_prices = [...this.state.date_prices];
    const newrow = {
      item_name: '',
      price: '',
      dates: undefined
    }
    date_prices = date_prices.concat(newrow);
    this.setState({
      date_prices: date_prices,
      change: true
    })
  }

  addDicountPrice = e => {
    e.preventDefault();
    var _element = [...this.state.qty_base_discount];
    const newrow = {
      qty: '',
      discount_type: '',
      discount: '',
    }
    _element = _element.concat(newrow);
    this.setState({
      qty_base_discount: _element,
      change: true
    })
  }

  deleteOption = (element, index) => e => {
    e.preventDefault();
    var date_prices = [...this.state[element]];
    date_prices.splice(index, 1);
    this.setState({
      [element]: date_prices,
      change: true
    });
  }

  updateFlag = input => e => {
    let value = this.state[input] === 1 ? 0 : 1;
    if (input === "is_required" && value === 1) {
      this.setState({
        is_required: 1,
        is_default: 1,
        change: true
      });
    } else if (input === "is_default" && value === 0) {
      this.setState({
        is_required: 0,
        is_default: 0,
        change: true
      });
    } else {
      this.setState({
        [input]: this.state[input] === 1 ? 0 : 1,
        change: true
      });
    }
  };

  linkTo = (link_to_id, link_to_names, link_to) => {
    this.setState({
      link_to: link_to,
      link_to_id: link_to_id,
      link_to_names: link_to_names,
      program_sidebar: false,
      change: true
    });
  }

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
            <div className="new-header">
              <h1 className="section-title">{t("BILLING_ITEMS_ADD_ITEMS_LABEL")}</h1>
            </div>
            <ConfirmationModal update={this.state.change} />
            {this.state.preLoader && <Loader />}
            {!this.state.preLoader && (
              <React.Fragment>
                <div
                  className={`option-wrapper form-items-section ${
                    this.props.editData ? "isGray" : ""
                    }`}
                >
                  {this.state.program_sidebar && <LinkTo linkTo={this.linkTo} link_to={this.state.link_to} link_to_id={this.state.link_to_id} onClose={this.handleSession.bind(this)} billing_item_type={this.props.billing_item_type} />}
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
                  <h3>{datamode}</h3>
                  <div className="row">
                    <div className="col-6">
                      {!in_array(this.state.type, ["admin_fee", "event_fee"]) && (
                        <DropDown
                          label={t("BILLING_ITEMS_GROUP_LABEL")}
                          listitems={this.state.groups}
                          required={false}
                          selected={this.state.group_id}
                          isSearchable='false'
                          isClearable={true}
                          selectedlabel={this.getSelectedLabel(this.state.groups, this.state.group_id)}
                          onChange={this.handleChange('group_id', 'dropdown')}
                        />
                      )}
                      <div className="field-item-name">
                        <Input
                          type='text'
                          label={t("BILLING_ITEMS_ITEM_NUMBER_LABEL")}
                          name='name'
                          required={false}
                          value={this.state.item_number}
                          onChange={this.handleChange('item_number', 'text')}
                          onBlur={() => this.validator.showMessageFor('item_number')}
                        />
                        <div className="info-sec-field">
                          <p>{t("BILLING_ITEMS_MAX_SIX_CHAR")}</p>
                        </div>
                        {this.state.errors.item_number && (
                          <p className="error-message">{this.state.errors.item_number}</p>
                        )}
                        {this.validator.message('item_number', this.state.item_number, 'required|max:6')}
                        <Input
                          type='text'
                          label={t("BILLING_ITEMS_ITEM_NAME")}
                          name='name'
                          required={true}
                          value={this.state.item_name}
                          onChange={this.handleChange('item_name', 'text')}
                          onBlur={() => this.validator.showMessageFor('item_name')}
                        />
                        <div className="info-sec-field">
                          <p>{t("BILLING_ITEMS_MAX_HUNDRED_CHAR")}</p>
                        </div>
                        {this.state.errors.item_name && (
                          <p className="error-message">{this.state.errors.item_name}</p>
                        )}
                        {this.validator.message('item_name', this.state.item_name, 'required')}
                      </div>
                      {!in_array(this.state.type, ["admin_fee", "event_fee"]) && (
                        <label onClick={this.handleSession.bind(this)} className='label-input-dropdown label-input'>
                          <input type="text" defaultValue={this.state.link_to_names} placeholder=" " readOnly />
                          <span>
                            {
                              (() => {
                                if (Number(this.props.billing_item_type) === 0)
                                  return t("BILLING_ITEMS_SELECT_PROGRAM");
                                else if (Number(this.props.billing_item_type) === 1)
                                  return t("BILLING_ITEMS_SELECT_TRACK")
                                else if (Number(this.props.billing_item_type) === 2)
                                  return t("BILLING_ITEMS_SELECT_WORKSHOP")
                                else if (Number(this.props.billing_item_type) === 3)
                                  return t("BILLING_ITEMS_SELECT_ATTENDEE_GROUP")
                              })()
                            }
                          </span>
                          <button className="btn">
                            <img src={require('img/ico-more.svg')} alt="" />
                          </button>
                        </label>
                      )}
                      {this.state.errors.link_to && (
                        <p className="error-message">{this.state.errors.link_to}</p>
                      )}
                      {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 && (
                        <React.Fragment>
                          <Input
                            type='text'
                            label={t("BILLING_ITEMS_ITEM_PRICE")}
                            name='name'
                            disabled={this.props.editData && Number(this.props.editData.sold_tickets) > 0 ? true : false}
                            required={true}
                            value={this.state.price}
                            onChange={this.handleChange('price', 'text')}
                            onBlur={() => this.validator.showMessageFor('price')}
                          />
                          {this.state.errors.price && (
                            <p className="error-message">{this.state.errors.price}</p>
                          )}
                          {this.validator.message('price', this.state.price, 'required|numeric')}
                          {this.props.event && this.props.event.eventsite_payment_setting && Number(this.props.event.eventsite_payment_setting.eventsite_apply_multi_vat) === 1 && (
                            <React.Fragment>
                              <Input
                                type='text'
                                label={t("BILLING_ITEMS_VAT")}
                                name='name'
                                disabled={this.props.editData && Number(this.props.editData.sold_tickets) > 0 ? true : false}
                                required={false}
                                value={this.state.vat}
                                onChange={this.handleChange('vat', 'text')}
                              />
                              {this.state.errors.vat && (
                                <p className="error-message">{this.state.errors.vat}</p>
                              )}
                            </React.Fragment>
                          )}
                        </React.Fragment>
                      )}
                      <h5>{t("BILLING_ITEMS_DESC")}</h5>
                      {this.state.editorLoaded && (
                        <CKEditor
                          data={this.state.description}
                          config={{
                            enterMode: CKEditor.ENTER_BR,
                            fullPage: true,
                            allowedContent: true,
                            extraAllowedContent: "style[id]",
                            htmlEncodeOutput: false,
                            entities: false,
                            height: 250,
                          }}
                          onChange={this.handleEditorChange}
                          onBeforeLoad={(CKEDITOR) => (CKEDITOR.disableAutoInline = true)}
                        />
                      )}
                      {this.state.errors.description && (
                        <p className="error-message">
                          {this.state.errors.description}
                        </p>
                      )}
                      <div className="bottom-form-area">
                        {!in_array(this.state.type, ["admin_fee", "event_fee"]) && (
                          <React.Fragment>
                            <Input
                              type='text'
                              label={t("BILLING_ITEMS_MAX_QTY_LABEL")}
                              name='name'
                              required={false}
                              value={this.state.qty}
                              onChange={this.handleChange('qty', 'text')}
                            />
                            {this.state.errors.qty && (
                              <p className="error-message">{this.state.errors.qty}</p>
                            )}
                            {this.state.link_to !== "program" && (
                              <React.Fragment>
                                <Input
                                  type='text'
                                  label={t("BILLING_ITEMS_TOTAL_TICKETS_LABEL")}
                                  name='name'
                                  required={false}
                                  value={this.state.total_tickets}
                                  onChange={this.handleChange('total_tickets', 'text')}
                                />
                                {this.state.errors.total_tickets && (
                                  <p className="error-message">{this.state.errors.total_tickets}</p>
                                )}
                              </React.Fragment>
                            )}
                          </React.Fragment>
                        )}
                        <div className="settings-registration">
                          <h3>{t("BILLING_ITEMS_SETTING_FOR_ITEMS_ON_REG_FORM")}</h3>
                          <div className="checkbox-row checkbox-flex-style">
                            <p>{t("BILLING_ITEMS_ITEM_DEFAULT_LABEL")}</p>
                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('is_default')}
                              checked={this.state.is_default} type="checkbox" /><span></span></label>
                          </div>
                          <div className="checkbox-row checkbox-flex-style">
                            <p>{t("BILLING_ITEMS_ITEM_MANDATORY_LABEL")}</p>
                            <label className="custom-checkbox-toggle"><input onChange={this.updateFlag('is_required')}
                              checked={this.state.is_required} type="checkbox" /><span></span></label>
                          </div>
                        </div>
                        {this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 && (
                          <div className="label-advance-setting">
                            <span onClick={() => this.setState({ advanceToggle: !this.state.advanceToggle })} className="advance-settings"><i className="material-icons">{this.state.advanceToggle ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}</i>{t("BILLING_ITEMS_ADVANCE_SETTING")}</span>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                  {this.state.advanceToggle && this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 &&
                    <React.Fragment>
                      <div className="form-date-base">
                        <h4>{t("BILLING_ITEMS_DATE_BASE_RULES")}</h4>
                        <div className="wrapp-form-date-base">
                          {this.state.date_prices && this.state.date_prices.map((list, key) => {
                            return (
                              <div key={key} className="row d-flex">
                                <div className="col-4">
                                  <Input
                                    type="text"
                                    label={t("BILLING_ITEMS_ITEM_NAME")}
                                    value={list.item_name ? list.item_name : ''}
                                    onChange={this.handleChangeprice("item_name", key, 'date_prices', "text")}
                                    required={false}
                                  />
                                </div>
                                <div className="col-2">
                                  <Input
                                    type="number"
                                    label={t("BILLING_ITEMS_PRICE_LABEL")}
                                    value={list.price ? list.price : ''}
                                    onChange={this.handleChangeprice("price", key, 'date_prices', "text")}
                                    required={false}
                                  />
                                </div>
                                <div className="col-6">
                                  <DateRange
                                    type='text'
                                    name='DateRange'
                                    dateList={this.dateList}
                                    required={true}
                                    datavalue={list.value && list.value}
                                    disabledDays={true}
                                    index={key}
                                  />
                                </div>
                                <span onClick={this.deleteOption('date_prices', key)} className="btn_remove_price"><img src={require('img/ico-delete.svg')} alt="" /></span>
                              </div>
                            )
                          })}
                          <span onClick={this.addPriceDate.bind(this)} className="btn-add-form-row"><i className="material-icons">add</i> {t("BILLING_ITEMS_ADD_RULE_LABEL")}</span>
                        </div>
                      </div>
                      {this.state.errors.date_prices && (
                        <p className="error-message">{this.state.errors.date_prices}</p>
                      )}
                      <div className="form-date-base">
                        <h4>{t("BILLING_ITEMS_QTY_BASE_RULES")}</h4>
                        <div className="wrapp-form-date-base">
                          {this.state.qty_base_discount && this.state.qty_base_discount.map((list, key) => {
                            return (
                              <div key={key} className="row d-flex">
                                <div className="col-4">
                                  <Input
                                    type="text"
                                    label={t("BILLING_ITEMS_QTY_LABEL")}
                                    value={list.qty ? list.qty : ''}
                                    onChange={this.handleChangeprice("qty", key, 'qty_base_discount', "text")}
                                    required={false}
                                  />
                                </div>
                                <div className="col-4">
                                  <DropDown
                                    label={t("BILLING_ITEMS_DISCOUNT_TYPE_LABEL")}
                                    listitems={this.state.discount_types}
                                    required={true}
                                    selected={list.discount_type}
                                    isSearchable='false'
                                    selectedlabel={this.getSelectedLabel(this.state.discount_types, list.discount_type)}
                                    onChange={this.handleChangeprice("discount_type", key, 'qty_base_discount', "dropdown")}
                                  />
                                </div>
                                <div className="col-4">
                                  <Input
                                    type="number"
                                    label={t("BILLING_ITEMS_DISCOUNT_LABEL")}
                                    value={list.discount ? list.discount : ''}
                                    onChange={this.handleChangeprice("discount", key, 'qty_base_discount', "text")}
                                    required={false}
                                  />
                                </div>
                                <span onClick={this.deleteOption('qty_base_discount', key)} className="btn_remove_price"><img src={require('img/ico-delete.svg')} alt="" /></span>
                              </div>
                            )
                          })}
                          <span onClick={this.addDicountPrice.bind(this)} className="btn-add-form-row"><i className="material-icons">add</i> {t("BILLING_ITEMS_ADD_RULE_LABEL")}</span>
                        </div>
                      </div>
                    </React.Fragment>
                  }
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
                      onClick={() => onCancel("page")}
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
    );
  }
}

function mapStateToProps(state) {
  const { event } = state;
  return {
    event
  };
}

export default connect(mapStateToProps)(withTranslation()(FormItem));