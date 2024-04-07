import React, { Component } from "react";
import { NavLink } from 'react-router-dom';
import { service } from "services/service";
import Pagination from "react-js-pagination";
import Loader from "@/app/forms/Loader";
import "react-confirm-alert/src/react-confirm-alert.css"; // Import css
import { Translation } from "react-i18next";
import AlertMessage from "@/app/forms/alerts/AlertMessage";
import { connect } from "react-redux";
import { EventAction } from "actions/event/event-action";
import { confirmAlert } from "react-confirm-alert"; // Import

class AppInvitation extends Component {
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
            .post(`${process.env.REACT_APP_URL}/attendee/app-invitations/${activePage}`, this.state)
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
            "send_by_email",
            "send_by_sms",
            "send_by_sms_email"
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
                module: 'app_invitation_sent',
                invite_type: invite_type,
                step: 3
            })
        );

        if (ids.length > 0 && actions.indexOf(action) > -1) {
            this.props.history.push("/event/invitation/send-invitation");
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
        e.preventDefault();
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
                                    <header className="new-header  clearfix">
                                        <h1
                                            className="section-title"
                                        >
                                            {t("ATTENDEE_APP_INVITATION_SENT")}
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
                                                                                "send_by_email",
                                                                                "app_invite"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_INVITATION_WITH_EMAIL")}
                                                                        </button>
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "send_by_sms",
                                                                                "app_invite"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_INVITATION_WITH_SMS")}
                                                                        </button>
                                                                        <button
                                                                            onClick={this.sendAction(
                                                                                "send_by_sms_email",
                                                                                "app_invite"
                                                                            )}
                                                                            className="dropdown-item"
                                                                        >
                                                                            {t("ATTENDEE_INVITATION_WITH_EMAIL_SMS")}
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
                                                                            "resend_all_invites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_INVITATION_WITH_EMAIL")}
                                                                    </button>
                                                                    <button
                                                                        onClick={this.sendAction(
                                                                            "send_by_sms_all",
                                                                            "resend_all_invites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_INVITATION_WITH_SMS")}
                                                                    </button>
                                                                    <button
                                                                        onClick={this.sendAction(
                                                                            "send_by_email_sms_all",
                                                                            "resend_all_invites"
                                                                        )}
                                                                        className="dropdown-item"
                                                                    >
                                                                        {t("ATTENDEE_INVITATION_WITH_EMAIL_SMS")}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </header>
                                                <div className="hotel-management-records invitation-list">
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
                                        <NavLink className="btn btn-prev-step" to={`/event/invitation/report/registration/settings`}><span className="material-icons">
                                            keyboard_backspace</span></NavLink>
                                        <NavLink className="btn btn-next-step" to={`/event/invitation/report/app-invitation-not-sent`}>{t('G_NEXT')}</NavLink>
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

export default connect(mapStateToProps)(AppInvitation);
