import React, { Component } from "react";
import { NavLink } from 'react-router-dom';
import Img from "react-image";
import { service } from "services/service";
import Pagination from "react-js-pagination";
import Loader from "@/app/forms/Loader";
import { confirmAlert } from "react-confirm-alert"; // Import
import "react-confirm-alert/src/react-confirm-alert.css"; // Import css
import { Translation } from "react-i18next";
import AlertMessage from "@/app/forms/alerts/AlertMessage";
import { connect } from "react-redux";
import { EventAction } from "actions/event/event-action";

class NotRegisteredAttendees extends Component {
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

            checkedItems: new Map()
        };
    }

    componentDidMount() {
        this._isMounted = true;
        this.listing();
        this.readCheckedItems();
        document.body.addEventListener('click', this.handleDocument);
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    readCheckedItems = () => {
        if (this.props.invitation && this.props.invitation.ids !== undefined && this.props.invitation.ids.length > 0) {
            for (let i = 0; i < this.props.invitation.ids.length; i++) {
                const id = this.props.invitation.ids[i];
                this.setState(prevState => ({
                    checkedItems: prevState.checkedItems.set(id, true)
                }));
            }
        }
    }

    handlePageChange = activePage => {
        this.listing(activePage);
    };

    listing = (activePage = 1, loader = false) => {
        this.setState({ preLoader: !loader ? true : false });
        service
            .post(`${process.env.REACT_APP_URL}/attendee/not-registered/${activePage}`, this.state)
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

    handleDocument = e => {
        const element = document.querySelectorAll(".dropdown .btn.active");
        for (let i = 0; i < element.length; i++) {
            const item = element[i];
            item.classList.remove('active');
        }

    }

    handleDeleteElement = id => {
        if (id === "selected" && this.state.checkedItems.size > 0) {
            let ids = [];
            this.state.checkedItems.forEach((value, key, map) => {
                if (value === true) {
                    ids.push(key);
                }
            });
            this.deleteInvitation(id, ids);
        } else if (id !== "selected") {
            this.deleteInvitation(id);
        }
    };

    deleteInvitation(id, ids = []) {
        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {t => (
                            <div className="app-main-popup">
                                <div className="app-header">
                                    <h4>{t("G_DELETE")}</h4>
                                </div>
                                <div className="app-body">
                                    <p>{t("EE_ON_DELETE_ALERT_MSG")}</p>
                                </div>
                                <div className="app-footer">
                                    <button className="btn btn-cancel" onClick={onClose}>
                                        {t("G_CANCEL")}
                                    </button>
                                    <button
                                        className="btn btn-success"
                                        onClick={() => {
                                            onClose();
                                            service
                                                .destroy(
                                                    `${process.env.REACT_APP_URL}/attendee/destroy-invitation/${id}`,
                                                    { ids: ids, module: 'not_registered' }
                                                )
                                                .then(
                                                    response => {
                                                        if (response.success) {
                                                            this.listing(1, false);
                                                        } else {
                                                            this.setState({
                                                                message: response.message,
                                                                success: false
                                                            });
                                                        }
                                                    },
                                                    error => { }
                                                );
                                        }}
                                    >
                                        {t("G_DELETE")}
                                    </button>
                                </div>
                            </div>
                        )}
                    </Translation>
                );
            }
        });
    }

    handleSelectAll = e => {
        const check = e.target.checked;
        const checkitems = document.querySelectorAll(".invitation-records input");
        for (let i = 0; i < checkitems.length; i++) {
            const element = checkitems[i];
            this.setState(prevState => ({
                checkedItems: prevState.checkedItems.set(element.name, check)
            }));
        }
    };

    handleCheckbox = e => {
        const checkitems = document.querySelectorAll(".invitation-records input");
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

    sendAction = (action, invite_type) => e => {
        var actions = [
            "resend_by_email",
            "send_by_email",
            "send_by_sms",
            "send_by_sms_email",
            "move_registration_to_not_attendee_input"
        ];

        let ids = [];

        this.state.checkedItems.forEach((value, key, map) => {
            if (value === true) {
                ids.push(key);
            }
        });

        this.props.dispatch(
            EventAction.invitation({
                ids: ids,
                action: action,
                module: (invite_type === "registration_invite" || invite_type === "reg_all_reinvites" ? "not_registered_invite" : "not_registered_reminder"),
                invite_type: invite_type,
                step: 3
            })
        );

        if (ids.length > 0 && actions.indexOf(action) > -1) {
            if (action === "move_registration_to_not_attendee_input") {
                confirmAlert({
                    customUI: ({ onClose }) => {
                        return (
                            <Translation>
                                {t => (
                                    <div className="app-main-popup">
                                        <div className="app-header">
                                            <h4>{t("EE_ON_DELETE_ALERT")}</h4>
                                        </div>
                                        <div className="app-body">
                                            <p>{t('ATTENDEE_MOVE_SELECTED_ATTENDEE_FROM_NOT_REGISTERED_TO_NOT_ATTENDEE')}</p>
                                        </div>
                                        <div className="app-footer">
                                            <button className="btn btn-cancel" onClick={onClose}>
                                                {t("G_CANCEL")}
                                            </button>
                                            <button
                                                className="btn btn-success"
                                                onClick={() => {
                                                    onClose();
                                                    service
                                                        .put(`${process.env.REACT_APP_URL}/attendee/invitation-process`, {
                                                            ids: ids,
                                                            action: action,
                                                            invite_type: "registration_invite"
                                                        })
                                                        .then(
                                                            response => {
                                                                if (response.success) {
                                                                    if (this._isMounted) {
                                                                        this.setState({
                                                                            checkedItems: new Map()
                                                                        });
                                                                        this.listing(1, false);
                                                                    }
                                                                }
                                                            },
                                                            error => { }
                                                        );
                                                }}
                                            >
                                                {t("G_OK")}
                                            </button>
                                        </div>
                                    </div>
                                )}
                            </Translation>
                        );
                    }
                });
            } else {
                this.props.history.push("/event/invitation/send-invitation");
            }
        } else if (actions.indexOf(action) === -1) {
            confirmAlert({
                customUI: ({ onClose }) => {
                    return (
                        <Translation>
                            {t => (
                                <div className="app-main-popup">
                                    <div className="app-header">
                                        <h4>{t("EE_ON_DELETE_ALERT")}</h4>
                                    </div>
                                    <div className="app-body">
                                        <p>{t("ATTENDEE_CONFIRMATION_INVITATION_TO_ALL_ATTENDEES")}</p>
                                    </div>
                                    <div className="app-footer">
                                        <button className="btn btn-cancel" onClick={onClose}>
                                            {t("G_CANCEL")}
                                        </button>
                                        <button
                                            className="btn btn-success"
                                            onClick={() => {
                                                onClose();
                                                this.props.history.push("/event/invitation/send-invitation");
                                            }}
                                        >
                                            {t("G_OK")}
                                        </button>
                                    </div>
                                </div>
                            )}
                        </Translation>
                    );
                }
            });
        }
    };

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

    export = () => {
        service.download(`${process.env.REACT_APP_URL}/attendee/invitations/export/not-registered`)
            .then(response => {
                response.blob().then(blob => {
                    let url = window.URL.createObjectURL(blob);
                    let a = document.createElement('a');
                    a.href = url;
                    a.download = 'export.csv';
                    a.click();
                });
            });
    }

    render() {
        const Records = ({ data }) => {
            return data.map((data, key) => {
                return (
                    <Translation key={key}>
                        {t => (
                            <div
                                className="row d-flex align-items-center invitation-records"
                                key={key}
                            >
                                <div className="col-3">
                                    <h5>
                                        <label className="checkbox-label">
                                            <input
                                                type="checkbox"
                                                name={data.id.toString()}
                                                checked={this.state.checkedItems.get(
                                                    data.id.toString()
                                                )}
                                                onChange={this.handleCheckbox}
                                            />
                                            <em></em>
                                        </label>
                                        {data.first_name + " " + data.last_name}
                                    </h5>
                                </div>
                                <div className="col-3">
                                    {data.email && <p>Email: {data.email}</p>}
                                </div>
                                <div className="col-4">
                                    {data.phone && <p> {data.phone}</p>}
                                </div>
                                <div className="col-2">
                                    <ul className="panel-actions">
                                        <li>
                                            <span onClick={() => this.handleEditElement(key)}>
                                                <i className="icons">
                                                    <Img src={require("img/ico-edit.svg")} />
                                                </i>
                                            </span>
                                        </li>
                                        <li>
                                            <span onClick={() => this.handleDeleteElement(data.id)}>
                                                <i className="icons">
                                                    <Img src={require("img/ico-delete.svg")} />
                                                </i>
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        )}
                    </Translation>
                );
            });
        };

        const selected_attendees_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size;

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
                                        <h1 className="section-title ">
                                            {t("ATTENDEE_NOT_REGISTERED_ATTENDEES")}
                                        </h1>
                                        <div className="d-flex">
                                        {this.state.attendees.length > 0 && (
                                            <div className="new-right-header">
                                                <button
                                                    onClick={() =>
                                                        this.export()
                                                    }
                                                    className="btn btn-import-csv"
                                                    >
                                                    {t("G_EXPORT")}
                                                </button>
                                            </div>
                                        )}
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
                                            <React.Fragment>
                                                <header className="row header-invitations d-flex align-items-center">
                                                    <div className="col-6 d-flex align-items-center">
                                                        <label>
                                                            <input
                                                                id="selectall"
                                                                onChange={this.handleSelectAll.bind(this)}
                                                                type="checkbox"
                                                                name="selectall"
                                                            />
                                                            <span>{t("G_SELECT_ALL")}</span>
                                                        </label>
                                                        {selected_attendees_length > 0 && (
                                                            <div className="parctical-button-panel">
                                                                <div className="dropdown">
                                                                    <button
                                                                        onClick={this.handleDropdown.bind(this)}
                                                                        className="btn"
                                                                    >
                                                                        {t("G_SEND")}
                                                                        <i className="material-icons">
                                                                            keyboard_arrow_down
                                                                        </i>
                                                                    </button>
                                                                    <div className="dropdown-menu">
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "resend_by_email",
                                                                                "registration_invite"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_RESEND_INVITATION_WITH_EMAIL")}
                                                                        </button>
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "send_by_email",
                                                                                "registration_invite_reminder"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_SEND_REMINDER_EMAIL")}
                                                                        </button>
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "send_by_sms",
                                                                                "registration_invite_reminder"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_SEND_REMINDER_SMS")}
                                                                        </button>
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "send_by_sms_email",
                                                                                "registration_invite_reminder"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_SEND_REMINDER_EMAIL_SMS")}
                                                                        </button>
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "move_registration_to_not_attendee_input",
                                                                                "registration_invite_reminder"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_MOVE_ATTENDEES_NOT_REGISTERED_TO_NOT_ATTENDING")}
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                    <div className="col-6 d-flex align-items-center justify-content-end">
                                                        <div className="parctical-button-panel">
                                                            <div className="dropdown">
                                                                <button
                                                                    onClick={this.handleDropdown.bind(this)}
                                                                    className="btn"
                                                                >
                                                                    {t("G_SEND_TO_ALL")}
                                                                    <i className="material-icons">
                                                                        keyboard_arrow_down
                                                                        </i>
                                                                </button>
                                                                <div className="dropdown-menu">
                                                                    <button
                                                                        onClick={this.sendAction(
                                                                            "send_by_email_all",
                                                                            "reg_all_reinvites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_RESEND_INVITATION_WITH_EMAIL")}
                                                                    </button>
                                                                    <button
                                                                        onClick={this.sendAction(
                                                                            "send_by_email_all",
                                                                            "all_reinvites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_REMINDER_INVITATION_WITH_EMAIL")}
                                                                    </button>
                                                                    <button
                                                                        onClick={this.sendAction(
                                                                            "send_by_sms_all",
                                                                            "all_reinvites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_REMINDER_INVITATION_WITH_SMS")}
                                                                    </button>
                                                                    <button
                                                                        onClick={this.sendAction(
                                                                            "send_by_email_sms_all",
                                                                            "all_reinvites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_REMINDER_INVITATION_WITH_EMAIL_SMS")}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div className="parctical-button-panel">
                                                            <div className="dropdown">
                                                                <button
                                                                    onClick={this.handleDropdown.bind(this)}
                                                                    className="btn"
                                                                >
                                                                    {t("G_ACTION")}
                                                                    <i className="material-icons">
                                                                        keyboard_arrow_down
                                                                    </i>
                                                                </button>
                                                                <div className="dropdown-menu">
                                                                    <button
                                                                        className="dropdown-item"
                                                                        onClick={() =>
                                                                            this.handleDeleteElement("selected")
                                                                        }
                                                                    >
                                                                        {t("G_DELETE_SELECTED")}
                                                                    </button>
                                                                    <button
                                                                        className="dropdown-item"
                                                                        onClick={() =>
                                                                            this.handleDeleteElement("all")
                                                                        }
                                                                    >
                                                                        {t("G_DELETE_ALL")}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </header>
                                                <div style={{ minHeight: '1px' }} className="hotel-management-records invitation-list">
                                                    <Records data={this.state.attendees} />
                                                </div>
                                            </React.Fragment>
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
                                        <NavLink className="btn btn-prev-step" to={`/event/invitation/report/registration`}><span className="material-icons">
                                            keyboard_backspace</span></NavLink>
                                        <NavLink className="btn btn-next-step" to={`/event/invitation/report/not-attendees-list`}>{t('G_NEXT')}</NavLink>
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
    const { invitation } = state;
    return {
        invitation
    };
}

export default connect(mapStateToProps)(NotRegisteredAttendees);
