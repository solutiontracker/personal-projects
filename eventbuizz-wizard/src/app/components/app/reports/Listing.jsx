import React, { Component } from 'react';
import Img from "react-image";
import { connect } from 'react-redux';
import { Translation, withTranslation } from "react-i18next";
import AlertMessage from '@/app/forms/alerts/AlertMessage';
import Loader from '@/app/forms/Loader';
import { service } from "services/service";
import Pagination from "react-js-pagination";
import DropDown from '@/app/forms/DropDown';
import { ReactSVG } from "react-svg";
import DateTime from "@/app/forms/DateTime";
import moment from 'moment';

const in_array = require("in_array");

class ReportWidget extends Component {
    _isMounted = false;

    constructor(props) {

        super(props);
        this.state = {
            query: "",
            sort_by: 'first_name',
            order_by: 'ASC',
            status: this.props.status,
            fromDate: "",
            toDate: "",
            type: this.props.type,
            response: [],

            //pagination
            limit: 10,
            total: null,
            from: 0,
            to: 0,
            activePage: 1,

            //errors & loading
            preLoader: true,

            typing: false,
            typingTimeout: 0,

            message: false,
            success: true,

            reports: [],
            report: this.props.report,
            _url: this.props._url,
            _export_url: this.props._export_url,
            isFilter: false
        };

        this.onSorting = this.onSorting.bind(this);
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
        if (order_by !== prevState.order_by || sort_by !== prevState.sort_by || prevState.limit !== this.state.limit || prevState._url !== this.state._url || prevState.type !== this.state.type) {
            this.listing(1);
        } else if (this.state.report !== prevState.report) {
            if (this.state.report === "registration-sign-up-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/listing`,
                    _export_url: `${process.env.REACT_APP_URL}/attendee/export`,
                    type: `registration-sign-ups`
                });
            } else if (this.state.report === "sign-up-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/listing`,
                    _export_url: `${process.env.REACT_APP_URL}/attendee/export`,
                    type: `sign-ups`
                });
            } else if (this.state.report === "invitation-sent-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/invitations`,
                    _export_url: '',
                    status: 1
                });
            } else if (this.state.report === "invite-reminder-send-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/registration/invitations/reminder-log`,
                    _export_url: '',
                });
            } else if (this.state.report === "invited-but-not-registered-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/not-registered`,
                    _export_url: '',
                });
            } else if (this.state.report === "invited-but-not-attending-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/not-attendees-list`,
                    _export_url: '',
                });
            } else if (this.state.report === "app-invite-send-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/app-invitations`,
                    _export_url: '',
                });
            } else if (this.state.report === "app-invite-not-send-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/app-invitations-not-sent`,
                    _export_url: '',
                });
            } else if (this.state.report === "app-invite-reminder-send-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/attendee/app/invitations/reminder-log`,
                    _export_url: '',
                });
            } else if (this.state.report === "cancelled-registration-list") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/billing/orders`,
                    type: "cancelled",
                    _export_url: '',
                });
            } else if (this.state.report === "hotel-bookings") {
                this.setState({
                    _url: `${process.env.REACT_APP_URL}/hotel/bookings`,
                    _export_url: `${process.env.REACT_APP_URL}/hotel/export`,
                });
            }
        }
    }

    handlePageChange = activePage => {
        this.listing(activePage);
    };

    listing = (activePage = 1, loader = false) => {
        this.setState({ preLoader: !loader ? true : false });
        service
            .post(`${this.state._url}/${activePage}`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                response: response.data.data,
                                activePage: response.data.current_page,
                                total: response.data.total,
                                from: response.data.from,
                                to: response.data.to,
                                preLoader: false
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

    onSorting(event) {
        this.setState({
            order_by: event.target.attributes.getNamedItem('data-order').value,
            sort_by: event.target.attributes.getNamedItem('data-sort').value,
        });
    }

    handleChange = (input) => e => {
        this.setState({
            [input]: e.value
        });
    }

    handleDateChange = (input) => e => {
        if (e !== undefined && e !== 'Invalid date' && e !== 'cleardate') {
            var date = moment(new Date(e)).format('YYYY-MM-DD');
            this.setState({
                [input]: date,
            });
        }
    }

    applyFilter = (e) => {
        this.listing(1);
    }

    resetFilter = (e) => {
        this.setState({
            fromDate: "",
            toDate: ""
        }, () => {
            this.listing(1);
        });
    }

    cancelFilter = (e) => {
        this.setState({
            isFilter: false
        });
    }

    export = () => {
        this.setState({ preLoader: true });
        service.download(`${this.state._export_url}?type=${this.state.type}`)
            .then(response => {
                response.blob().then(blob => {
                    this.setState({ preLoader: false });
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

        const reports = [
            {
                name: this.props.t('RPT_REGISTRATION_SIGNUP_LIST'),
                id: 'registration-sign-up-list'
            }, {
                name: this.props.t('RPT_SIGNUP_LIST'),
                id: 'sign-up-list'
            }, {
                name: this.props.t('RPT_INVITATION_SEND_LIST'),
                id: 'invitation-sent-list'
            }, {
                name: this.props.t('RPT_INVITE_REMINDER_SEND_LIST'),
                id: 'invite-reminder-send-list'
            }, {
                name: this.props.t('RPT_INVITED_BUT_NOT_REGISTERED_LIST'),
                id: 'invited-but-not-registered-list'
            }, {
                name: this.props.t('RPT_INVITED_BUT_NOT_ATTENDING_LIST'),
                id: 'invited-but-not-attending-list'
            }, {
                name: this.props.t('RPT_CANCELLED_REGISTRATION_LIST'),
                id: 'cancelled-registration-list'
            }, {
                name: this.props.t('RPT_APP_INVITE_SEND_LIST'),
                id: 'app-invite-send-list'
            }, {
                name: this.props.t('RPT_APP_INVITE_NOT_SEND_LIST'),
                id: 'app-invite-not-send-list'
            }, {
                name: this.props.t('RPT_APP_INVITE_REMINDER_SEND_LIST'),
                id: 'app-invite-reminder-send-list'
            }, {
                name: this.props.t('RPT_BOOKINGS_FOR_ACCOMODATION'),
                id: 'hotel-bookings'
            }
        ];

        this.getSelectedLabel = (item, id) => {
            if (item && item.length > 0 && id) {
                let obj = item.find(o => o.id.toString() === id.toString());
                return (obj ? obj.name : '');
            }
        }

        const Records = ({ data }) => {
            return (
                <React.Fragment>
                    <Translation>
                        {
                            t =>
                                <header className="header-records row d-flex">
                                    {
                                        (() => {
                                            if (in_array(this.state.report, ["registration-sign-up-list", "sign-up-list"])) {
                                                return (
                                                    <React.Fragment>
                                                        <div className="col-md-1">
                                                            <strong>{t('ATTENDEE_ID')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="id" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "id" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "id" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-3">
                                                            <strong>{t('ATTENDEE_REGISTRATION_DATE')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="created_at" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "created_at" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "created_at" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-3">
                                                            <strong>{t('ATTENDEE_FULL_NAME')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-3 col-flex-12">
                                                            <strong>{t('ATTENDEE_EMAIL')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('ATTENDEE_COMPANY')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="company_name" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "company_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "company_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                    </React.Fragment>
                                                )
                                            } else if (in_array(this.state.report, ["hotel-bookings"])) {
                                                return (
                                                    <React.Fragment>
                                                        <div className="col-md-1">
                                                            <strong>{t('RPT_HOTEL_ORDER_ID')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="order_id" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "order_id" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "order_id" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_ITEM')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="name" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_ROOM_TYPE')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="price_type" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "price_type" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "price_type" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_ROOMS')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="rooms" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "rooms" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "rooms" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_CHECKIN')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="checkin" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "checkin" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "checkin" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_CHECKOUT')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="checkout" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "checkout" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "checkout" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_NIGHTS')}</strong>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_CONTACT_NAME')}</strong>
                                                        </div>
                                                        <div className="col-md-2">
                                                            <strong>{t('RPT_HOTEL_CONTACT_EMAIL')}</strong>
                                                        </div>
                                                    </React.Fragment>
                                                )
                                            } else {
                                                return (
                                                    <React.Fragment>
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
                                                        <div className="col-3 col-flex-12">
                                                            <strong>{t('ATTENDEE_EMAIL')}</strong>
                                                            <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                                {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                            </i>
                                                        </div>
                                                        <div className="col-3">
                                                            <strong>{t('ATTENDEE_PHONE')}</strong>
                                                        </div>
                                                    </React.Fragment>
                                                )
                                            }
                                        })()
                                    }
                                </header>
                        }
                    </Translation>
                    {
                        data.map((data, key) => {
                            return (
                                <Translation key={key}>
                                    {
                                        t =>
                                            <div className={`${this.state.editElement && this.state.editElement && this.state.editElementIndex === key ? "no-hover row d-flex align-items-center" : "row d-flex align-items-center"}`} key={key}>
                                                {
                                                    (() => {
                                                        if (in_array(this.state.report, ["registration-sign-up-list", "sign-up-list"])) {
                                                            return (
                                                                <React.Fragment>
                                                                    <div className="col-md-1">
                                                                        {data.order_id && <p>{data.order_id}</p>}
                                                                    </div>
                                                                    <div className="col-md-3">
                                                                        {data.created_at && <p>{data.created_at}</p>}
                                                                    </div>
                                                                    <div className="col-md-3">
                                                                        {data.first_name && <p>{data.first_name + ' ' + data.last_name}</p>}
                                                                    </div>
                                                                    <div className="col-md-3">
                                                                        {data.email && <p>{data.email}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.attendee_detail && <p>{data.attendee_detail.company_name}</p>}
                                                                    </div>
                                                                </React.Fragment>
                                                            )
                                                        } else if (in_array(this.state.report, ["hotel-bookings"])) {
                                                            return (
                                                                <React.Fragment>
                                                                    <div className="col-md-1">
                                                                        {data.order_number && <p>{data.order_number}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.name && <p>{data.name}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.price_type && <p>{data.price_type}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.rooms && <p>{data.rooms}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.checkin && <p>{data.checkin}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.checkout && <p>{data.checkout}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.nights && <p>{data.nights}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.first_name && <p>{data.first_name + ' ' + data.last_name}</p>}
                                                                    </div>
                                                                    <div className="col-md-2">
                                                                        {data.email && <p>{data.email}</p>}
                                                                    </div>
                                                                </React.Fragment>
                                                            )
                                                        } else {
                                                            return (
                                                                <React.Fragment >
                                                                    <div className="col-3">
                                                                        {data.first_name && <p>{data.first_name}</p>}
                                                                    </div>
                                                                    <div className="col-3">
                                                                        {data.last_name && <p>{data.last_name}</p>}
                                                                    </div>
                                                                    <div className="col-3">
                                                                        {data.email && <p>{data.email}</p>}
                                                                    </div>
                                                                    <div className="col-3">
                                                                        {data.phone && <p>{data.phone}</p>}
                                                                    </div>
                                                                </React.Fragment>
                                                            )
                                                        }
                                                    })()
                                                }
                                            </div>
                                    }
                                </Translation>
                            )
                        })
                    }
                </React.Fragment >
            )
        }

        return (
            <Translation>
                {t => (
                    <div>
                        <div style={{ height: "100%" }}>
                            {this.state.isFilter && (
                                <div className="wrapper-popup">
                                    <div className="wrapper-sidebar">
                                        <header>
                                            <h3>{t('G_FILTER_BY')}</h3>
                                        </header>
                                        <div className="bottom-content">
                                            <div className="package-wrapper">
                                                <h4>
                                                    {t('G_Date')} <i className="icons"><img src={require('img/chevron.svg')} alt="" /></i>
                                                </h4>
                                                <div className="row">
                                                    <div className="col-6">
                                                        <h6>{t('G_FROM')}</h6>
                                                        <DateTime
                                                            label={t('G_FROM')}
                                                            value={this.state.fromDate}
                                                            required={true}
                                                            onChange={this.handleDateChange('fromDate')}
                                                        />
                                                    </div>
                                                    <div className="col-6">
                                                        <h6>{t('G_TO')}</h6>
                                                        <DateTime label={t('G_TO')} value={this.state.toDate} required={true} onChange={this.handleDateChange('toDate')} fromDate={(this.state.fromDate ? new Date(this.state.fromDate) : new Date())} />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="bottom-component-panel clearfix">
                                            <span className="btn_close float-left" onClick={this.resetFilter}>
                                                <i className="material-icons">clear</i> {t('G_RESET_ALL')}
                                            </span>
                                            <button data-type="save" className="btn btn btn-save" onClick={this.cancelFilter}>
                                                {t('G_CANCEL')}
                                            </button>
                                            <button data-type="save-next" className="btn btn-save-next" onClick={this.applyFilter} disabled={this.state.fromDate && this.state.toDate ? false : true}>
                                                {t('G_APPLY')}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
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
                                                {this.props.largeScreen ? (
                                                    <h1 className="section-title">{this.getSelectedLabel(reports, this.state.report)}</h1>
                                                ) : (
                                                        <h1 className="section-title">{t('RPT_REPORTS')}</h1>
                                                    )}
                                                {!this.props.largeScreen && (
                                                    <h4 className="component-heading">{t('RPT_SELECT_REPORT_LABEL')}</h4>
                                                )}
                                            </div>
                                            {!this.props.largeScreen && (
                                                <div style={{maxWidth: '510px'}} className="col-6">
                                                    <DropDown
                                                        label={t('RPT_SELECT_REPORT_TYPE')}
                                                        listitems={reports}
                                                        required={false}
                                                        selected={this.state.report}
                                                        isSearchable='false'
                                                        selectedlabel={this.getSelectedLabel(reports, this.state.report)}
                                                        onChange={this.handleChange('report')}
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    </header>
                                    <div className="attendee-management-section attendee-form-modifications">
                                        <div style={{ marginTop: '0px', marginBottom: 0 }} className="new-header">
                                            <div style={{ marginTop: '0px', marginBottom: 0 }} className="row d-flex align-items-center">
                                                <div className="col-6">
                                                    <input style={{borderRadius: '5px'}} value={this.state.query} name="query" type="text"
                                                        placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                                    />
                                                </div>
                                                <div className="col-6">
                                                    <div className="panel-right-table d-flex justify-content-end">
                                                        {this.state._export_url && (
                                                            <button style={{ marginLeft: '3px' }} className="btn btn-fullscreen" onClick={() => this.export()}>
                                                                <span className="icons">
                                                                    <Img  src={require("img/export-csv.svg")} />
                                                                </span>
                                                            </button>
                                                        )}
                                                        {!this.props.largeScreen && (
                                                            <button onClick={() => this.setState({ isFilter: true })} className="btn btn-fullscreen">
                                                                <ReactSVG wrapper="span" className="icons" src={require('img/filter-arrow.svg')} />
                                                            </button>
                                                        )}
                                                        {this.state.response.length > 0 && (
                                                            <React.Fragment>
                                                                {!this.props.largeScreen && (
                                                                    <button onClick={this.props.handleLargeScreen(this.state._url, this.state.status, this.state.type, this.state.report, this.state._export_url)} className="btn btn-fullscreen">
                                                                        <img src={require('img/fullscreen.svg')} alt="" />
                                                                    </button>
                                                                )}
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
                                                            </React.Fragment>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="hotel-management-records attendee-records-template">
                                            {this.state.response.length > 0 ? (
                                                <Records data={this.state.response} />
                                            ) : (
                                                    <div className="no-record-found">{t('G_NOT_FOUND_RECORD')}</div>
                                                )}
                                        </div>
                                        <div style={{ marginBottom: '20px' }} className="row">
                                            <div className="col-6">
                                                {this.state.response.length > 0 && (
                                                    <span className="total-counter">
                                                        {`${this.state.from} - ${this.state.to} ${t('G_OF')} ${this.state.total}`}
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
                                </React.Fragment>
                            )}
                        </div>
                        {this.props.largeScreen && (
                            <div className="bottom-component-panel clearfix">
                                <div className="bottom-component-panel clearfix">
                                    <button className="btn btn-cancel" onClick={this.props.handleLargeScreen(this.state._url, this.state.status, this.state.type, this.state.report, this.state._export_url)}>{t('G_CANCEL')}</button>
                                    <button className="btn btn-save-next" onClick={this.props.handleLargeScreen(this.state._url, this.state.status, this.state.type, this.state.report, this.state._export_url)}>{t('G_SAVE_CLOSE')}
                                    </button>
                                </div>
                            </div>
                        )}
                    </div>
                )}
            </Translation >
        );

    }
}

function mapStateToProps(state) {
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(withTranslation()(ReportWidget));