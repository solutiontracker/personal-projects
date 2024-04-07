import React, { Component } from 'react';
import Pagination from "react-js-pagination";
import Loader from '@/app/forms/Loader';
import { service } from 'services/service';
import { Translation } from "react-i18next";
import { confirmAlert } from 'react-confirm-alert'; // Import
import { ReactSVG } from "react-svg";
import { formatString } from 'helpers';

const in_array = require("in_array");

export default class LargeScreen extends Component {

    _isMounted = false;

    constructor(props) {
        super(props);
        this.state = {
            query: "",
            rows: [],
            total: 0,
            from: 0,
            to: 0,
            page: 1,
            limit: 100,
            activePage: 1,
            displayElement: false,
            editElement: false,
            editElementIndex: undefined,
            typing: false,
            typingTimeout: 0,
            sort_by: 'first_name',
            order_by: 'ASC',

            //errors & loading
            preLoader: false,
            success: true,

            checkedItems: this.props.checkedItems,
        };

        this.onSorting = this.onSorting.bind(this);
    }

    componentDidMount() {
        this._isMounted = true;
        this.readCheckedItems();
        this.listing();
    }

    componentDidUpdate(prevProps, prevState) {
        if (prevState.limit !== this.state.limit || (this.state.order_by !== prevState.order_by || this.state.sort_by !== prevState.sort_by)) {
            this.listing();
        }
    }

    componentWillUnmount() {
        this._isMounted = false;
    }

    listing = (activePage = 1, loader = false) => {
        this.setState({ preLoader: !loader ? true : false });
        service
            .post(this.props._url + activePage, this.state)
            .then(
                response => {
                    if (response.success) {
                        if (this._isMounted) {
                            this.setState({
                                rows: response.data.data,
                                activePage: response.data.current_page,
                                total: response.data.total,
                                from: response.data.from,
                                to: response.data.to,
                                editElement: false,
                                displayElement: false,
                                preLoader: false,
                            });
                        }
                    }
                },
                error => { }
            );
    };

    handlePageChange = activePage => {
        this.listing(activePage);
    };

    handleDeleteElement = id => {
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
    };

    deleteRecords(id, ids = []) {
        const selected_attendees_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size;

        confirmAlert({
            customUI: ({ onClose }) => {
                return (
                    <Translation>
                        {t => (
                            <div className="app-main-popup">
                                <div className="app-header">
                                    <h4>{(in_array(id, ['selected', 'all']) ? t("ATTENDEE_DELETE_ALL_GUESTS") : t("G_DELETE"))}</h4>
                                </div>
                                <div className="app-body">
                                    <p>
                                        {in_array(id, ['selected', 'all']) ? (
                                            <React.Fragment>
                                                {id === 'all' ? (
                                                    <React.Fragment>
                                                        {
                                                            (() => {
                                                                if (in_array(this.props.module, ["add_reg"]))
                                                                    return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.props.modules.add_reg)}`
                                                                else if (in_array(this.props.module, ["not_registered_invite", "not_registered_reminder"]))
                                                                    return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.props.modules.not_registered)}`
                                                                else if (this.props.module === "app_invitation_not_sent")
                                                                    return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.props.modules.app_invitations_not_sent)}`
                                                                else if (this.props.module === "app_invitation_sent")
                                                                    return `${formatString(t('ATTENDEE_INVITATION_DELETE_CONFIRMATION'), this.props.modules.app_invitations)}`
                                                            })()
                                                        }
                                                    </React.Fragment>
                                                ) : (
                                                        formatString(t('ATTENDEE_INVITATION_DELETE_SELECTED_CONFIRMATION'), selected_attendees_length)
                                                    )}
                                            </React.Fragment>
                                        ) : (
                                                t('EE_ON_DELETE_ALERT_MSG')
                                            )}
                                    </p>
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
                                                    `${this.props.destroyUrl}/${id}`,
                                                    { ids: ids, module: (this.props.module === "add_reg" ? 'add_reg' : 'not_registered') }
                                                )
                                                .then(
                                                    response => {
                                                        if (response.success) {
                                                            this.listing(1, false);
                                                        } else {
                                                            this.setState({
                                                                preLoader: false,
                                                                message: response.message,
                                                                success: false
                                                            });
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

    handleEditElement = index => {
        this.setState({
            editElement: true,
            editElementIndex: index,
            displayElement: false
        });
    };

    handleCancel = () => {
        this.setState({
            editElement: false,
            editElementIndex: undefined,
            displayElement: false
        });
    };

    handleAddElement = () => {
        this.setState({
            displayElement: true
        });
    };

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

    handleLimit = (limit) => e => {
        this.setState(prevState => ({
            limit: limit
        }));
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

    onSorting(event) {
        this.setState({
            order_by: event.target.attributes.getNamedItem('data-order').value,
            sort_by: event.target.attributes.getNamedItem('data-sort').value,
        });
    }

    render() {

        const Records = ({ data }) => {
            return data.map((data, key) => {
                return (
                    <Translation key={key}>
                        {t => (
                            <React.Fragment>
                                <div
                                    className={`row d-flex align-items-center invitation-records ${this.state.checkedItems.get(data.id.toString()) ? 'check' : ''}`}
                                    key={key}
                                >
                                    <div className="col-3">
                                        <h5>
                                            <label className={`checkbox-label`}>
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
                                            {data.first_name}
                                        </h5>
                                    </div>
                                    <div className="col-3">
                                        <p>{data.last_name ? data.last_name : '-'}</p>
                                    </div>
                                    <div className="col-2 col-phone">
                                        <p>{data.phone ? data.phone : '-'}</p>
                                    </div>
                                    <div className="col-2">
                                        <p>{data.email ? data.email : '-'}</p>
                                    </div>
                                    <div className="col-2 col-last">
                                        {in_array(this.props.module, ["not_registered_invite", "not_registered_reminder", "add_reg"]) && (
                                            <ul className="panel-actions">
                                                {in_array(this.props.module, ["add_reg"]) && (
                                                    <li>
                                                        <span onClick={() => this.handleEditElement(key)}>
                                                            <i className="icons">
                                                                <ReactSVG wrapper="span" src={require("img/ico-edit-gray.svg")} />
                                                            </i>
                                                        </span>
                                                    </li>
                                                )}
                                                <li>
                                                    <span onClick={() => this.handleDeleteElement(data.id)}>
                                                        <i className="icons">
                                                            <ReactSVG wrapper="span" src={require("img/ico-delete-gray.svg")} />
                                                        </i>
                                                    </span>
                                                </li>
                                            </ul>
                                        )}
                                    </div>
                                    {this.state.editElement &&
                                        this.state.editElementIndex === key && React.cloneElement(this.props.children, { listing: this.listing, editdata: data, editdataindex: key, datacancel: this.handleCancel, editElement: this.state.editElement })}
                                </div>
                            </React.Fragment>
                        )}
                    </Translation>
                );
            });
        };

        const selected_rows_length = new Map(
            [...this.state.checkedItems]
                .filter(([k, v]) => v === true)
        ).size;

        return (
            <Translation>
                {t => (
                    <div className="wrapper-import-file-wrapper">
                        <div className="wrapper-import-file inline-popup-records">
                            <div style={{ height: '100%', display: 'flex', flexDirection: 'column' }}>
                                <div className="top-popuparea">
                                    <h1
                                        style={{ whiteSpace: "nowrap" }}
                                        className="section-title"
                                    >
                                        {this.props.topHeading}
                                    </h1>
                                    <h4 className="component-heading">{this.props.bottomHeading}</h4>
                                    {this.state.preLoader && <Loader />}
                                    {!this.state.preLoader && (
                                        <React.Fragment>
                                            {this.state.rows.length > 0 &&
                                                <div style={{ marginTop: '20px', marginBottom: 0 }} className="row d-flex align-items-center">
                                                    <div className="col-6">
                                                        <div style={{ marginTop: '0', marginBottom: 0 }} className="new-header">
                                                            <input
                                                                style={{ width: '100%', maxWidth: '390px' }}
                                                                value={this.state.query}
                                                                name="query"
                                                                type="text"
                                                                placeholder={t("G_SEARCH")}
                                                                onChange={this.onFieldChange.bind(this)}
                                                            />
                                                        </div>
                                                    </div>
                                                    <div className="col-6">
                                                        <div className="panel-right-table d-flex justify-content-end">
                                                            <div className="parctical-button-panel">
                                                                <div className="dropdown">
                                                                    <button
                                                                        onClick={this.handleDropdown.bind(this)}
                                                                        className="btn"
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
                                            }
                                            <div
                                                style={{ paddingTop: "10px" }}
                                                className="attendee-management-section"
                                            >
                                                {this.state.rows.length > 0 ? (
                                                    <React.Fragment>
                                                        <div
                                                            style={{ minHeight: "1px", paddingTop: 0 }}
                                                            className="hotel-management-records attendee-records-template invitation-list"
                                                        >
                                                            <header className="header-records row d-flex">
                                                                <div className="col-3 d-flex">
                                                                    <div className="header-invitations">
                                                                        <label>
                                                                            <input
                                                                                id="selectall"
                                                                                onChange={this.handleSelectAll.bind(this)}
                                                                                type="checkbox"
                                                                                name="selectall"
                                                                            />
                                                                            <span style={{ height: '21px', paddingLeft: '21px', marginLeft: 0 }}></span>
                                                                        </label>
                                                                        <div style={{ marginLeft: 0 }} className="parctical-button-panel">
                                                                            <div className="dropdown">
                                                                                <button
                                                                                    onClick={this.handleDropdown.bind(this)}
                                                                                    className="btn"
                                                                                >
                                                                                    <i className="material-icons">
                                                                                        keyboard_arrow_down
                                                       </i>
                                                                                </button>
                                                                                <div className="dropdown-menu">
                                                                                    {selected_rows_length > 0 && (
                                                                                        <button
                                                                                            className="dropdown-item"
                                                                                            onClick={() =>
                                                                                                this.handleDeleteElement("selected")
                                                                                            }
                                                                                        >
                                                                                            {t("G_DELETE_SELECTED")}
                                                                                        </button>
                                                                                    )}
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
                                                                <div className="col-2 col-phone">
                                                                    <strong>{t('ATTENDEE_PHONE')}</strong>
                                                                </div>
                                                                <div className="col-2">
                                                                    <strong>{t('ATTENDEE_EMAIL')} </strong>
                                                                    <i data-order={this.state.order_by === "ASC" ? "DESC" : "ASC"} data-sort="email" onClick={this.onSorting} className="material-icons">
                                                                        {(this.state.order_by === "ASC" && this.state.sort_by === "email" ? "keyboard_arrow_down" : (this.state.order_by === "DESC" && this.state.sort_by === "email" ? "keyboard_arrow_up" : "unfold_more"))}
                                                                    </i>
                                                                </div>
                                                                <div className="col-2 text-right col-last">
                                                                    <span className="total-counter">
                                                                        {`${this.state.rows.length} / ${this.state.total} ${t('ATTENDEE_GUESTS')}`}
                                                                    </span>
                                                                </div>
                                                            </header>
                                                            <Records data={this.state.rows} />
                                                        </div>
                                                        <span className="total-counter">
                                                            {`${this.state.from} ${this.state.to} ${t('G_OF')} ${this.state.total} (${t('G_SELECTED')} ${selected_rows_length})`}
                                                        </span>
                                                    </React.Fragment>
                                                ) : (
                                                        ""
                                                    )}
                                                {this.state.displayElement ? (
                                                    React.cloneElement(this.props.children, { listing: this.listing, datacancel: this.handleCancel })
                                                ) : (
                                                        ""
                                                    )}
                                                {this.state.total > this.state.limit ? (
                                                    <React.Fragment>
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
                                                    </React.Fragment>
                                                ) : (
                                                        ""
                                                    )}
                                            </div>
                                        </React.Fragment>
                                    )}
                                </div>
                                <div className="bottom-component-panel clearfix">
                                    <button className="btn btn-cancel" onClick={this.props.popup}>{t('G_CANCEL')}</button>
                                    <button className="btn btn-save-next" onClick={this.props.popup}>{t('G_SAVE_CLOSE')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </Translation>
        );
    }
}