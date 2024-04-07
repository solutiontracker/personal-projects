import React, { Component } from 'react';
import Loader from '@/app/forms/Loader';
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Select from 'react-select';
import { service } from "services/service";
import { Translation } from "react-i18next";


export default class VoucherItems extends Component {
  _isMounted = false;

  constructor(props) {
    super(props);
    this.state = {
      currency: '',
      query: '',
      selected_vouchers: [],
      selectedItems: (this.props.selectedItems && this.props.selectedItems.length > 0 ? this.props.selectedItems : []),
      items: [],

      //loading
      preLoader: true,

      typing: false,
      typingTimeout: 0,

      message: false,
      success: true,
      errorMessage: false,

      sort_by: 'sort_order',
      order_by: 'ASC'
    }

    this.onSorting = this.onSorting.bind(this);
  }

  componentDidMount() {
    this.setState({ selected_vouchers: this.props.selected_vouchers })
    const _body = document.getElementsByTagName('body');
    _body[0].classList.add('noscroll');
    document.body.addEventListener('click', this.removePopup.bind(this));
    this.loadItems();
  }

  componentDidUpdate(prevProps, prevState) {
    const { order_by, sort_by } = this.state;
    if (order_by !== prevState.order_by || sort_by !== prevState.sort_by) {
      this.loadItems();
    }
  }

  loadItems = () => {
    var _url;
    this._isMounted = true;
    this.setState({ preLoader: true });
    if (this.props.editData) {
      _url = `${process.env.REACT_APP_URL}/eventsite/billing/voucher/items/${this.props.editData.id}`;
    } else {
      _url = `${process.env.REACT_APP_URL}/eventsite/billing/voucher/items`;
    }
    service.put(_url, this.state)
      .then(
        response => {
          if (response.success) {
            if (response.items) {
              if (this._isMounted) {
                this.setState({
                  items: response.items,
                  currency: response.currency,
                  preLoader: false,
                });
              }
            }
          }
        },
        error => { }
      );
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
    const _body = document.getElementsByTagName('body');
    _body[0].classList.remove('noscroll');
  }

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

  handleChange = (item, index) => e => {
    var data = [...this.state.items];
    if (item === 'checked') {
      data[index][item] = e.target.checked;
    } else {
      data[index][item] = Number(e.target.value);
    }
    this.setState({ data })
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
        self.loadItems()
      }, 1000)
    });
  }

  onSorting(event) {
    this.setState({
      order_by: event.target.attributes.getNamedItem('data-order').value,
      sort_by: event.target.attributes.getNamedItem('data-sort').value,
    });
  }

  onChangeSelect = (item, index, value) => {
    var data = [...this.state.items];
    data[index][item] = value.value;
    this.setState({ data })
  };

  handleSelectAll = e => {
    const check = e.target.checked;
    var data = this.state.items;
    data.forEach(function (ob) {
      ob.checked = check;
    });
    this.setState({ items: data });
  };

  handleSave = (e) => {
    e.preventDefault();
    var _selected = document.querySelectorAll('.voucher-select-items .selected');
    if (_selected && _selected !== undefined && _selected.length > 0) {
      let counter = 0;
      for (let i = 0; i < _selected.length; i++) {
        const element = _selected[i];
        const _child = element.getElementsByClassName('input-number')
        if (Number(_child[0].value) !== 0 && Number.isInteger(Number(_child[0].value)) && Number(_child[1].value) !== 0 && Number.isInteger(Number(_child[1].value))) {
          element.classList.remove('error');
        } else {
          element.classList.add('error');
          counter++;
        }

      }
      if (counter > 0) {
        this.setState({
          errorMessage: true
        })
      } else {
        this.setState({
          errorMessage: false
        })
        this.props.saveItems(this.state.items)
      }
    }
  }

  render() {

    this.getSelectedLabel = (item, id) => {
      if (item && item.length > 0 && id) {
        let obj = item.find(o => o.value.toString() === id.toString());
        return obj;
      }
    }

    const style = {
      control: base => ({
        ...base,
        boxShadow: 'none'
      })
    };

    const options = [
      { value: '1', label: 'Amount' },
      { value: '2', label: 'Percentage' }
    ]

    const selected_rows_length = this.state.items.filter(function (item, i) {
      return item.checked === true;
    }).length;

    return (
      <Translation>
        {
          t =>
            <div className="wrapper-popup popup-program-session">
              <div className="wrapper-sidebar">
                {this.state.preLoader &&
                  <Loader />
                }

                {!this.state.preLoader && (
                  <React.Fragment>
                    <header>
                      <h3>{t("BILLING_VOUCHERS_SELECT_ITEMS_LABEL")}</h3>
                    </header>
                    <div className="bottom-content">
                      <div className="new-header">
                        <input value={this.state.query} name="query" type="text"
                          placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                        />
                      </div>
                      <div className="wrapper-billing-section voucher-select-items">
                        {this.state.errorMessage &&
                          <div style={{ padding: '1px 0 0 0', margin: '0 -7px' }}>
                            <AlertMessage
                              className="alert alert-danger"
                              title="Error"
                              content={t('BILLING_VOUCHERS_ITEMS_REQUIRED_ALL_FIELDS_ERROR')}
                              icon="info"
                            />
                          </div>
                        }
                        <div className="text-right"><p className="eb-mandatory-field"><span className="error-star">*</span> Indicates mandatory fields</p></div>
                        <div style={{borderTop: '0px'}} className="row d-flex header-billing">
                          <div className="grid-1">
                            <label className="checkbox-items">
                              <input
                                id="selectall"
                                checked={(selected_rows_length === this.state.items.length ? true : false)}
                                onChange={this.handleSelectAll.bind(this)}
                                type="checkbox"
                                name="selectall"
                              />
                              <span></span>
                            </label>
                          </div>
                          <div className="grid-2">
                            <strong>{t("BILLING_VOUCHERS_ITEM_NUMBER_LABEL").toUpperCase()}</strong>
                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="id" onClick={this.onSorting} className="material-icons">
                              {(this.state.order_by === "ASC" && this.state.sort_by === "id" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "id" ? "keyboard_arrow_up" : "unfold_more"))}
                            </i>
                          </div>
                          <div className="grid-3">
                            <strong>{t("BILLING_VOUCHERS_ITEM_NAME").toUpperCase()}</strong>
                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="item_name" onClick={this.onSorting} className="material-icons">
                              {(this.state.order_by === "ASC" && this.state.sort_by === "item_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "item_name" ? "keyboard_arrow_up" : "unfold_more"))}
                            </i>
                          </div>
                          <div className="grid-4">
                            <strong><span className="error-star">*</span> {t("BILLING_VOUCHERS_NUMBER_OF_USAGE").toUpperCase()}</strong>
                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="useage" onClick={this.onSorting} className="material-icons">
                              {(this.state.order_by === "ASC" && this.state.sort_by === "useage" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "useage" ? "keyboard_arrow_up" : "unfold_more"))}
                            </i>
                          </div>
                          <div className="grid-5">
                            <strong>{t("BILLING_VOUCHERS_DISCOUNT_TYPE_LABEL").toUpperCase()}</strong>
                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="discount_type" onClick={this.onSorting} className="material-icons">
                              {(this.state.order_by === "ASC" && this.state.sort_by === "discount_type" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "discount_type" ? "keyboard_arrow_up" : "unfold_more"))}
                            </i>
                          </div>
                          <div className="grid-6">
                            <strong><span className="error-star">*</span> {t("BILLING_VOUCHERS_VALUE_LABEL").toUpperCase()}</strong>
                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="amount" onClick={this.onSorting} className="material-icons">
                              {(this.state.order_by === "ASC" && this.state.sort_by === "amount" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "amount" ? "keyboard_arrow_up" : "unfold_more"))}
                            </i>
                          </div>
                          <div className="grid-7">
                            <strong>{t("BILLING_VOUCHERS_PRICE_LABEL").toUpperCase()} ({this.state.currency})</strong>
                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="price" onClick={this.onSorting} className="material-icons">
                              {(this.state.order_by === "ASC" && this.state.sort_by === "price" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "price" ? "keyboard_arrow_up" : "unfold_more"))}
                            </i>
                          </div>
                        </div>
                        {this.state.items && this.state.items.map((item, key) => {
                          return (
                            <div key={key} className={`${item.checked && 'selected'} row d-flex align-items-center`}>
                              <div className="grid-1 check-box-list">
                                <label className="checkbox-items">
                                  <input onChange={this.handleChange('checked', key)} type="checkbox" name="selectall" checked={item.checked} />
                                  <span></span>
                                </label>
                              </div>
                              <div className="grid-2">
                                <p>{item.id}</p>
                              </div>
                              <div className="grid-3">
                                <p>{(item.detail ? item.detail.item_name : '')}</p>
                              </div>
                              <div className="grid-4">
                                <input type="text" className="input-number" pattern="\d+" onChange={this.handleChange('useage', key)} value={item.useage} />
                              </div>
                              <div className="grid-5">
                                {/* <select onChange={this.handleChange('discount_type', key)} defaultValue={item.discount_type}>
                                  <option value="1">Amount</option>
                                  <option value="2">Percentage</option>
                                </select> */}
                                <label style={{ width: '100%' }} className="simple-drop-down">
                                  <Select
                                    options={options}
                                    isSearchable={false}
                                    value={[this.getSelectedLabel(options, item.discount_type ? item.discount_type.toString() : '1')]}
                                    components={{ IndicatorSeparator: null }}
                                    onChange={(value) => this.onChangeSelect('discount_type', key, value)}
                                    styles={style}
                                  />
                                </label>
                              </div>
                              <div className="grid-6">
                                <input type="text" className="input-number" pattern="\d+" onChange={this.handleChange('discount_price', key)} value={item.discount_price} />
                                <span className="value-meter">{Number(item.discount_type) === 2 ? '%' : this.state.currency}</span>
                              </div>
                              <div className="grid-7">
                                <p><strong>{item.price}</strong></p>
                              </div>
                            </div>
                          )
                        })}
                      </div>
                    </div>
                    <div className="bottom-component-panel clearfix">
                      <button
                        onClick={this.props.onClose}
                        style={{ minWidth: "124px" }}
                        data-type="save"
                        className="btn btn btn-save"
                      >
                        Cancel
                    </button>
                      <button onClick={this.handleSave.bind(this)} data-type="save-next" className="btn btn-save-next">{t("G_SAVE")}</button>
                    </div>
                  </React.Fragment>
                )}
              </div>
            </div>
        }
      </Translation>
    );
  }
}
