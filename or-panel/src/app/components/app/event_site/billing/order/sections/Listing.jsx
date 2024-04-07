
import * as React from "react";
import Img from 'react-image';
import { NavLink } from 'react-router-dom';
import Loader from '@/app/forms/Loader';
import { Translation, withTranslation } from "react-i18next";
import { confirmAlert } from 'react-confirm-alert'; // Import
import { connect } from 'react-redux';
import { service } from 'services/service';
import Pagination from "react-js-pagination";
import moment from 'moment';
import DropDown from '@/app/forms/DropDown';
import { ReactSVG } from 'react-svg';
import 'react-confirm-alert/src/react-confirm-alert.css'; // Import css
import 'sass/billing.scss';


import in_array from "in_array";
import OrderAttendees from "./OrderAttendees";

const module_routes = { "attendees": "/event/module/attendees", "agendas": "/event/module/programs", "speakers": "/event/module/speakers", "infobooth": "/event/module/practical-information", "additional_info": "/event/module/additional-information", "general_info": "/event/module/general-information", "maps": "/event/module/map", "subregistration": "/event/module/sub-registration", "ddirectory": "/event/module/documents" };

const pStyles = {
    fontSize: '12px',
    whiteSpace: 'nowrap',
    overflow: 'hidden',
    textOverflow: 'ellipsis'
}
const flexGrow = {
    flexGrow: 1,
    flexBasis: 0
}

class Listing extends React.Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            type: '',
            payment_status: '',
            query: '',
            sort_by: 'created_at',
            order_by: 'DESC',
            orders: [],
            permissions: [],
            displayPanel: true,
            editData: undefined,
            cancel_order_note: 1,
            //pagination
            limit: 10,
            total: '',
            from: 0,
            to: 0,
            activePage: 1,

            //errors & loading
            preLoader: true,
            downloadLoader: false,

            typing: false,
            typingTimeout: 0,

            message: false,
            success: true,

            checkedItems: new Map(),

            prev: (this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 1 ? '/event_site/billing-module/purchase-policy' : '/event_site/billing-module/items'),
            next: '/event_site/billing-module/waiting-list-orders'

        }
        this.myToggleRef = React.createRef();
        this.onSorting = this.onSorting.bind(this);
        this.handleClickOutside = this.handleClickOutside.bind(this);
        this.myRef = React.createRef();
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing(1);
        document.body.addEventListener('click', this.removePopup.bind(this));
    }

    componentDidUpdate(prevProps, prevState) {
        const { order_by, sort_by } = this.state;
        if (order_by !== prevState.order_by || sort_by !== prevState.sort_by || prevState.limit !== this.state.limit || prevState.type !== this.state.type || prevState.payment_status !== this.state.payment_status) {
            this.listing(1);
        }
        document.addEventListener("mousedown",  this.handleClickOutside);
    }

    handleClickOutside = (event) => {
        if (this.myToggleRef && this.myToggleRef.current && !this.myToggleRef.current.contains(event.target)) {
            this.setState({
                stateToggle: false
            });
        }
    };

    handlePageChange = (activePage) => {
        this.listing(activePage);
    }

    componentWillUnmount() {
        document.removeEventListener("mousedown",  this.handleClickOutside);
        this._isMounted = false;
    }

    listing = (activePage = 1, loader = false, type = "save") => {
        this.setState({ preLoader: (!loader ? true : false) });
        service.post(`${process.env.REACT_APP_URL}/eventsite/billing/orders/listing/${activePage}`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (response.data) {
                            if (this._isMounted) {
                                this.setState({
                                    orders: response.data.data,
                                    permissions: response.permissions,
                                    activePage: response.data.current_page,
                                    total: response.data.total,
                                    from: response.data.from,
                                    to: response.data.to,
                                    displayPanel: true,
                                    editData: undefined,
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

    handleEdit = (order) => e => {
        e.preventDefault();
        this.setState({
            displayPanel: false,
            stateToggle: false,
            childEditMode: true,
            editData: order
        });
    }

    handleAdd = () => e => {
        e.preventDefault();
        this.setState({
            displayPanel: false,
            stateToggle: false
        })
    }

    handleToggle = e => {
        e.preventDefault();
        this.setState({
            stateToggle: !this.state.stateToggle
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

    cancelOrder(id, cancel_order_note) {
        service.destroy(
            `${process.env.REACT_APP_URL}/eventsite/billing/order/delete/${id}`
        )
            .then(
                response => {
                    if (response.success) {
                        this.listing(1);
                    } else {
                        this.setState({
                            'message': response.message,
                            'success': false
                        });
                    }
                },
                error => {
                }
            );
    }


    handleToggleNote = (cancel_order_note) => e => {
        e.preventDefault();
        this.setState({
            cancel_order_note: cancel_order_note,
            update: true
        })
    }

    cancel = value => {
        this.setState({
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

    handleToggleNote = e => {


        this.setState({
            cancel_order_note: e.target.value,
            update: true
        },()=>{
            console.log(this.state.cancel_order_note);
        })
    }

    removeOrder = (id) => e => {

        this.setState({
            cancel_order_note: 1,
            update: true
        });

        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div style={{ maxWidth: "800px"}} className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t('BILLING_ORDERS_CANCEL_ORDER_CONFIRMATION')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <div className="container-map">
                                            <form className="navigation">
                                                <label style={{fontSize: "20px"}}>
                                                    <input defaultChecked={true} type="radio" onChange={this.handleToggleNote.bind(this)} name="cancel_order" defaultValue="1"/>
                                                    <i className="material-icons">
                                                    </i> {t('BILLING_ORDERS_CANCEL_ORDER_CREATE_AND_SEND_CREDIT_NOTE')} </label> <br/>
                                                <p style={{paddingLeft: "20px"}}>This action will cancel the order. Create a credit note attached an email and send it to the attendee.
                                                    It will also send an EAN credit note.</p>

                                                <label  style={{fontSize: "20px"}}>
                                                    <input  type="radio" onChange={this.handleToggleNote.bind(this)} name="cancel_order" defaultValue="2" />
                                                    <i className="material-icons">
                                                    </i>  {t('BILLING_ORDERS_CANCEL_ORDER_CREATE_CREDIT_NOTE')}</label> <br/>
                                                <p style={{paddingLeft: "20px"}}>This action will cancel the order and create a credit note but the system will NOT send any email to the attendee or send any EAN credit note.</p>
                                                <br />
                                                <label  style={{fontSize: "20px"}}>
                                                    <input type="radio" onChange={this.handleToggleNote.bind(this)} name="cancel_order" defaultValue="3"/>
                                                    <i className="material-icons">
                                                    </i> {t('BILLING_ORDERS_CANCEL_ORDER_WITHOUT_CREATE_CREDIT_NOTE')} </label> <br/>
                                                <p style={{paddingLeft: "20px"}}>This action will cancel the order but it will NOT create any credit note. <br/>This is typically used when the order is cancelled after the official cancellation end date and when the invoiced amount is non-refundable, due to the company terms and conditions.<br/>The invoice will get this status: “Cancelled without credit note”</p>
                                            </form>
                                        </div>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                                onClick={() => {
                                                    onClose();
                                                    service
                                                        .post(
                                                            `${process.env.REACT_APP_URL}/eventsite/billing/orders/cancel/${id}`,
                                                            { option: this.state.cancel_order_note }
                                                        )
                                                        .then(
                                                            response => {
                                                                if (response.success) {
                                                                    this.listing(1);
                                                                } else {
                                                                    this.setState({
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
                                            {t('G_SUBMIT')}
                                        </button>
                                    </div>
                                </div>
                        }
                    </Translation>
                );
            }
        });
    }

    removeOrderFree = (id) => e => {

        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {
                            t =>
                                <div className='app-main-popup'>
                                    <div className="app-header">
                                        <h4>{t('BILLING_ORDERS_CANCEL_ORDER')}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{t('BILLING_ORDERS_CANCEL_ORDER_CONFIRMATION')}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>{t('G_CANCEL')}</button>
                                        <button className="btn btn-success"
                                                onClick={() => {
                                                    onClose();
                                                    service
                                                        .post(
                                                            `${process.env.REACT_APP_URL}/eventsite/billing/orders/cancel/${id}`,
                                                            { option: 2 }
                                                        )
                                                        .then(
                                                            response => {
                                                                if (response.success) {
                                                                    this.listing(1);
                                                                } else {
                                                                    this.setState({
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
                                            {t('G_SUBMIT')}
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

    export = (type) => {
        this.setState({ downloadLoader: true });
        service.downloadWithPost(`${process.env.REACT_APP_URL}/eventsite/billing/orders/${type === "order-list" ? 'export' : 'export-detail'}`, this.state)
            .then(response => {
                response.blob().then(blob => {
                    if (window.navigator && window.navigator.msSaveOrOpenBlob) { // for IE
                        var csvData = new Blob([blob], { type: 'text/csv' });
                        window.navigator.msSaveOrOpenBlob(csvData, "export.csv");
                    } else {
                        let url = window.URL.createObjectURL(blob);
                        let a = document.createElement('a');
                        a.href = url;
                        a.download = 'Orders-' + moment().valueOf() + '.xlsx';
                        a.click();
                    }
                    this.setState({ downloadLoader: false });
                });
            });
    }

    handleChange = (input) => e => {
        this.setState({
            [input]: e.value
        });
    }

    render() {
        /* const selected_rows_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size; */

        const order_status_filters = [
            {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_ALL'),
                id: 'all'
            }, {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_COMPLETE'),
                id: 'completed'
            }, {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_CANCELLED'),
                id: 'cancelled'
            }, {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_CANCELLED_WITHOUT_CREDIT_NOTE'),
                id: 'cancelled_without_creditnote'
            }, {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_PENDING'),
                id: 'pending'
            }
        ];

        const payment_status_filters = [
            {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_ALL'),
                id: 'all'
            }, {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_RECIEVED'),
                id: 'payment_received'
            }, {
                name: this.props.t('BILLING_ORDERS_ORDER_FILTER_PENDING'),
                id: 'payment_pending'
            }
        ];

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
                        {this.state.preLoader && <Loader />}
                        {this.state.downloadLoader &&
                            <Loader fixed="true" />
                        }
                        {!this.state.preLoader && (
                            <React.Fragment>
                                <div style={{ height: "100%" }}>
                                    {this.state.displayPanel && (
                                        <div style={{ margin: "0" }} className="new-header clearfix">
                                            {!this.props.largeScreen && (
                                                <div className="row">
                                                    <div className="col-6">
                                                        <h1 className="section-title">{t("BILLING_ORDERS_MAIN_HEADING")}</h1>
                                                        {/* <p>
                                                            {t("BILLING_ORDERS_SUB_HEADING")}
                                                        </p> */}
                                                    </div>
                                                    {Number(this.state.permissions["add"]) === 1 && (
                                                        <div className="col-6">
                                                            <div className="right-panel-billingitem float-right">
                                                                <button
                                                                    className={`${this.state.stateToggle && "active"} btn_addNew_main`}
                                                                    onClick={this.handleToggle.bind(this)}
                                                                    ref={this.myToggleRef}
                                                                >
                                                                    <span className="icons">
                                                                        <Img src={require("img/dots.svg")} />
                                                                    </span>
                                                                </button>
                                                                <div className="drop_down_panel">
                                                                    <button
                                                                        className="btn_addNew"
                                                                        onClick={() => this.export('order-list')}
                                                                    >
                                                                        {t("G_EXPORT")}
                                                                    </button>
                                                                    <button
                                                                        className="btn_addNew"
                                                                        onClick={() => this.export('order-list-detail')}
                                                                    >
                                                                        {t("G_EXPORT_SINGLE")}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    )}
                                                </div>
                                            )}
                                            <div style={{ marginBottom: '10px' }} className="d-flex row align-items-center order-section-filter">
                                                <div className="col-4">
                                                    {(this.state.orders.length > 0 && !this.state.query) || this.state.query || this.state.type || this.state.payment_status ? (
                                                        <input value={this.state.query} name="query" type="text"
                                                               placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                                        />
                                                    ) : ''}
                                                </div>
                                                <div className="col-3">
                                                    <DropDown
                                                        label={t('BILLING_ORDERS_ORDER_FILTER_ORDER_STATUS')}
                                                        listitems={order_status_filters}
                                                        required={false}
                                                        selected={this.state.type}
                                                        isSearchable='false'
                                                        selectedlabel={this.getSelectedLabel(order_status_filters, this.state.type)}
                                                        onChange={this.handleChange('type')}
                                                    />
                                                </div>
                                                <div className="col-3">
                                                    <DropDown
                                                        label={t('BILLING_ORDERS_ORDER_FILTER_PAYMENT_STATUS')}
                                                        listitems={payment_status_filters}
                                                        required={false}
                                                        selected={this.state.payment_status}
                                                        isSearchable='false'
                                                        selectedlabel={this.getSelectedLabel(payment_status_filters, this.state.payment_status)}
                                                        onChange={this.handleChange('payment_status')}
                                                    />
                                                </div>
                                                {this.state.orders.length > 0 && (
                                                    <div className="col-2">
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
                                                                            style={{ ...flexGrow, minWidth: '54px' }}
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
                                    {this.state.displayPanel && this.state.orders.length > 0 && (
                                        <div className="wrapper-billing-section voucher-select-items voucher-elements-main">
                                            <div style={{ borderTop: '0px' }} className="row d-flex header-billing">
                                                {/* <div style={{ minWidth: '55px', width: '55px' }} className="grid-1">
                                                    <label className="checkbox-items">
                                                        <input
                                                            id="selectall"
                                                            checked={(selected_rows_length === this.state.orders.length ? true : false)}
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
                                                                        onClick={this.removeOrder("selected")
                                                                        }
                                                                    >
                                                                        {t("G_DELETE_SELECTED")}
                                                                    </button>
                                                                )}
                                                                <button
                                                                    className="dropdown-item"
                                                                    onClick={this.removeOrder("all")
                                                                    }
                                                                >
                                                                    {t("G_DELETE_ALL")}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> */}
                                                <div style={{ ...flexGrow, minWidth: '81px', width: '81px' }} className="grid-1">
                                                    <strong>{t("BILLING_ORDERS_ORDER_NUMBER")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_number" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "order_number" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "order_number" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div style={{ ...flexGrow, minWidth: '85px', width: '85px' }} className="grid-3">
                                                    <strong>{t("BILLING_ORDERS_ORDER_DATE")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_date" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "order_date" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "order_date" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-4">
                                                    <strong>{t("BILLING_ORDERS_ORDER_NAME")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div style={{ ...flexGrow, minWidth: '170px', width: '170px' }} className="grid-5">
                                                    <strong>{t("BILLING_ORDERS_ORDER_EMAIL")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div style={{ ...flexGrow, minWidth: '110px', width: '110px' }} className="grid-6">
                                                    <strong>{t("BILLING_ORDERS_ORDER_COMPANY")}</strong>
                                                </div>
                                                <div style={{ ...flexGrow, minWidth: '90px', width: '90px' }} className="grid-7">
                                                    <strong>{t("BILLING_ORDERS_ORDER_AMOUNT")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="grand_total" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "grand_total" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "grand_total" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-7">
                                                    <strong>{t("BILLING_ORDERS_ORDER_ORDER_STATUS")}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="status" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "status" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "status" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="grid-7">
                                                    <strong>{t("BILLING_ORDERS_ORDER_PAYMENT_STATUS")}</strong>
                                                </div>
                                                <div className="grid-8">

                                                </div>
                                            </div>
                                            {this.state.orders.map((order, index) => (
                                                <React.Fragment key={index}>
                                                    <div className={`${this.state.checkedItems.get(order.id.toString()) && 'selected'} row check-box-list`}>
                                                        <div style={{ ...flexGrow, minWidth: '81px', width: '81px' }} className="grid-2">
                                                            <p>
                                                                {
                                                                    (() => {
                                                                        if (this.props.event.eventsite_payment_setting.eventsite_invoice_prefix)
                                                                            return this.props.event.eventsite_payment_setting.eventsite_invoice_prefix + '-' + (order.order_number ? order.order_number : order.id);
                                                                        else
                                                                            return (order.order_number ? order.order_number : order.id);
                                                                    })()
                                                                }
                                                            </p>
                                                        </div>
                                                        <div style={{ ...flexGrow, minWidth: '85px', width: '85px' }} className="grid-3">
                                                            <p>{moment(order.order_date).format('DD/MM/YYYY')}</p>
                                                        </div>
                                                        <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-4">
                                                            <p title={order.first_name + ' ' + order.last_name} style={pStyles}>{order.first_name + ' ' + order.last_name}</p>
                                                        </div>
                                                        <div style={{ ...flexGrow, minWidth: '170px', width: '170px' }} className="grid-5">
                                                            <p title={order.email} style={pStyles}>{order.email}</p>


                                                        </div>
                                                        <div style={{ ...flexGrow, minWidth: '110px', width: '110px' }} className="grid-6">
                                                            <p title={order.company_name} style={pStyles}>{order.company_name}</p>
                                                        </div>
                                                        <div style={{ ...flexGrow, minWidth: '120px', width: '120px' }} className="grid-7">
                                                            <p>{order.grand_total}</p>
                                                        </div>
                                                        <div style={{ ...flexGrow, minWidth: '90px', width: '90px' }} className="grid-7">
                                                            <p>{this.getSelectedLabel(order_status_filters, order.status) || order.status}</p>
                                                        </div>
                                                        <div className="grid-7">
                                                            <p>
                                                                {
                                                                    (() => {
                                                                        if (Number(order.is_payment_received) === 0)
                                                                            return t("BILLING_ORDERS_ORDER_PAYMENT_STATUS_PENDING");
                                                                        else
                                                                            return <p dangerouslySetInnerHTML={{ __html: t("BILLING_ORDERS_ORDER_FILTER_RECIEVED") + '<br>' + moment(order.payment_received_date).format('DD/MM/YYYY') }}></p>;
                                                                    })()
                                                                }</p>
                                                        </div>
                                                        <div className="grid-8">
                                                            <div className="parctical-button-panel button-panel-list">
                                                                <div className="dropdown">
                                                                                                                        <span onClick={this.handleDropdown.bind(this)} className="btn btn_dots">
                                                                                                                                <ReactSVG style={{pointerEvents: 'none'}} wrapper="span" className='icons' alt="" src={require("img/ico-dots-gray.svg")} />
                                                                                                                        </span>
                                                                    <div className="dropdown-menu">
                                                                        {
                                                                            (() => {
                                                                                if (order.status === 'cancelled')
                                                                                    return
                                                                                else if(this.props.event && this.props.event.eventsite_setting && Number(this.props.event.eventsite_setting.payment_type) === 0)
                                                                                    return (
                                                                                        <button className="dropdown-item" onClick={this.removeOrderFree(order.id)}>
                                                                                            {t('G_CANCEL')}
                                                                                        </button>
                                                                                    )
                                                                                else
                                                                                    return (
                                                                                        <button className="dropdown-item" onClick={this.removeOrder(order.id)}>
                                                                                            {t('G_CANCEL')}
                                                                                        </button>
                                                                                    )
                                                                            })()
                                                                        }
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {order.order_attendees.length>1 &&
                                                        <OrderAttendees order_attendees={order.order_attendees} attendee_id={order.attendee_id} />

                                                    }
                                                </React.Fragment>
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

export default connect(mapStateToProps)(withTranslation()(Listing));