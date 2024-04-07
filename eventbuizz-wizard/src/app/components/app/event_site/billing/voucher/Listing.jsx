import * as React from "react";
import Img from 'react-image';
import { NavLink } from 'react-router-dom';
import Loader from '@/app/forms/Loader';
import FormVoucher from "@/app/event_site/billing/voucher/FormVoucher";
import { Translation } from "react-i18next";
import { confirmAlert } from 'react-confirm-alert'; // Import
import { connect } from 'react-redux';
import { service } from 'services/service';
import Pagination from "react-js-pagination";
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import 'sass/billing.scss';

import in_array from "in_array";

class Listing extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            type: '',
            query: '',
            sort_by: 'voucher_name',
            order_by: 'ASC',
            vouchers: [],
            permissions: [],
            displayPanel: true,
            editData: undefined,

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

            checkedItems: new Map(),

            prev: "/event_site/billing-module/items",
            next: "/event_site/billing-module/purchase-policy"
        }

        this.onSorting = this.onSorting.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing(1);
        document.body.addEventListener('click', this.removePopup.bind(this));
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

    componentWillUnmount() {
        this._isMounted = false;
    }

    listing = (activePage = 1, loader = false, type = "save") => {
        this.setState({ preLoader: (!loader ? true : false) });
        service.post(`${process.env.REACT_APP_URL}/eventsite/billing/voucher/listing/${activePage}`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            if (this._isMounted) {
                                this.setState({
                                    vouchers: response.data.data,
                                    permissions: response.permissions,
                                    activePage: response.data.current_page,
                                    total: response.data.total,
                                    from: response.data.from,
                                    to: response.data.to,
                                    displayPanel: true,
                                    editData: undefined,
                                    type: "",
                                    preLoader: false,
                                    checkedItems: new Map()
                                }, () => {
                                });
                            }
                        }
                    }
                },
                error => { }
            );
    }

    handleEdit = (voucher) => e => {
        e.preventDefault();
        this.setState({
            type: voucher.type,
            displayPanel: false,
            stateToggle: false,
            childEditMode: true,
            editData: voucher
        });
    }

    handleAdd = (type) => e => {
        e.preventDefault();
        this.setState({
            type: type,
            displayPanel: false,
            stateToggle: false
        })
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

    handleToggle = e => {
        e.preventDefault();
        this.setState({
            stateToggle: !this.state.stateToggle
        })
    }

    cancel = value => {
        this.setState({
            type: "",
            displayPanel: true,
            parent_id: '',
            childEditMode: false,
            editData: undefined
        })
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
                self.listing(1);
            }, 1000)
        });
    }

    updateStatus = (id, status) => e => {
        service.put(`${process.env.REACT_APP_URL}/eventsite/billing/voucher/update-status/${id}`, { id: id, status: status })
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.listing(1);
                        }
                    }
                },
                error => { }
            );
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

    removeVoucher = (id) => e => {
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
                                                        `${process.env.REACT_APP_URL}/eventsite/billing/voucher/delete/${id}`,
                                                        { ids: ids }
                                                    )
                                                    .then(
                                                        response => {
                                                            if (response.success) {
                                                                this.listing(1);
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

    removePopup = e => {
        if (e.target.className !== 'btn active') {
            const items = document.querySelectorAll(".parctical-button-panel .btn");
            for (let i = 0; i < items.length; i++) {
                const element = items[i];
                element.classList.remove("active");
            }
        }
    }

    handleLimit = (limit) => e => {
        this.setState(prevState => ({
            limit: limit,
        }));
    };

    handleDrowon = e => {
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

    onSorting(event) {
        this.setState({
            order_by: event.target.attributes.getNamedItem('data-order').value,
            sort_by: event.target.attributes.getNamedItem('data-sort').value,
        });
    }

    export = () => {
        service.download(`${process.env.REACT_APP_URL}/eventsite/billing/vouchers/export`)
            .then(response => {
                response.blob().then(blob => {
                    if (window.navigator && window.navigator.msSaveOrOpenBlob) { // for IE
                        var csvData = new Blob([blob], { type: 'text/csv' });
                        window.navigator.msSaveOrOpenBlob(csvData, "export.csv");
                    } else { 
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = 'export.csv';
                        a.click();
                    }
                });
            });
    }

    render() {
        const selected_rows_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size;

        return (
            <Translation>
                {(t) => (
                    <React.Fragment>
                        {this.state.preLoader && <Loader />}
                        {!this.state.preLoader && (
                            <React.Fragment>
                                <div style={{ height: "100%" }}>
                                    {this.state.displayPanel && (
                                        <div style={{ margin: "0" }} className="new-header clearfix">
                                            {!this.props.largeScreen && (
                                                <div className="row">
                                                    <div className="col-6">
                                                        <h1 className="section-title">{t("BILLING_VOUCHERS_MAIN_HEADING")}</h1>
                                                        <p>
                                                            {t("BILLING_VOUCHERS_SUB_HEADING")}
                                                        </p>
                                                    </div>
                                                    {Number(this.state.permissions["add"]) === 1 && (
                                                        <div className="col-6">
                                                            <div className="right-panel-billingitem float-right">
                                                                <button
                                                                    className={`${this.state.stateToggle && "active"} btn_addNew_main`}
                                                                    onClick={this.handleToggle.bind(this)}
                                                                >
                                                                    <span className="icons">
                                                                        <Img src={require("img/ico-plus-lg.svg")} />
                                                                    </span>
                                                                </button>
                                                                <div className="drop_down_panel">
                                                                    <button
                                                                        className="btn_addNew"
                                                                        onClick={this.handleAdd("order")}
                                                                    >
                                                                        {t("BILLING_VOUCHERS_ORDER_LABEL")}
                                                                    </button>
                                                                    <button
                                                                        className="btn_addNew"
                                                                        onClick={this.handleAdd("billing_items")}
                                                                    >
                                                                        {t('BILLING_VOUCHERS_ITEMS_LABEL')}
                                                                    </button>
                                                                </div>
                                                                <button style={{ marginLeft: '3px' }} onClick={() => this.export()} className="btn_addNew_main">
                                                                    <span className="icons">
                                                                        <Img src={require("img/ico-download-alt.svg")} />
                                                                    </span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    )}
                                                </div>
                                            )}
                                            <div style={{ marginBottom: '10px' }} className="d-flex row align-items-center">
                                                <div className="col-6">
                                                    {(this.state.vouchers.length > 0 && !this.state.query) || this.state.query ? (
                                                        <input value={this.state.query} name="query" type="text"
                                                            placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                                        />
                                                    ) : ''}
                                                </div>
                                                {this.state.vouchers.length > 0 && (
                                                    <div className="col-6">
                                                        <div className="panel-right-table d-flex justify-content-end">
                                                            {!this.props.largeScreen && (
                                                                <button onClick={this.props.handleLargeScreen} className="btn btn-fullscreen">
                                                                    <img src={require('img/fullscreen.svg')} alt="" />
                                                                </button>
                                                            )}
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
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    )}
                                    {in_array(this.state.type, ['billing_items', 'order', 'vat_free']) && (
                                        <div className="parctical-info-widgets">
                                            <FormVoucher
                                                datamode={
                                                    this.state.childEditMode
                                                        ? t("BILLING_VOUCHERS_EDIT_INNER_VOUCHER")
                                                        : t("BILLING_VOUCHERS_ADD_INNER_VOUCHER")
                                                }
                                                onCancel={this.cancel}
                                                listing={this.listing}
                                                type={this.state.type}
                                                editData={this.state.editData}
                                            />
                                        </div>
                                    )}
                                    {this.state.displayPanel && this.state.vouchers.length > 0 && (
                                        <div className="wrapper-billing-section voucher-select-items voucher-elements-main">
                                            <div style={{borderTop: '0px'}} className="row d-flex header-billing">
                                                <div className="grid-1">
                                                    <label className="checkbox-items">
                                                        <input
                                                            id="selectall"
                                                            checked={(selected_rows_length === this.state.vouchers.length ? true : false)}
                                                            onChange={this.handleSelectAll.bind(this)}
                                                            type="checkbox"
                                                            name="selectall"
                                                        />
                                                        <span></span>
                                                    </label>
                                                    <div style={{ marginLeft: 0 }} className="parctical-button-panel">
                                                        <div className="dropdown">
                                                            <button
                                                                style={{ margin: 0 }}
                                                                onClick={this.handleDropdown.bind(this)}
                                                                className="btn"
                                                            >
                                                                <i className="material-icons">
                                                                    keyboard_arrow_down
                                                        </i>
                                                            </button>
                                                            <div className="dropdown-menu leftAlign">
                                                                {selected_rows_length > 0 && (
                                                                    <button
                                                                        className="dropdown-item"
                                                                        onClick={this.removeVoucher("selected")
                                                                        }
                                                                    >
                                                                        {t("G_DELETE_SELECTED")}
                                                                    </button>
                                                                )}
                                                                <button
                                                                    className="dropdown-item"
                                                                    onClick={this.removeVoucher("all")
                                                                    }
                                                                >
                                                                    {t("G_DELETE_ALL")}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="grid-2">
                                                    <strong>{t("BILLING_VOUCHERS_ITEM_LABEL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="voucher_name" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "voucher_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "voucher_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div style={{width: 'auto'}} className="grid-3">
                                                    <strong>{t("BILLING_VOUCHERS_TYPE_LABEL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="type" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.type === "ASC" && this.state.sort_by === "type" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "type" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="grid-4">
                                                    <strong>{t("BILLING_VOUCHERS_VOUCHER_CODE_LABEL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="code" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.code === "ASC" && this.state.sort_by === "code" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "code" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="grid-5">
                                                    <strong>{t("BILLING_VOUCHERS_TOTAL_LABEL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="usage" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.usage === "ASC" && this.state.sort_by === "usage" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "usage" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="grid-6">
                                                    <strong>{t("BILLING_VOUCHERS_USED_LABEL")}</strong>
                                                </div>
                                                <div className="grid-6">
                                                    <strong>{t("BILLING_VOUCHERS_AVAILABLE_LABEL")}</strong>
                                                </div>
                                                <div className="grid-7">
                                                    <strong>{t("BILLING_VOUCHERS_EXPIRY_DATE_LABEL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="expiry_date" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.expiry_date === "ASC" && this.state.sort_by === "expiry_date" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "expiry_date" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                {!this.props.largeScreen && (
                                                    <div className="grid-8">
                                                    </div>
                                                )}
                                            </div>
                                            {this.state.vouchers.map((voucher, index) => (
                                                <div className={`${this.state.checkedItems.get(voucher.id.toString()) && 'selected'} row check-box-list`} key={index}>
                                                    <div className="grid-1">
                                                        <label className="checkbox-items">
                                                            <input
                                                                type="checkbox"
                                                                name={voucher.id.toString()}
                                                                disabled={in_array(voucher.delete, ["delete", "archive"]) ? false : true}
                                                                checked={(this.state.checkedItems.get(
                                                                    voucher.id.toString()
                                                                ))}
                                                                onChange={this.handleCheckbox}
                                                            />
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                    <div className="grid-2">
                                                        <p>{voucher.value}</p>
                                                    </div>
                                                    <div style={{width: 'auto'}} className="grid-3">
                                                        <p>{voucher.type === "billing_items" ? "Billing Items" : "Order"}</p>
                                                    </div>
                                                    <div className="grid-4">
                                                        <p>{voucher.code}</p>
                                                    </div>
                                                    <div className="grid-5">
                                                        <p>{voucher.type === "billing_items" ? "---" : voucher.usage}</p>
                                                    </div>
                                                    <div className="grid-6">
                                                        <p>{voucher.type === "billing_items" ? "---" : voucher.number_of_usage_in_order}</p>
                                                    </div>
                                                    <div className="grid-6">
                                                        <p>{voucher.type === "billing_items" ? "---" : voucher.available}</p>
                                                    </div>
                                                    <div className="grid-7">
                                                        <p>{voucher.display_expiry_date}</p>
                                                    </div>
                                                    {!this.props.largeScreen && (
                                                        <div className="grid-8">
                                                            <div className="practical-edit-panel">
                                                                <span className="btn-edit" onClick={this.updateStatus(voucher.id, (Number(voucher.status) === 1 ? 0 : 1))}><i className="icons"><Img style={{ maxWidth: "18px" }} src={require(`img/ico-feathereye${Number(voucher.status) !== 1 ? '-alt' : ''}.svg`)} /></i></span>
                                                                <span className="btn-edit" onClick={this.handleEdit(
                                                                    voucher
                                                                )}>
                                                                    <Img src={require('img/ico-edit.svg')} />
                                                                </span>
                                                                <span className="btn-edit" onClick={this.removeVoucher(
                                                                    voucher.id
                                                                )}>
                                                                    <Img src={require('img/ico-delete.svg')} />
                                                                </span>
                                                            </div>
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    )}
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
                                {this.state.displayPanel && (
                                    <div className="bottom-component-panel clearfix">
                                        {!this.props.largeScreen ? (
                                            <React.Fragment>
                                                <NavLink
                                                    target="_blank"
                                                    className="btn btn-preview float-left"
                                                    to={`/event/preview`}
                                                >
                                                    <i className="material-icons">remove_red_eye</i>
                                                    {t("G_PREVIEW")}
                                                </NavLink>
                                                {this.state.prev !== undefined && (
                                                    <NavLink className="btn btn-prev-step" to={this.state.prev}>
                                                        <span className="material-icons">keyboard_backspace</span>
                                                    </NavLink>
                                                )}
                                                {this.state.next !== undefined && (
                                                    <NavLink className="btn btn-next-step" to={this.state.next}>
                                                        {t("G_NEXT")}
                                                    </NavLink>
                                                )}
                                            </React.Fragment>
                                        ) : (
                                                <button className="btn btn-save-next" onClick={this.props.handleLargeScreen}>{t('G_CLOSE')}
                                                </button>
                                            )}
                                    </div>
                                )}
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

export default connect(mapStateToProps)(Listing);