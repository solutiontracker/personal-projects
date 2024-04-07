import React, { Component } from "react";
import { NavLink } from 'react-router-dom';
import { service } from "services/service";
import Pagination from "react-js-pagination";
import Loader from "@/app/forms/Loader";
import "react-confirm-alert/src/react-confirm-alert.css"; // Import css
import { Translation } from "react-i18next";
import AlertMessage from "@/app/forms/alerts/AlertMessage";

class AppInvitationReminderLog extends Component {
    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            query: "",
            attendees: [],

            //pagination
            limit: 10,
            total: null,
            activePage: 1,

            //errors & loading
            preLoader: true,

            typing: false,
            typingTimeout: 0,

            message: false,
            success: true,
            isChecked: false,

            checkedItems: new Map()
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    handlePageChange = activePage => {
        this.listing(activePage);
    };

    listing = (activePage = 1, loader = false) => {
        this.setState({ preLoader: !loader ? true : false });
        service
            .post(`${process.env.REACT_APP_URL}/attendee/app/invitations/reminder-log/${activePage}`, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                attendees: response.data.data,
                                activePage: response.data.current_page,
                                total: response.data.total,
                                preLoader: false,
                                checkedItems: new Map()
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

    render() {
        const Records = ({ data }) => {
            return (
                <React.Fragment>
                    <Translation>
                        {
                            t =>
                                <header className="header-records row d-flex">
                                    <div className="col-3">
                                        <strong>{t("ATTENDEE_FULL_NAME")}</strong>
                                    </div>
                                    <div className="col-3">
                                        <strong>{t("ATTENDEE_EMAIL")}</strong>
                                    </div>
                                    <div className="col-3">
                                        <strong>{t("ATTENDEE_PHONE")}</strong>
                                    </div>
                                    <div className="col-3">
                                        <strong>{t("ATTENDEE_DATE")}</strong>
                                    </div>
                                </header>
                        }
                    </Translation>
                    {data.map((data, key) => {
                        return (
                            <Translation key={key}>
                                {
                                    t =>
                                        <div className={this.state.editElement && this.state.editElement && this.state.editElementIndex === key ? "no-hover row d-flex" : "row d-flex"} key={key}>
                                            <div className="col-3">
                                                {data.first_name + " " + data.last_name}
                                            </div>
                                            <div className="col-3">
                                                {data.email && <p>Email: {data.email}</p>}
                                            </div>
                                            <div className="col-3">
                                                {data.phone && <p> {data.phone}</p>}
                                            </div>
                                            <div className="col-3">
                                                {data.email_date && <p>{data.email_date}</p>}
                                            </div>
                                        </div>
                                }
                            </Translation>
                        )
                    })}
                </React.Fragment>
            )
        }

        return (
            <Translation>
                {t => (
                    <div>
                        <div className="wrapper-content third-step">
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
                                            icon={this.state.success ? "check" : "info"}
                                        />
                                    )}
                                    <header className="new-header clearfix">
                                        <h1 className="section-title">
                                            {t("ATTENDEE_REMINDER_LOG")}
                                        </h1>
                                        <div className="d-flex">

                                        <input
                                            value={this.state.query}
                                            name="query"
                                            type="text"
                                            placeholder={t("G_SEARCH")}
                                            onChange={this.onFieldChange.bind(this)}
                                        />
                                        </div>
                                    </header>

                                    <div
                                        style={{ paddingTop: "15px" }}
                                        className="attendee-management-section"
                                    >
                                        {this.state.attendees.length > 0 ? (
                                            <div className="hotel-management-records attendee-records-template">
                                                <Records data={this.state.attendees} />
                                            </div>
                                        ) : (
                                                ""
                                            )}
                                        {this.state.total > this.state.limit ? (
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
                                        ) : (
                                                ""
                                            )}
                                    </div>
                                    <div className="bottom-component-panel clearfix">
                                        <NavLink target="_blank" className="btn btn-preview float-left" to={`/event/preview`}>
                                            <i className='material-icons'>remove_red_eye</i>
                                          {t('G_PREVIEW')}
                                        </NavLink>
                                        <NavLink className="btn btn-prev-step" to={`/event/invitation/report/app-invitation-not-sent`}><span className="material-icons">
                                            keyboard_backspace</span></NavLink>
                                        <NavLink className="btn btn-next-step" to={`/event/preview`}>{t('G_NEXT')}</NavLink>
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

export default AppInvitationReminderLog;
