import React, { Component } from 'react';
import { Translation } from "react-i18next";
import Loader from '@/app/forms/Loader';
import { connect } from 'react-redux';
import DropDown from '@/app/forms/DropDown';
import { AttendeeService } from 'services/attendee/attendee-service';
import Pagination from "react-js-pagination";

class Signup extends Component {

    constructor(props) {
        super(props);
        this.state = {
            query: '',
            action: '',
            created_at: this.props.created_at,
            sort_by: 'first_name',
            order_by: 'ASC',
            attendees: [],
            registered_attendees: '',

            //pagination
            limit: 25,
            total: '',
            activePage: 1,

            //errors & loading
            preLoader: true,

            typing: false,
            typingTimeout: 0,
        }

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
        const { order_by, sort_by, action } = this.state;
        if (action !== prevState.action || order_by !== prevState.order_by || sort_by !== prevState.sort_by) {
            this.listing(1);
        }
    }

    handlePageChange = (activePage) => {
        this.listing(activePage);
    }

    listing = (activePage = 1, loader = false) => {
        this.setState({ preLoader: (!loader ? true : false) });
        AttendeeService.listing(activePage, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                attendees: response.data.data,
                                registered_attendees: response.registered_attendees,
                                activePage: response.data.current_page,
                                total: response.data.total,
                                preLoader: false
                            });
                        }
                    }
                },
                error => { }
            );
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
                self.listing(1)
            }, 1000)
        });
    }

    handleChange = (input) => e => {
        this.setState({
            [input]: e.value
        })
    }

    onSorting(event) {
        this.setState({
            order_by: event.target.attributes.getNamedItem('data-order').value,
            sort_by: event.target.attributes.getNamedItem('data-sort').value,
        });
    }

    render() {
        return (
            <Translation>
                {t => (
                    <div className="data-popup-charts wrapper-import-file-wrapper">
                        <div className="wrapper-import-file main-landing-page">
                            <div className="top-landing-page">
                                <div className="row d-flex">
                                    <div className="col-5 d-flex align-items-center">
                                        <div className="heading-area-popup">
                                            <h2>{t('DSB_SIGNUPS')}</h2>
                                            <p>{this.props.event.name}</p>
                                        </div>
                                    </div>
                                    <div className="col-7">
                                        <div className="right-top-header">
                                            <input className="search-field" value={this.state.query} name="query" type="text"
                                                placeholder={t('G_SEARCH')} onChange={this.onFieldChange.bind(this)}
                                            />
                                            <label className="label-select-alt">
                                                <DropDown
                                                    label={t('EL_FILTER_BY')}
                                                    listitems={[
                                                        { id: "completed", name: t('DSB_COMPLETED') },
                                                        { id: "cancelled", name: t('DSB_CANCELLED') }
                                                    ]}
                                                    onChange={this.handleChange('action')}
                                                    required={true}
                                                />
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <span
                                    onClick={this.props.close(false)}
                                    className="btn-close"
                                >
                                    <i className="material-icons">close</i>
                                </span>
                            </div>
                            {this.state.preLoader &&
                                <Loader />
                            }
                            {!this.state.preLoader && (
                                <React.Fragment>
                                    <div className="wrapper-module">
                                        <div className="top-section-wrapper row d-flex">
                                            <div className="col-6">
                                                <div className="pnp-breadcrumb">
                                                    <ol className="breadcrumb">
                                                        <li onClick={this.props.close(false)} className="breadcrumb-item"><a href="#!">{t('DSB_HOME')}</a></li>
                                                    </ol>
                                                </div>
                                            </div>
                                            <div className="col-6">
                                                <p className="text-right">
                                                    {t('DSB_SIGNUPS')} <a href="#!">{this.state.registered_attendees}</a>
                                                </p>
                                            </div>
                                        </div>
                                        <div className="hotel-management-records attendee-records-template">
                                            <header className="header-records row d-flex">
                                                <div className="col-2">
                                                    <strong>{t('DSB_ORDER_NUMBER')}</strong>
                                                </div>
                                                <div className="col-2">
                                                    <strong>{t('DSB_DATE_TIME')}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="created_at" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "created_at" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "created_at" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="col-2">
                                                    <strong>{t('ATTENDEE_COMPANY_NAME')}</strong>
                                                </div>
                                                <div className="col-2">
                                                    <strong>{t('ATTENDEE_FIRST_NAME')}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="first_name" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "first_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "first_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="col-2">
                                                    <strong>{t('ATTENDEE_LAST_NAME')}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="last_name" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "last_name" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "last_name" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="col-2">
                                                    <strong>{t('ATTENDEE_EMAIL')}</strong>
                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                    </i>
                                                </div>
                                                <div className="col-2">
                                                    <strong>{t('DSB_STATUS')}</strong>
                                                </div>
                                            </header>
                                            {this.state.attendees && this.state.attendees.map((row, k) => {
                                                return (
                                                    <div className="row d-flex" key={row.id}>
                                                        <div className="col-2">
                                                            <p><strong>{row.order_id}</strong></p>
                                                        </div>
                                                        <div className="col-2">
                                                            <p><strong>{row.created_at}</strong></p>
                                                        </div>
                                                        <div className="col-2">
                                                            <p><strong>{row.attendee_detail.company_name}</strong></p>
                                                        </div>
                                                        <div className="col-2">
                                                            <p>{row.first_name}</p>
                                                        </div>
                                                        <div className="col-2">
                                                            <p>{row.last_name}</p>
                                                        </div>
                                                        <div className="col-2">
                                                            <p>{row.email}</p>
                                                        </div>
                                                        <div className="col-2">
                                                            <p>{row.order_status}</p>
                                                        </div>
                                                    </div>
                                                )
                                            })
                                            }
                                            {this.state.total > this.state.limit ? (
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
                                            ) : ''}
                                        </div>
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
    const { event } = state;
    return {
        event
    };
}

export default connect(mapStateToProps)(Signup);